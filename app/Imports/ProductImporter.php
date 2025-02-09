<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductCategory;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Carbon\CarbonInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Str;

class ProductImporter extends Importer
{
    use InteractsWithQueue, Queueable;

    protected static ?string $model = Product::class;
    protected int $maxAttempts = 3;
    protected int $retryDelay = 500;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->example('Название товара'),
            ImportColumn::make('image')
                ->castStateUsing(function (?string $state, ProductImporter $importer) {
                    return blank($state) ? null : static::processImageStatic($state, $importer);
                })
                ->example('https://stella-tech.ru/assets/images/products/83/km-rps-veni-24.png'),
            ImportColumn::make('slug')
                ->example('nazvaniye-tovara'),
            ImportColumn::make('gallery')
                ->castStateUsing(function (?string $state, ProductImporter $importer) {
                    if (blank($state)) return null;

                    $gallery_ids = [];
        
                    foreach (explode('|', $state) as $image) {
                        $imageId = static::processImageStatic($image, $importer);
                        if ($imageId) {
                            $gallery_ids[] = $imageId;
                        }
                    }
                    return $gallery_ids ?? null;
                })
                ->example('https://stella-tech.ru/assets/images/products/83/km-rps-veni-24.png|https://stella-tech.ru/assets/images/products/83/km-rps-veni-24.png|https://stella-tech.ru/assets/images/products/83/km-rps-veni-24.png'),
            ImportColumn::make('short_description')
                ->example('Короткое описание в несколько строк для товара'),
            ImportColumn::make('description')
                ->example('Описание товара <div>в котором много текста</div><strong> и где можно использовать HTML</strong>'),
            ImportColumn::make('categories')
                ->fillRecordUsing(function (Product $record, string $state) {
                    // $record->sku = strtoupper($state);
                    if (blank($state) || !$record->id) return;
                    dump($state);
                    try {
                        $category_names = explode('|', $state);
                        $categories = [];
                        $category_ids = [];


                        foreach ($category_names as &$title) {
                            $category = ProductCategory::firstOrCreate([
                                'title' => $title,
                            ], [
                                'icon' => 'fas-box-archive',
                                'is_visible' => true
                            ]);
                            $categories[] = $category;
                            $category_ids[] = $category->id;
                        }
                        // [3, 2, 1]
                        array_unshift($category_ids, -1);
                        unset($category_ids[count($category_ids) - 1]);

                        // Log::info('category_ids', ['category_ids' => $category_ids]);

                        foreach ($categories as $index => $category) {

                            $category->update([
                                'parent_id' => $category_ids[$index]
                            ]);
                        }
                        // Log::info('fillRecordUsing', ['state' => $state]);
                        $record->categories()->sync($category_ids);
                    } catch (Exception $e) {

                        Log::error('fillRecordUsing error', ['message' => $e->getMessage(), 'state' => $state, 'product' => $record]);
                        // return null;
                    }
                    
                })
                ->example('Складское оборудование|Штабелеры|Электрические самоходные штабелеры'),
            ImportColumn::make('price')
                ->requiredMapping()
                ->numeric()
                ->example('1000'),
            ImportColumn::make('new_price')
                ->numeric()
                ->example('800'),
            ImportColumn::make('count')
                ->requiredMapping()
                ->numeric()
                ->example('100'),
            ImportColumn::make('synonims')
                ->example('тут может быть какой то текст|который может быть разделен любым символом|и будет учавствовать в поиске')
        ];
    }
    

    public function getJobQueue(): ?string
    {
        return 'imports';
    }

    public function afterSave(): void
    {
        // Log::info('afterSave complete', [
        //     'record_id' => $this->record->id,
        //     'import_id' => $this->import->id,
        //     'processed_rows' => $this->import->processed_rows,
        //     'successful_rows' => $this->import->successful_rows
        // ]);
    }

    public function resolveRecord(): ?Product
    {
        // Log::info('resolveRecord start', ['name' => $this->data['name']]);
        if (empty($this->data['name'])) return null;
        
        $product = Product::where('name', $this->data['name'])->first();

        if ($product) {
            // Log::info('resolveRecord: found existing product', ['id' => $product->id]);
            return $product;
        }

        $this->import->update([
            'created_rows' => $this->import->created_rows + 1
        ]);

        // Log::info('resolveRecord: creating new product');
        return new Product([
            'name' => $this->data['name'],
        ]);
    }

    public function fillRecord(): void
    {
        // Log::info('fillRecord start', ['data' => $this->getCachedColumns()]);

        foreach ($this->getCachedColumns() as $column) {
            // Log::info('fillRecord column', ['column' => $column]);
            try {
                $columnName = $column->getName();

                if (blank($this->columnMap[$columnName] ?? null)) {
                    continue;
                }

                if (! array_key_exists($columnName, $this->data)) {
                    continue;
                }

                $state = $this->data[$columnName];

                if (blank($state) && $column->isBlankStateIgnored()) {
                    continue;
                }

                $column->fillRecord($state);
            } catch (Exception $e) {
                Log::error('fillRecord error', [
                    'message' => $e->getMessage(),
                    'column_name' => $columnName
                ]);
            }
        }
    }

    public function saveRecord(): void
    {
        // Log::info('saveRecord start', [
        //     'product_name' => $this->record->name,
        //     'data' => $this->data
        // ]);
        
        try {
            $this->record->save();
            // Log::info('saveRecord: product saved successfully', ['id' => $this->record->id]);
            
            $this->import->update([
                'processed_rows' => $this->import->processed_rows + 1,
                'successful_rows' => $this->import->successful_rows + 1
            ]);
        } catch (\Exception $e) {
            Log::error('saveRecord error', [
                'message' => $e->getMessage(),
                'product_name' => $this->record->name
            ]);
            
            $this->import->update([
                'processed_rows' => $this->import->processed_rows + 1,
                'failed_rows' => $this->import->failed_rows + 1
            ]);
        }
    }

    public function beforeValidate(): void
    {
        // Log::info('beforeValidate start', ['row_data' => $this->data]);
        $this->import->update([
            'status' => 'processing'
        ]);
    }

    public function afterValidate(): void
    {
        // Log::info('afterValidate', ['validation_passed' => true]);
    }

    public function beforeFill(): void
    {
        // Log::info('beforeFill start');
    }

    public function afterFill(): void
    {
        // Log::info('afterFill complete', ['filled_data' => $this->data]);
    }

    public function beforeSave(): void
    {
        // Log::info('beforeSave start', ['record' => $this->record]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $import->update([
            'status' => 'completed'
        ]);

        return "Импорт товаров завершен. Успешно импортировано: {$import->successful_rows} записей.";
    }

    protected function processImage(string $imageUrl): ?int 
    {
        try {
            $attempt = 1;
            
            while ($attempt <= $this->maxAttempts) {
                try {
                    DB::beginTransaction();
                    
                    $tempDir = storage_path('app/temp');
                    if (!file_exists($tempDir)) {
                        mkdir($tempDir, 0755, true);
                    }

                    $ctx = stream_context_create([
                        'http' => [
                            'timeout' => 10
                        ]
                    ]);

                    $imageContent = @file_get_contents($imageUrl, false, $ctx);
                    if ($imageContent === false) {
                        throw new \Exception("Не удалось загрузить изображение");
                    }

                    $extension = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                    $tempImagePath = $tempDir . '/' . uniqid() . '.' . $extension;
                    
                    if (!file_put_contents($tempImagePath, $imageContent)) {
                        throw new \Exception("Не удалось сохранить временный файл");
                    }

                    $uploadedFile = new \Illuminate\Http\UploadedFile(
                        $tempImagePath,
                        basename($imageUrl),
                        mime_content_type($tempImagePath),
                        null,
                        true
                    );

                    $image = \App\Models\Image::upload(
                        $uploadedFile,
                        'public',
                        [
                            'title' => json_encode(['Product Image']),
                            'alt' => json_encode(['Product Image']),
                        ]
                    );

                    DB::commit();
                    return $image->id;

                } catch (QueryException $e) {
                    DB::rollBack();
                    if (str_contains($e->getMessage(), 'database is locked')) {
                        if ($attempt >= $this->maxAttempts) {
                            throw $e;
                        }
                        usleep($this->retryDelay * 1000);
                        $attempt++;
                        continue;
                    }
                    throw $e;
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
            }
        } catch (\Exception $e) {
            return null;
        } finally {
            if (isset($tempImagePath) && file_exists($tempImagePath)) {
                @unlink($tempImagePath);
            }
        }
    }

    protected static function processImageStatic(string $imageUrl, ProductImporter $importer): ?int 
    {
        return $importer->processImage($imageUrl);
    }

    public function processGallery(string $state): array
    {
        if (blank($state)) return [];
        
        $gallery_ids = [];

        foreach (explode('|', $state) as $image) {
            $imageId = $this->processImage($image);
            if ($imageId) {
                $gallery_ids[] = $imageId;
            }
        }
    
        return $gallery_ids;
    }
}
