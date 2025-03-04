<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductParam;
use App\Models\ProductParamItem;
use App\Models\ProductVariant;
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
            ImportColumn::make('uuid')
                ->requiredMapping()
                ->numeric()
                ->example('asd667a6d-asdd77887-77as87d7-787a8sd78'),
            ImportColumn::make('name')
            
                ->requiredMapping()
                ->example('Название товара')
                ->rules(['required', 'string']),
            ImportColumn::make('image')
                ->fillRecordUsing(function (?Product $record, ?string $state, ProductImporter $importer) {
                    $record->update([
                        'image' => static::processImageStatic($state, $importer)
                    ]);
                })
                ->example('https://stella-tech.ru/assets/images/products/83/km-rps-veni-24.png')
                ->rules(['required', 'url']),
            ImportColumn::make('slug')
                ->example('nazvaniye-tovara')
                ->rules(['string', 'nullable']),
            ImportColumn::make('gallery')
                ->fillRecordUsing(function (?Product $record, ?string $state, ProductImporter $importer) {
                    if (blank($state)) return null;

                    $gallery_ids = [];
        
                    foreach (explode('|', $state) as $image) {
                        $imageId = static::processImageStatic($image, $importer);
                        if ($imageId) {
                            $gallery_ids[] = $imageId;
                        }
                    }
                    $record->update([
                        'gallery' => $gallery_ids ?? null
                    ]);
                })
                ->example('https://stella-tech.ru/assets/images/products/83/km-rps-veni-24.png|https://stella-tech.ru/assets/images/products/83/km-rps-veni-24.png|https://stella-tech.ru/assets/images/products/83/km-rps-veni-24.png'),
            ImportColumn::make('short_description')
                ->example('Короткое описание в несколько строк для товара')
                ->rules(['string', 'nullable']),
            ImportColumn::make('description')
                ->example('Описание товара <div>в котором много текста</div><strong> и где можно использовать HTML</strong>'),
            ImportColumn::make('categories')
                ->fillRecordUsing(function (?Product $record, ?string $state) {
                    // $record->sku = strtoupper($state);
                    if (blank($state) || !$record->id) return;
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
                ->guess(['categories', 'parents', 'kategorii'])
                ->example('Складское оборудование|Штабелеры|Электрические самоходные штабелеры')
                ->rules(['string', 'required']),
            ImportColumn::make('price')
                ->requiredMapping()
                ->numeric()
                ->example('1000')
                ->rules(['required']),
            ImportColumn::make('new_price')
                ->castStateUsing(function (?string $state) {
                    if (blank($state) || $state < 1 ) return null;

                    return $state;
                })
                ->numeric()
                ->example('800'),
            ImportColumn::make('count')
                ->requiredMapping()
                ->numeric()
                ->example('100')
                ->rules(['required']),
            ImportColumn::make('parameters')
                ->fillRecordUsing(function (?Product $record, ?string $state) {
                    if (blank($state) || !$record->id) return;

                    
                    try {
                        $param_items = [];
                        
                        // Разбиваем строку на отдельные параметры
                        $params = explode('||', $state);
                        
                        foreach ($params as $param) {
                            if (empty($param)) continue;
                            
                            // Разбиваем параметр на пары ключ-значение
                            $pairs = explode(';;', $param);
                            $data = [];
                            
                            foreach ($pairs as $pair) {
                                if (empty($pair)) continue;
                                list($key, $value) = explode('::', $pair);
                                $data[trim($key)] = trim($value);
                            }
                            
                            // Проверяем обязательные поля
                            if (!isset($data['name']) || !isset($data['value'])) {
                                continue;
                            }
                            
                            // Создаем или находим параметр
                            $product_param = ProductParam::firstOrCreate(
                                ['name' => $data['name']],
                                [
                                    'type' => $data['type'] ?? 'text',
                                    'allow_filtering' => $data['allow_filtering'] ?? true
                                ]
                            );
                            
                            // Создаем или находим значение параметра
                            $param_item = ProductParamItem::firstOrCreate(
                                [
                                    'product_param_id' => $product_param->id,
                                    'value' => $data['value']
                                ],
                                [
                                    'title' => $data['title'] ?? $data['value']
                                ]
                            );
                            
                            $param_items[] = $param_item->id;
                        }
                        
                        // Привязываем параметры к продукту
                        if (!empty($param_items)) {
                            $record->paramItems()->sync($param_items);
                        }




                    } catch (\Exception $e) {
                        Log::error('Error processing parameters', [
                            'error' => $e->getMessage(),
                            'state' => $state,
                            'product_id' => $record->id
                        ]);
                    }
                })
                ->example('name::Грузоподъемность;;type::number;;value::1500;;title::1500 кг||name::Высота подъема;;type::number;;value::3000;;title::3000 мм'),
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
        dump('afterSave complete');

        $param_items = $this->record->paramItems->pluck('id')->toArray();

        // dump($param_items);
        // Get active variants
        $activeVariants = ProductVariant::where('product_id', $this->record->id)
        ->pluck('product_param_item_id')
        ->toArray();

        // Get deleted variants
        $deletedVariants = ProductVariant::onlyTrashed()
        ->where('product_id', $this->record->id)
        ->pluck('product_param_item_id')
        ->toArray();

        // Delete variants that are not in paramItems anymore
        ProductVariant::where('product_id', $this->record->id)
        ->whereIn('product_param_item_id', array_diff($activeVariants, $param_items))
        ->delete();

        foreach ($this->record->paramItems as $paramItem) {
            // If variant exists but was deleted - restore it
            if (in_array($paramItem->id, $deletedVariants)) {
                ProductVariant::onlyTrashed()
                    ->where('product_id', $this->record->id)
                    ->where('product_param_item_id', $paramItem->id)
                    ->restore();
            } 
            // If variant never existed - create it
            elseif (!in_array($paramItem->id, $activeVariants)) {
                ProductVariant::create([
                    'product_id' => $this->record->id,
                    'product_param_item_id' => $paramItem->id,
                    'name' => $this->record->name . ' ' . $paramItem->title,
                    'price' => $this->record->price,
                    'new_price' => $this->record->new_price,
                    'image' => $this->record->image
                ]);
            }
        }


        dump($this->record->id);
        dump('===================================================');
    }

    public function resolveRecord(): ?Product
    {
        // Log::info('resolveRecord start', ['name' => $this->data['name']]);
        if (empty($this->data['name'])) return null;
        // dump($this->data['name']);
        
        $product = Product::where('name', $this->data['name'])->first();

        if ($product) {
            // Log::info('resolveRecord: found existing product', ['id' => $product->id]);
            return $product;
        }

        $this->import->update([
            'created_rows' => $this->import->created_rows + 1
        ]);

        // Log::info('resolveRecord: creating new product');
        return Product::create([
            'name' => $this->data['name'],
        ]);
    }

    public function fillRecord(): void
    {
        dump('fillRecord start');
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
        dump('saveRecord start');
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
        dump('beforeValidate start');
        // Log::info('beforeValidate start', ['row_data' => $this->data]);
        $this->import->update([
            'status' => 'processing'
        ]);
    }

    public function afterValidate(): void
    {
        dump('afterValidate');
        // Log::info('afterValidate', ['validation_passed' => true]);
    }

    public function beforeFill(): void
    {
        dump('beforeFill start');
        // Log::info('beforeFill start');
    }

    public function afterFill(): void
    {
        dump('afterFill complete');
        // Log::info('afterFill complete', ['filled_data' => $this->data]);
    }

    public function beforeSave(): void
    {
        dump('beforeSave start');
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
        dump('processImage start');
        try {
            $attempt = 1;
            
            while ($attempt <= $this->maxAttempts) {
                try {

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

                    return $image->id;

                } catch (QueryException $e) {
                    return null;
                } catch (\Exception $e) {
                    return null;
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
}
