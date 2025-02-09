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
    // use InteractsWithQueue, Queueable;

    protected static ?string $model = Product::class;
    protected int $maxAttempts = 3;
    protected int $retryDelay = 500;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping(),
            ImportColumn::make('image')
                ->castStateUsing(function (?string $state, ProductImporter $importer) {
                    return blank($state) ? null : static::processImageStatic($state, $importer);
                }),
            ImportColumn::make('slug'),
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
                }),
            ImportColumn::make('short_description'),
            ImportColumn::make('description'),
            // ImportColumn::make('category')
            //     ->castStateUsing(function (?string $state, ProductImporter $importer) {
            //         return null;
            //     }),
            ImportColumn::make('price')
                ->requiredMapping()
                ->numeric(),
            ImportColumn::make('new_price')
                ->numeric(),
            ImportColumn::make('count')
                ->requiredMapping()
                ->numeric(),
            ImportColumn::make('synonims')
        ];
    }

    public function getJobQueue(): ?string
    {
        return 'imports';
    }

    public function resolveRecord(): ?Product
    {

        $product = Product::where('name', $this->data['name'])->first();

        if ($product) {
            return $product;
        }

        $this->import->update([
            'created_rows' => $this->import->created_rows + 1
        ]);

        return Product::create([
             // Update existing records, matching them by `$this->data['column_name']`
            'name' => $this->data['name'],
        ]);
    }

    public function saveRecord(): void
    {
        
        dump('saveRecord');
        $this->record->save();
    }


    public function beforeValidate(): void
    {
        $this->import->update([
            'status' => 'processing'
        ]);
        dump('beforeValidate');
    }

    public function afterValidate(): void
    {
        dump('afterValidate');
    }

    public function beforeFill(): void
    {
        dump('beforeFill');
    }

    public function afterFill(): void
    {
        dump('afterFill');
    }

    public function beforeSave(): void
    {
        dump('beforeSave');
        
    }

    public function afterSave(): void
    {
        dump('afterSave');
        $this->import->update([
            'processed_rows' => $this->import->processed_rows + 1
        ]);
    }



    public static function getCompletedNotificationBody(Import $import): string
    {
        $import->update([
            'status' => 'completed',
            'failed_rows' => $import->getFailedRowsCount()
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
