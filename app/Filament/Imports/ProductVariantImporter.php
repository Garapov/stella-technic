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

    protected array $paramItemIds = [];
    protected array $additionalParamItemIds = [];

    protected array $steps = [];

    public static function getColumns(): array
    {
        return [
            ImportColumn::make("product_id")
                ->label("Родительский товар")
                ->requiredMapping()
                ->guess(["product_id", "product", "parent"])
                ->castStateUsing(function (
                    $state,
                    ProductVariantImporter $importer,
                    $record
                ) {
                    // Log::info(["data", json_decode($state), $state]);

                    try {
                        $data = json_decode($state, true);
                    } catch (\Exception $e) {
                        throw new RowImportFailedException($e->getMessage());
                    }

                    $requiredFields = ["name", "image", "categories"];

                    foreach ($requiredFields as $field) {
                        if (empty($data[$field])) {
                            throw new RowImportFailedException(
                                "Отсутствует обязательное поле '{$field}' в поле родительского товара."
                            );
                            break;
                        }
                    }
                    try {
                        $data["gallery"] = static::processImageStatic(
                            $data["image"],
                            "products",
                            $importer
                        );
                    } catch (\Exception $e) {
                        Log::error($e->getMessage());
                    }

                    $data["image"] = "/assets/placeholder.svg";

                    $product = Product::where("name", $data["name"])->first();

                    if (!$product) {
                        $product = Product::create($data);
                        // dump("Создается товар: " . $product->name);
                    } else {
                        // dump("Обновляется товар: " . $product->name);
                    }

                    if (
                        isset($data["categories"]) &&
                        !empty($data["categories"])
                    ) {
                        try {
                            $product->categories()->sync([]);
                            sleep(0.3);
                            // Log::info(["cats", $data["categories"]]);
                            foreach ($data["categories"] as $category) {
                                static::createCategoriesTreeStatic(
                                    $category,
                                    $importer,
                                    $product
                                );
                            }
                        } catch (\Exception $e) {
                            throw new RowImportFailedException(
                                $e->getMessage()
                            );
                        }
                    }
                    sleep(0.3);

                    return $product->id;
                })
                ->fillRecordUsing(function (
                    $state,
                    ProductVariantImporter $importer,
                    $record
                ) {
                    Log::warning($state);
                })
                ->rules(["required", "json"]),
            ImportColumn::make("name")
                ->ignoreBlankState()
                ->rules(["nullable"]),
            ImportColumn::make("price")
                ->numeric()
                ->ignoreBlankState()
                ->rules(["required", "integer"]),
            ImportColumn::make("new_price")
                ->numeric()
                ->ignoreBlankState()
                ->rules(["integer", "nullable"]),
            // ImportColumn::make('image')
            //     ->fillRecordUsing(function ($state, ProductVariantImporter $importer, $record): void  {
            //         // $record->image = static::processImageStatic(
            //         //     $state,
            //         //     $importer
            //         // );
            //         $record->image = '/assets/placeholder.svg';
            //     })
            //     ->requiredMapping()
            //     ->rules(['required', 'url']),
            ImportColumn::make("sku")->label("SKU"),
            ImportColumn::make("short_description")->ignoreBlankState(),
            ImportColumn::make("description")->ignoreBlankState(),
            ImportColumn::make("is_popular")
                ->requiredMapping()
                ->boolean()
                ->rules(["boolean"]),
            ImportColumn::make("count")
                ->requiredMapping()
                ->numeric()
                ->rules(["required", "integer"]),
            ImportColumn::make("paramItems")
                ->fillRecordUsing(function (
                    $state,
                    ProductVariantImporter $importer,
                    $record
                ): void {
                    $data = \json_decode($state, true);

                    $variationName = Product::where(
                        "id",
                        $importer->data["product_id"]
                    )->first()->name;
                    $variatinLinks = "";

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
                        sleep(0.3);

                        // Создаем или находим значение параметра
                        $paramItemModel = ProductParamItem::firstOrCreate(
                            [
                                "product_param_id" => $productParam->id,
                                "value" => (string) $paramItem["value"],
                            ],
                            [
                                "title" =>
                                    $paramItem["name"] ??
                                    (string) $paramItem["value"],
                            ]
                        );
                        sleep(0.3);
                        $variationName .= " {$paramItemModel->title}";
                        $importer->paramItemIds[] = $paramItemModel->id;
                        $variatinLinks .= $paramItemModel->id;
                    }

                    $record->name = $variationName;
                    $record->links = $variatinLinks;
                })
                ->rules(["required", "json"]),
            ImportColumn::make("parametrs")
                ->fillRecordUsing(function (
                    $state,
                    ProductVariantImporter $importer,
                    $record
                ): void {
                    $data = \json_decode($state, true);

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
                        sleep(0.3);

                        // Создаем или находим значение параметра
                        $paramItemModel = ProductParamItem::firstOrCreate(
                            [
                                "product_param_id" => $productParam->id,
                                "value" => (string) $paramItem["value"],
                            ],
                            [
                                "title" =>
                                    $paramItem["name"] ??
                                    (string) $paramItem["value"],
                            ]
                        );
                        sleep(0.3);
                        $importer->additionalParamItemIds[] =
                            $paramItemModel->id;
                    }
                })
                ->rules(["required", "json"]),
            ImportColumn::make("synonims")
                ->ignoreBlankState()
                ->fillRecordUsing(function (
                    $state,
                    ProductVariantImporter $importer,
                    $record
                ) {
                    if (blank($state)) {
                        return "";
                    }

                    $record->synonims = $state;
                })
                ->rules(["nullable"]),
            ImportColumn::make("gallery")
                ->array(",")
                ->fillRecordUsing(function (
                    $state,
                    ProductVariantImporter $importer,
                    $record
                ) {
                    if (blank($state)) {
                        return [];
                    }

                    $gallery_ids = [];
                    foreach ($state as $image) {
                        $imageId = static::processImageStatic(
                            $image,
                            "variations",
                            $importer
                        );
                        if ($imageId) {
                            $gallery_ids[] = $imageId;
                        }
                    }
                    $record->gallery = $gallery_ids;
                })
                ->rules(["array"])
                ->nestedRecursiveRules(["url"]),
        ];
    }

    public function getJobQueue(): ?string
    {
        $this->steps[] = "getJobQueue";
        return "imports";
    }

    public function resolveRecord(): ?ProductVariant
    {
        $product = ProductVariant::firstWhere("sku", $this->data["sku"]);

        if ($this->options["updateExisting"] && $product) {

            return $product;
        }

        if ($this->options["updateExisting"] && !$product) {
            $product = new ProductVariant();
            $product->product_id = $this->data["product_id"];
            $product->sku = $this->data["sku"];
            $product->price = 100000;
            $product->name = "Не задано";
            $product->image = "/assets/placeholder.svg";

            return $product;
        }

        if (!$this->options["updateExisting"] && $product) {
            
            throw new RowImportFailedException(
                "Найден товар с артикулом '{$product->sku}', но обновление товаров отключено."
            );
        }

        if (!$this->options["updateExisting"] && !$product) {
            
            $product = new ProductVariant();
            $product->product_id = $this->data["product_id"];
            $product->sku = $this->data["sku"];
            $product->price = 100000;
            $product->name = "Не задано";
            $product->image = "/assets/placeholder.svg";
            
            return $product;
        }

        // if ($product) {
        // }

        // if (!$this->options["updateExisting"] && $product) {
        //     throw new RowImportFailedException(
        //         "Найден товар с артикулом '{$product->sku}', но обновление товаров отключено."
        //     );
        // } else {
        //     $product = new ProductVariant();
        //     $product->product_id = $this->data['product_id'];
        //     $product->sku = $this->data['sku'];
        //     $product->price = 100000;
        //     $product->name = 'Не задано';
        //     $product->image = "/assets/placeholder.svg";
        //     // $product->save();
        // }

        $this->steps[] = "resolveRecord";
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $import->update([
            "status" => "completed",
        ]);

        $body =
            "Импорт товаров завершен. Успешно импортировано: " .
            number_format($import->successful_rows);

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ". Ошибок: " . number_format($failedRowsCount);
        }

        return $body;
    }

    public static function getOptionsFormComponents(): array
    {
        return [
            Checkbox::make("updateExisting")->label("Обновлять существующие"),
        ];
    }

    public function afterSave(): void
    {
        $this->record->paramItems()->sync($this->paramItemIds);
        sleep(0.3);
        $this->record->parametrs()->sync($this->additionalParamItemIds);
        sleep(0.3);
        if ($this->data["name"]) {
            $this->record->update([
                "name" => $this->data["name"],
            ]);
        }
        sleep(0.3);

        // Проверяем текущее значение links в продукте
        $links = $this->record->product->links ?? [];

        // Если links не является массивом, создаем новый массив
        if (!is_array($links)) {
            $links = [];
        }

        // Преобразуем $this->paramItemIds в строку для корректного сравнения
        $currentIds = json_encode($this->paramItemIds);

        // Флаг, указывающий, найдена ли уже такая запись
        $rowExists = false;

        // Проверяем, есть ли уже такая строка в массиве links
        foreach ($links as $row) {
            if (isset($row["row"])) {
                // Преобразуем существующую строку для сравнения
                $existingRow = json_encode($row["row"]);
                if ($existingRow === $currentIds) {
                    $rowExists = true;
                    break;
                }
            }
        }
        sleep(0.3);
        // Если такой записи нет, добавляем её
        if (!$rowExists) {
            $links[] = ["row" => $this->paramItemIds];

            // Обновляем links в продукте
            $this->record->product->update([
                "links" => $links,
            ]);
        }

        $this->paramItemIds = [];
        $this->additionalParamItemIds = [];

        $this->steps[] = "afterSave";
        $this->steps[] = $this->record->sku;
        $this->steps[] = $this->record->name;

        dump($this->steps);
        $this->steps = [];
    }

    public function saveRecord(): void
    {
        try {
            $this->record->save();
            sleep(0.3);
            $this->import->update([
                "processed_rows" => $this->import->processed_rows + 1,
                "successful_rows" => $this->import->successful_rows + 1,
            ]);
        } catch (\Exception $e) {
            $this->import->update([
                "processed_rows" => $this->import->processed_rows + 1,
                "failed_rows" => $this->import->failed_rows + 1,
            ]);
        }

        $this->steps[] = "saveRecord";
    }

    public function beforeValidate(): void
    {
        $this->import->update([
            "status" => "processing",
        ]);
        $this->steps[] = "beforeValidate";
    }

    public function afterValidate(): void
    {
        $this->steps[] = "afterValidate";
    }

    public function beforeFill(): void
    {
        $this->steps[] = "beforeFill";
    }

    public function afterFill(): void
    {
        $this->steps[] = "afterFill";
    }

    public function beforeSave(): void
    {
        $this->record->image = "/assets/placeholder.svg";
        $this->steps[] = "beforeSave";
    }

    protected function createCategoriesTree(
        $category,
        ProductVariantImporter $importer,
        ?Product $record
    ): ?ProductCategory {
        // Log::info($category);

        $category_model = null;

        try {
            $category_model = ProductCategory::updateOrCreate(
                [
                    "title" => $category["name"],
                ],
                [
                    "icon" => "fas-box-archive",
                    "image" => $category["image"]
                        ? static::processImageStatic(
                            $category["image"],
                            "categories",
                            $importer
                        )
                        : null,
                    "is_visible" => true,
                    "parent_id" => $category["parent"]
                        ? $importer->createCategoriesTree(
                            $category["parent"],
                            $importer,
                            $record
                        )->id
                        : -1,
                ]
            );

            $record->categories()->attach($category_model->id);
        } catch (\Exception $e) {
            throw new RowImportFailedException($e->getMessage());
        }
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

        $image = Storage::disk(config("filesystems.default"))->put(
            "categories/",
            $uploadedFile
        );
        @unlink($tempImagePath);

        // Возвращаем путь до файла
        return $image;
    }

    protected function processImage(
        string $imageUrl,
        string $imagePath = "images/"
    ): ?string {
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

                    $filename = $uploadedFile->getClientOriginalName();
                    $path = Storage::disk(
                        config("filesystems.default")
                    )->putFileAs($imagePath, $uploadedFile, $filename);

                    @unlink($tempImagePath);

                    return $path;
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
        string $imagePath,
        ProductVariantImporter $importer
    ): ?string {
        return $importer->processImage($imageUrl, $imagePath);
    }
}
