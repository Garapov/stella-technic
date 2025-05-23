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
use Exception;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductImporter extends Importer
{
    use InteractsWithQueue, Queueable;

    protected static ?string $model = Product::class;
    protected int $maxAttempts = 3;
    protected int $retryDelay = 500;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make("uuid")
                ->requiredMapping()
                ->numeric()
                ->fillRecordUsing(function (
                    ?Product $record,
                    ?string $state,
                    ProductImporter $importer
                ) {
                    // Проверяем, есть ли uuid у записи и совпадает ли он с импортируемым значением
                    if (isset($record->uuid) && $record->uuid == $state) return;

                    // Проверяем, существует ли другая запись с таким UUID
                    $existingWithUuid = Product::where("uuid", $state)
                        ->where("id", "!=", $record->id)
                        ->exists();

                    if ($existingWithUuid) return;

                    // Обновляем UUID только если он не существует у других записей
                    try {
                        DB::table("products")
                            ->where("id", $record->id)
                            ->update(["uuid" => $state]);

                        // Обновляем запись в памяти для поддержания согласованности
                        $record->uuid = $state;

                    } catch (Exception $e) {
                        Log::error("Ошибка обновления UUID", [
                            "product_id" => $record->id,
                            "uuid" => $state,
                            "error" => $e->getMessage(),
                        ]);
                    }
                })
                ->example("asd667a6d-asdd77887-77as87d7-787a8sd78"),
            ImportColumn::make("name")

                ->requiredMapping()
                ->example("Название товара")
                ->rules(["required", "string"]),
            ImportColumn::make("image")
                ->fillRecordUsing(function (
                    ?Product $record,
                    ?string $state,
                    ProductImporter $importer
                ) {
                    $record->update([
                        "image" => static::processImageStatic(
                            $state,
                            $importer
                        ),
                    ]);
                })
                ->example(
                    "https://stella-tech.ru/assets/images/products/83/km-rps-veni-24.png"
                )
                ->rules(["required", "url"]),
            ImportColumn::make("slug")
                ->fillRecordUsing(function (
                    ?Product $record,
                    ?string $state,
                    ProductImporter $importer
                ) {
                    // Если значение пустое, пропускаем
                    if (blank($state)) {
                        return;
                    }

                    // Проверяем, есть ли slug у записи и совпадает ли он с импортируемым значением
                    if (isset($record->slug) && $record->slug == $state) return;

                    // Если slug не указан, генерируем его из названия
                    $slugValue = $state ?: Str::slug($record->name);

                    // Проверяем, существует ли другая запись с таким Slug
                    $existingWithSlug = Product::where("slug", $slugValue)
                        ->where("id", "!=", $record->id)
                        ->exists();

                    if ($existingWithSlug) {
                        // Если существует другая запись с таким Slug, генерируем уникальный
                        $baseSlug = $slugValue;
                        $counter = 1;
                        $slugValue = $baseSlug;

                        while (
                            Product::where("slug", $slugValue)
                                ->where("id", "!=", $record->id)
                                ->exists()
                        ) {
                            $slugValue = $baseSlug . "-" . $counter++;
                        }
                    }

                    // Обновляем Slug только если он не существует у других записей
                    try {
                        DB::table("products")
                            ->where("id", $record->id)
                            ->update(["slug" => $slugValue]);

                        // Обновляем запись в памяти для поддержания согласованности
                        $record->slug = $slugValue;
                    } catch (Exception $e) {
                        Log::error("Ошибка обновления Slug", [
                            "product_id" => $record->id,
                            "slug" => $slugValue,
                            "error" => $e->getMessage(),
                        ]);
                    }
                })
                ->example("nazvaniye-tovara")
                ->rules(["string", "nullable"]),
            ImportColumn::make("gallery")
                ->fillRecordUsing(function (
                    ?Product $record,
                    ?string $state,
                    ProductImporter $importer
                ) {
                    if (blank($state)) {
                        return null;
                    }

                    $gallery_ids = [];

                    foreach (explode("|", $state) as $image) {
                        $imageId = static::processImageStatic(
                            $image,
                            $importer
                        );
                        if ($imageId) {
                            $gallery_ids[] = $imageId;
                        }
                    }
                    $record->update([
                        "gallery" => $gallery_ids ?? null,
                    ]);
                })
                ->example(
                    "https://stella-tech.ru/assets/images/products/83/km-rps-veni-24.png|https://stella-tech.ru/assets/images/products/83/km-rps-veni-24.png|https://stella-tech.ru/assets/images/products/83/km-rps-veni-24.png"
                ),
            ImportColumn::make("short_description")
                ->example("Короткое описание в несколько строк для товара")
                ->rules(["string", "nullable"]),
            ImportColumn::make("description")->example(
                "Описание товара <div>в котором много текста</div><strong> и где можно использовать HTML</strong>"
            ),
            ImportColumn::make("categories")
                ->fillRecordUsing(function (
                    ?Product $record,
                    ?string $state,
                    ProductImporter $importer
                ) {
                    if (blank($state) || !$record->id) {
                        return;
                    }
                    try {
                        $data = json_decode($state);
                        $record->categories()->sync([]);
                        static::createCategoriesTreeStatic(
                            $data,
                            $importer,
                            $record
                        );
                    } catch (Exception $e) {
                        throw new RowImportFailedException($e->getMessage());
                    }
                })
                ->guess(["categories", "parents", "kategorii"])
                ->example(
                    '{"name":"Мебельные","image": "https://stella-tech.ru/connectors/system/phpthumb.php?src=menu_images/mebelnye.jpg&source=2","parent": {"name": "Колеса Tellure Rota","image": "https://stella-tech.ru/connectors/system/phpthumb.php?src=menu_images/tellure.jpg&source=2","parent": {"name": "Колеса и колесные опоры","image": "https://stella-tech.ru/connectors/system/phpthumb.php?src=menu_images/kolesa-prew.png&source=2","parent": null}}}'
                )
                ->rules(["required", "json"]),
            ImportColumn::make("price")
                ->requiredMapping()
                ->numeric()
                ->example("1000")
                ->rules(["required"]),
            ImportColumn::make("new_price")
                ->castStateUsing(function (?string $state) {
                    if (blank($state) || $state < 1) {
                        return null;
                    }

                    return $state;
                })
                ->numeric()
                ->example("800"),
            ImportColumn::make("count")
                ->requiredMapping()
                ->numeric()
                ->example("100")
                ->rules(["required"]),
            ImportColumn::make("parameters")
                ->fillRecordUsing(function (
                    ?Product $record,
                    ?string $state,
                    ProductImporter $importer
                ) {
                    if (blank($state) || !$record->id) {
                        return;
                    }

                    try {
                        $data = json_decode($state, true);
                        $record->paramItems()->sync([]);

                        // Глобальный массив для хранения созданных paramItems и их соответствия названиям параметров и значениям
                        // Это будет использоваться при создании вариаций
                        $GLOBALS["paramItemsMapping"] = [];

                        foreach ($data as $paramData) {
                            // Создаем или находим параметр
                            $productParam = ProductParam::updateOrCreate(
                                ["name" => $paramData["name"]],
                                [
                                    "type" =>
                                        $paramData["type"] ?? "checkboxes",
                                    "allow_filtering" =>
                                        $paramData["allow_filtering"] ?? true,
                                    "show_on_preview" =>
                                        $paramData["show_on_preview"] ?? false,
                                ]
                            );

                            foreach ($paramData["values"] as $valueData) {
                                // Создаем или находим значение параметра
                                $paramItem = ProductParamItem::updateOrCreate(
                                    [
                                        "product_param_id" => $productParam->id,
                                        "value" => (string) $valueData["value"],
                                    ],
                                    [
                                        "title" =>
                                            $valueData["title"] ??
                                            (string) $valueData["value"],
                                    ]
                                );

                                // Привязываем значение параметра к товару
                                $record->paramItems()->attach($paramItem->id);

                                // Сохраняем mapping для использования при создании вариаций
                                $GLOBALS["paramItemsMapping"][
                                    $paramData["name"]
                                ][(string) $valueData["value"]] =
                                    $paramItem->id;
                            }
                        }
                    } catch (Exception $e) {
                        throw new RowImportFailedException($e->getMessage());
                    }
                })
                ->rules(["required", "json"])
                ->example(
                    '[{"name": "Грузоподъемность","type": "number","values": [{"value": 1500,"title": "1500 кг"},{"value": 300,"title": "300 кг"},{"value": 800,"title": "800 кг"}]},{"name": "Высота подъема","type": "number","values": [{"value": 3000,"title": "3000 мм"},{"value": 1500,"title": "1500 мм"}]}]'
                ),
            ImportColumn::make("variations")
                ->fillRecordUsing(function (
                    ?Product $record,
                    ?string $state,
                    ProductImporter $importer
                ) {
                    if (blank($state) || !$record->id) {
                        return;
                    }

                    try {
                        $variationsData = json_decode($state, true);
                        $links = [];
                        $existingVariantSlugs = [];

                        // Результаты обработки
                        $results = [
                            "success" => 0,
                            "errors" => [],
                            "created" => [],
                            "updated" => [],
                        ];

                        foreach ($variationsData as $variation) {
                            // Проверка обязательных полей
                            $requiredFields = ["image", "price", "paramItems"];
                            $hasAllRequired = true;

                            foreach ($requiredFields as $field) {
                                if (empty($variation[$field])) {
                                    $results["errors"][] =
                                        "Отсутствует обязательное поле '{$field}' в вариации SKU: " .
                                        ($variation["sku"] ?? "неизвестный");
                                    $hasAllRequired = false;
                                    break;
                                }
                            }

                            if (!$hasAllRequired) {
                                continue;
                            }
                            $variantName = $record->name;

                            // Обработка paramItems - основных параметров вариации
                            if (!empty($variation["paramItems"])) {
                                $rowIds = [];

                                foreach (
                                    $variation["paramItems"]
                                    as $paramItem
                                ) {
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
                                            "type" =>
                                                $paramItem["type"] ??
                                                "checkboxes",
                                        ]
                                    );

                                    // Создаем или находим значение параметра
                                    $paramItemModel = ProductParamItem::firstOrCreate(
                                        [
                                            "product_param_id" =>
                                                $productParam->id,
                                            "value" =>
                                                (string) $paramItem["value"],
                                        ],
                                        [
                                            "title" =>
                                                $paramItem["name"] ??
                                                (string) $paramItem["value"],
                                        ]
                                    );

                                    $rowIds[] = $paramItemModel->id;

                                    $variantName .= " {$paramItemModel->title}";
                                }
                            }

                            $slug =
                                $variation["slug"] ?? Str::slug($variantName);

                            // Поиск существующей вариации
                            $variant = ProductVariant::withTrashed()
                                ->where("slug", $variation["sku"])
                                ->first();
                            $isUpdate = !empty($variant);

                            // Обрабатываем изображение
                            $imageId = null;
                            if (!empty($variation["image"])) {
                                $imageId = static::processImageStatic(
                                    $variation["image"],
                                    $importer
                                );
                            }

                            // Обрабатываем галерею
                            $galleryIds = [];
                            if (!empty($variation["gallery"])) {
                                foreach (
                                    explode("|", $variation["gallery"])
                                    as $image
                                ) {
                                    $galleryImageId = static::processImageStatic(
                                        $image,
                                        $importer
                                    );
                                    if ($galleryImageId) {
                                        $galleryIds[] = $galleryImageId;
                                    }
                                }
                            }
                            // Данные для создания/обновления вариации
                            $variantData = [
                                "product_id" => $record->id,
                                "name" => $variantName,
                                "slug" => $slug,
                                "sku" => $variation["sku"],
                                "price" => $variation["price"],
                                "new_price" => $variation["new_price"] ?? null,
                                "image" => $imageId ?? $record->image,
                                "gallery" => !empty($galleryIds)
                                    ? $galleryIds
                                    : null,
                                "short_description" =>
                                    $variation["short_description"] ??
                                    $record->short_description,
                                "description" =>
                                    $variation["description"] ??
                                    $record->description,
                                "is_popular" =>
                                    $variation["is_popular"] ?? false,
                                "count" =>
                                    $variation["count"] ?? $record->count,
                                "synonims" =>
                                    $variation["synonims"] ?? $record->synonims,
                            ];

                            // Создаем или обновляем вариацию
                            if ($isUpdate) {
                                if ($variant->trashed()) {
                                    $variant->restore();
                                }

                                $variant->update($variantData);
                                $results["updated"][] = $variation["sku"];
                            } else {
                                $variant = ProductVariant::create($variantData);
                                $results["created"][] = $variation["sku"];
                            }

                            $results["success"]++;
                            $existingVariantSlugs[] = $slug;

                            if (!empty($rowIds)) {
                                $variant->paramItems()->sync($rowIds);

                                // Добавляем комбинацию для links
                                $links[] = ["row" => $rowIds];
                            }

                            // Обработка дополнительных параметров вариации
                            if (!empty($variation["params"])) {
                                $additionalParams = [];

                                foreach ($variation["params"] as $param) {
                                    if (
                                        empty($param["param"]) ||
                                        !isset($param["name"]) ||
                                        !isset($param["value"])
                                    ) {
                                        continue;
                                    }

                                    // Проверяем существует ли параметр, если нет - создаем
                                    $productParam = ProductParam::firstOrCreate(
                                        [
                                            "name" => $param["param"],
                                        ],
                                        [
                                            "type" =>
                                                $param["type"] ?? "checkboxes",
                                        ]
                                    );

                                    // Создаем или находим значение параметра
                                    $paramItemModel = ProductParamItem::firstOrCreate(
                                        [
                                            "product_param_id" =>
                                                $productParam->id,
                                            "value" => $param["value"],
                                        ],
                                        [
                                            "title" =>
                                                $param["name"] ??
                                                $param["value"],
                                        ]
                                    );

                                    $additionalParams[] = $paramItemModel->id;
                                }

                                // Добавляем дополнительные параметры к вариации, не заменяя существующие
                                if (!empty($additionalParams)) {
                                    $variant
                                        ->parametrs()
                                        ->sync(array_unique($additionalParams));
                                }
                            }
                        }

                        // Обновляем поле links в модели товара
                        if (!empty($links)) {
                            $record->update(["links" => $links]);
                        }

                        // Удаляем вариации, которых нет в импорте
                        if (!empty($existingVariantSlugs)) {
                            ProductVariant::where("product_id", $record->id)
                                ->whereNotIn("slug", $existingVariantSlugs)
                                ->delete();
                        }
                    } catch (Exception $e) {
                        Log::error("Ошибка импорта вариаций", [
                            "product_id" => $record->id,
                            "message" => $e->getMessage(),
                            "trace" => $e->getTraceAsString(),
                        ]);
                        throw new RowImportFailedException($e->getMessage());
                    }
                })
                ->rules(["nullable", "json"]),
            ImportColumn::make("synonims")->example(
                "тут может быть какой то текст|который может быть разделен любым символом|и будет учавствовать в поиске"
            ),
        ];
    }

    public function getJobQueue(): ?string
    {
        return "imports";
    }

    public function afterSave(): void
    {
        dump("afterSave complete");

        // $param_items = $this->record->paramItems->pluck('id')->toArray();

        // // dump($param_items);
        // // Get active variants
        // $activeVariants = ProductVariant::where('product_id', $this->record->id)
        // ->pluck('product_param_item_id')
        // ->toArray();

        // // Get deleted variants
        // $deletedVariants = ProductVariant::onlyTrashed()
        // ->where('product_id', $this->record->id)
        // ->pluck('product_param_item_id')
        // ->toArray();

        // // Delete variants that are not in paramItems anymore
        // ProductVariant::where('product_id', $this->record->id)
        // ->whereIn('product_param_item_id', array_diff($activeVariants, $param_items))
        // ->delete();
        // foreach ($this->record->paramItems as $paramItem) {
        //     // If variant exists but was deleted - restore it
        //     if (in_array($paramItem->id, $deletedVariants)) {
        //         ProductVariant::onlyTrashed()
        //             ->where('product_id', $this->record->id)
        //             ->where('product_param_item_id', $paramItem->id)
        //             ->restore();
        //     }
        //     // If variant never existed - create it
        //     elseif (!in_array($paramItem->id, $activeVariants)) {
        //         ProductVariant::create([
        //             'product_id' => $this->record->id,
        //             'product_param_item_id' => $paramItem->id,
        //             'name' => $this->record->name . ' ' . $paramItem->title,
        //             'price' => $this->record->price,
        //             'new_price' => $this->record->new_price,
        //             'image' => $this->record->image
        //         ]);
        //     }
        // }

        // dump($this->record->id);
        // dump('===================================================');
    }

    public function resolveRecord(): ?Product
    {
        // Log::info('resolveRecord start', ['name' => $this->data['name']]);
        if (empty($this->data["name"])) {
            return null;
        }
        // dump($this->data['name']);

        $product = Product::where("name", $this->data["name"])->first();

        if ($product) {
            // Log::info('resolveRecord: found existing product', ['id' => $product->id]);
            return $product;
        }

        $this->import->update([
            "created_rows" => $this->import->created_rows + 1,
        ]);

        // Log::info('resolveRecord: creating new product');
        return Product::create([
            "name" => $this->data["name"],
        ]);
    }

    public function fillRecord(): void
    {
        dump("fillRecord start");
        // Log::info('fillRecord start', ['data' => $this->getCachedColumns()]);

        foreach ($this->getCachedColumns() as $column) {
            // Log::info('fillRecord column', ['column' => $column]);
            try {
                $columnName = $column->getName();

                if (blank($this->columnMap[$columnName] ?? null)) {
                    continue;
                }

                if (!array_key_exists($columnName, $this->data)) {
                    continue;
                }

                $state = $this->data[$columnName];

                if (blank($state) && $column->isBlankStateIgnored()) {
                    continue;
                }

                $column->fillRecord($state);
            } catch (Exception $e) {
                Log::error("fillRecord error", [
                    "message" => $e->getMessage(),
                    "column_name" => $columnName,
                ]);
            }
        }
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
        dump("beforeValidate start");
        // Log::info('beforeValidate start', ['row_data' => $this->data]);
        $this->import->update([
            "status" => "processing",
        ]);
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
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $import->update([
            "status" => "completed",
        ]);

        return "Импорт товаров завершен. Успешно импортировано: {$import->successful_rows} записей.";
    }

    protected function createProductParams(
        $data,
        ProductImporter $importer,
        ?Product $record
    ) {
        foreach ($data as $key => $param) {
            // Создаем или находим параметр
            $product_param = ProductParam::updateOrCreate(
                ["name" => $param->name],
                [
                    "type" => $param->type ?? "text",
                    "allow_filtering" => $param->allow_filtering ?? true,
                    "show_on_preview" => $param->show_on_preview ?? false,
                ]
            );
            foreach ($param->values as $value) {
                // Создаем или находим значение параметра
                $param_item = ProductParamItem::updateOrCreate(
                    [
                        "product_param_id" => $product_param->id,
                        "value" => $value->value,
                    ],
                    [
                        "title" => $value->title ?? $value->value,
                    ]
                );

                dump(
                    "param item: " .
                        $product_param->name .
                        " " .
                        $param_item->title
                );

                $record->paramItems()->attach($param_item->id);
            }
        }
    }
    protected static function createProductParamsStatic(
        $data,
        ProductImporter $importer,
        ?Product $record
    ) {
        $importer->createProductParams($data, $importer, $record);
    }

    protected function createCategoriesTree(
        $category,
        ProductImporter $importer,
        ?Product $record
    ): ?ProductCategory {
        $categiry_model = ProductCategory::updateOrCreate(
            [
                "title" => $category->name,
            ],
            [
                "icon" => "fas-box-archive",
                "image" => $category->image
                    ? static::storeImageFromUrlStatic($category->image)
                    : null,
                "is_visible" => true,
                "parent_id" => $category->parent
                    ? $importer->createCategoriesTree(
                        $category->parent,
                        $importer,
                        $record
                    )->id
                    : -1,
            ]
        );

        $record->categories()->attach($categiry_model->id);

        return $categiry_model;
    }

    protected static function createCategoriesTreeStatic(
        $category,
        ProductImporter $importer,
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

        $image = Storage::disk(config("filesystems.default"))->put(
            "categories/",
            $uploadedFile
        );
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

    protected static function processImageStatic(
        string $imageUrl,
        ProductImporter $importer
    ): ?int {
        return $importer->processImage($imageUrl);
    }
}
