<?php

namespace App\Filament\Imports;

use App\Models\ProductCategory;
use App\Models\ProductVariant;
use App\Models\Product;
use App\Models\ProductParam;
use App\Models\ProductParamItem;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Forms\Components\Checkbox;
use Illuminate\Support\Facades\Log;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Illuminate\Support\Facades\Storage;

class ProductVariantImporter extends Importer
{
    protected static ?string $model = ProductVariant::class;
    protected $maxAttempts = 3;
    protected int $retryDelay = 500;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('product_id')
                ->label('Родительский товар')
                ->requiredMapping()
                ->guess(['product_id', 'product', 'parent'])
                ->castStateUsing(function ($state, ProductVariantImporter $importer): ?int {
                    $data = json_decode($state, true);
                    // Log::info('$data', ['data' => $data]);
                    $requiredFields = ['name', 'image'];
                    foreach ($requiredFields as $field) {
                        if (empty($data[$field])) {
                            throw new RowImportFailedException("Отсутствует обязательное поле '{$field}' в поле родительского товара.");
                            break;
                        }
                    }
                    $data['image'] = static::processImageStatic(
                        $data['image'],
                        $importer
                    );
                    
                    $product = Product::firstOrCreate($data);

                    if (isset($data['categories']) && !empty($data['categories'])) {
                        
                        try {
                            $product->categories()->sync([]);
                            foreach($data['categories'] as $category) {
                                static::createCategoriesTreeStatic(
                                    $category,
                                    $importer,
                                    $product
                                );
                            }
                        } catch (\Exception $e) {
                            throw new RowImportFailedException($e->getMessage());
                        }
                    }

                    return $product->id;
                })
                ->rules(['required', 'json']),
            ImportColumn::make('price')
                ->numeric()
                ->ignoreBlankState()
                ->rules(['required', 'integer']),
            ImportColumn::make('new_price')
                ->numeric()
                ->ignoreBlankState()
                ->rules(['integer']),
            ImportColumn::make('image')
                ->fillRecordUsing(function ($state, ProductVariantImporter $importer, $record): void  {
                    $record->image = static::processImageStatic(
                        $state,
                        $importer
                    );
                    Log::info('$record->image', ['data' => $record->image]);
                })
                ->requiredMapping()
                ->rules(['required', 'url']),
            ImportColumn::make('sku')
                ->label('SKU'),
            ImportColumn::make('short_description')
                ->ignoreBlankState(),
            ImportColumn::make('description')
                ->ignoreBlankState(),
            ImportColumn::make('is_popular')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('count')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('paramItems')
                ->fillRecordUsing(function ($state, ProductVariantImporter $importer, $record): void  {
                    $data = \json_decode($state, true);
                    $rowIds = [];

                    $variationName = $record->product->name;

                    Log::info('param items', ['data' => $data, 'state' => $state]);

                    foreach ($data as $paramItem) {
                        if (
                            empty($paramItem["param"]) ||
                            !isset($paramItem["name"]) ||
                            !isset($paramItem["value"])
                        ) {
                            continue;
                        }

                        // Проверяем существует ли параметр, если нет - создаем
                        $productParam = ProductParam::firstOrCreate(
                            [
                                "name" => $paramItem["param"],
                            ],
                            [
                                "type" => $paramItem["type"] ?? "checkboxes",
                            ]
                        );

                        // Создаем или находим значение параметра
                        $paramItemModel = ProductParamItem::firstOrCreate(
                            [
                                "product_param_id" => $productParam->id,
                                "value" => (string) $paramItem["value"],
                            ],
                            [
                                "title" => $paramItem["name"] ?? (string) $paramItem["value"],
                            ]
                        );

                        $rowIds[] = $paramItemModel->id;
                    }
                    $record->paramItems->sync($rowIds);
                })
                ->rules(['required', 'json']),
            ImportColumn::make('synonims'),
            ImportColumn::make('gallery')
                ->array(',')
                ->rules(['array'])
                ->nestedRecursiveRules(['url']),
        ];
    }

    public function getJobQueue(): ?string
    {
        return 'imports';
    }

    public function resolveRecord(): ?ProductVariant
    {
        if ($this->options['updateExisting'] ?? false) {
            return ProductVariant::firstOrNew([
                // Update existing records, matching them by `$this->data['column_name']`
                'sku' => $this->data['sku'],
            ]);
        } else {
            $product = ProductVariant::where('sku', $this->data['sku'])->first();
            
            if ($product) {
                throw new RowImportFailedException("Найден товар с артикулом '{$this->data['sku']}', но обновление товаров отключено.");
            }
        }

        return new ProductVariant();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $import->update([
            "status" => "completed",
        ]);

        $body = 'Импорт товаров завершен. Успешно импортировано: ' . number_format($import->successful_rows);

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= '. Ошибок: ' . number_format($failedRowsCount);
        }

        return $body;
    }

    public static function getOptionsFormComponents(): array
    {
        return [
            Checkbox::make('updateExisting')
                ->label('Обновлять существующие'),
        ];
    }


    public function afterSave(): void
    {
        dump("afterSave complete");
    }


    public function saveRecord(): void
    {
        dump("saveRecord start");
        // Log::info('saveRecord start', [
        //     'product_name' => $this->record->name,
        //     'data' => $this->data
        // ]);

        try {
            $this->record->save();
            // Log::info('saveRecord: product saved successfully', ['id' => $this->record->id]);

            $this->import->update([
                "processed_rows" => $this->import->processed_rows + 1,
                "successful_rows" => $this->import->successful_rows + 1,
            ]);
        } catch (\Exception $e) {
            Log::error("saveRecord error", [
                "message" => $e->getMessage(),
                "product_name" => $this->record->name,
            ]);

            $this->import->update([
                "processed_rows" => $this->import->processed_rows + 1,
                "failed_rows" => $this->import->failed_rows + 1,
            ]);
        }
    }

    public function beforeValidate(): void
    {
        
        // Log::info('beforeValidate start', ['row_data' => $this->data]);
        $this->import->update([
            "status" => "processing",
        ]);
        dump("beforeValidate start");
    }

    public function afterValidate(): void
    {
        dump("afterValidate");
        // Log::info('afterValidate', ['validation_passed' => true]);
    }

    public function beforeFill(): void
    {
        dump("beforeFill start");
        // Log::info('beforeFill start');
    }

    public function afterFill(): void
    {
        
        dump("afterFill complete");
        // Log::info('afterFill complete', ['filled_data' => $this->data]);
    }

    public function beforeSave(): void
    {
        dump("beforeSave start");
        // Log::info('beforeSave start', ['record' => $this->record]);
        
        // Проверяем, что запись и родительский продукт существуют
        // if ($this->record && $this->record->product) {
        //     // Получаем ID параметров вариации
        //     $paramItemIds = $this->record->paramItems()->pluck('product_param_items.id')->toArray();
            
        //     if (!empty($paramItemIds)) {
        //         // Получаем текущие links родительского продукта
        //         $links = $this->record->product->links ?? [];
                
        //         // Добавляем новую комбинацию параметров
        //         $links[] = ["row" => $paramItemIds];
                
        //         // Обновляем links родительского продукта
        //         $this->record->product->update(['links' => $links]);
                
        //         Log::info('Updated product links in beforeSave', [
        //             'product_id' => $this->record->product->id,
        //             'variant_id' => $this->record->id,
        //             'paramItemIds' => $paramItemIds,
        //             'links' => $links
        //         ]);
        //     }
            
        //     // Обновляем название вариации на основе названия продукта и параметров
        //     $variantName = $this->record->product->name;
            
        //     foreach ($this->record->paramItems as $paramItem) {
        //         $variantName .= " {$paramItem->title}";
        //     }
            
        //     $this->record->name = $variantName;
            
        //     Log::info('Updated variant name in beforeSave', [
        //         'variant_id' => $this->record->id,
        //         'name' => $variantName
        //     ]);
        // } else {
        //     Log::error('Record or product is null in beforeSave', [
        //         'record' => $this->record ? 'exists' : 'null',
        //         'product' => ($this->record && $this->record->product) ? 'exists' : 'null'
        //     ]);
        // }
    }

    protected function createCategoriesTree(
        $category,
        ProductVariantImporter $importer,
        ?Product $record
    ): ?ProductCategory {

        $category_model = ProductCategory::updateOrCreate(
            [
                "title" => $category['name'],
            ],
            [
                "icon" => "fas-box-archive",
                "image" => $category['image']
                    ? static::storeImageFromUrlStatic($category['image'])
                    : null,
                "is_visible" => true,
                "parent_id" => $category['parent']
                    ? $importer->createCategoriesTree(
                        $category['parent'],
                        $importer,
                        $record
                    )->id
                    : -1,
            ]
        );

        $record->categories()->attach($category_model->id);

        return $category_model;
    }

    protected static function createCategoriesTreeStatic(
        $category,
        ProductVariantImporter $importer,
        ?Product $record
    ): ?ProductCategory {
        return $importer->createCategoriesTree($category, $importer, $record);
    }

    protected static function storeImageFromUrlStatic($imageUrl)
    {
        $tempDir = storage_path("app/temp");
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $ctx = stream_context_create([
            "http" => [
                "timeout" => 10,
            ],
        ]);

        $imageContent = @file_get_contents($imageUrl, false, $ctx);

        if ($imageContent === false) {
            throw new \Exception("Не удалось загрузить изображение");
        }

        $extension =
            pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?:
            "jpg";
        $tempImagePath = $tempDir . "/" . uniqid() . "." . $extension;

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

        $image = Storage::disk("public")->put("categories/", $uploadedFile);
        @unlink($tempImagePath);

        dump("image: " . $image);
        // Возвращаем путь до файла
        return $image;
    }

    protected function processImage(string $imageUrl): ?int
    {
        dump("processImage start");
        try {
            $attempt = 1;

            while ($attempt <= $this->maxAttempts) {
                try {
                    $tempDir = storage_path("app/temp");
                    if (!file_exists($tempDir)) {
                        mkdir($tempDir, 0755, true);
                    }

                    $ctx = stream_context_create([
                        "http" => [
                            "timeout" => 10,
                        ],
                    ]);

                    $imageContent = @file_get_contents($imageUrl, false, $ctx);
                    if ($imageContent === false) {
                        throw new \Exception(
                            "Не удалось загрузить изображение"
                        );
                    }

                    $extension =
                        pathinfo(
                            parse_url($imageUrl, PHP_URL_PATH),
                            PATHINFO_EXTENSION
                        ) ?:
                        "jpg";
                    $tempImagePath =
                        $tempDir . "/" . uniqid() . "." . $extension;

                    if (!file_put_contents($tempImagePath, $imageContent)) {
                        throw new \Exception(
                            "Не удалось сохранить временный файл"
                        );
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
                        "public",
                        [
                            "title" => json_encode(["Product Image"]),
                            "alt" => json_encode(["Product Image"]),
                        ]
                    );
                    @unlink($tempImagePath);

                    return $image->id;
                } catch (\Illuminate\Database\QueryException $e) {
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
        
        return null;
    }

    protected static function processImageStatic(
        string $imageUrl,
        ProductVariantImporter $importer
    ): ?int {
        return $importer->processImage($imageUrl);
    }
    
}
