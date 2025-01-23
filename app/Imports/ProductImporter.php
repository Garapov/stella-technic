<?php

namespace App\Imports;

use App\Models\Product;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Log;

class ProductImporter extends Importer
{
    protected static ?string $model = Product::class;

    protected array $options = [];

    public function __construct(Import $import, array $options = [], ?array $records = [])
    {
        parent::__construct($import, $options, $records);
        Log::channel('daily')->info('ProductImporter: Создан новый экземпляр импортера', [
            'import_id' => $import->id,
            'options' => $options,
            'records_count' => count($records)
        ]);
    }

    public function setUp(): void
    {
        Log::info('ProductImporter: Настройка импорта');
        parent::setUp();
        $this->options = [
            'updateExisting' => true,
        ];
    }

    public static function getOptionsFormComponents(): array
    {
        Log::info('ProductImporter: Получение компонентов формы опций');
        return [];
    }

    public function getOptions(): array
    {
        Log::info('ProductImporter: Получение опций', [
            'options' => $this->options
        ]);
        return $this->options;
    }

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Название товара')
                ->requiredMapping()
                ->rules(['required', 'string']),

            ImportColumn::make('price')
                ->label('Цена')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'numeric', 'min:0']),

            ImportColumn::make('new_price')
                ->label('Новая цена')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0']),

            ImportColumn::make('short_description')
                ->label('Короткое описание')
                ->rules(['nullable', 'string']),

            ImportColumn::make('description')
                ->label('Описание')
                ->rules(['nullable', 'string']),

            ImportColumn::make('image')
                ->label('Изображение')
                ->rules(['nullable', 'string']),
        ];
    }

    public function getJobConnection(): ?string
    {
        return 'database';
    }

    public function getJobQueue(): ?string
    {
        return 'default';
    }

    public function resolveRecord(): ?Product
    {
        $logger = Log::channel('daily');
        $logger->info('ProductImporter: Начало обработки записи');

        try {
            $data = $this->record;
            
            if (empty($data)) {
                $logger->error('ProductImporter: Пустая запись');
                return null;
            }

            $logger->info('ProductImporter: Данные записи', ['data' => $data]);

            // Поиск или создание продукта
            $product = Product::firstOrNew(['name' => $data['name']]);
            $logger->info('ProductImporter: Продукт найден/создан', [
                'product_id' => $product->id,
                'is_new' => !$product->exists,
                'name' => $product->name
            ]);

            // Заполняем все поля из данных
            foreach ($data as $field => $value) {
                $logger->info('ProductImporter: Обработка поля', [
                    'field' => $field,
                    'value' => $value
                ]);

                if ($field === 'price' || $field === 'new_price') {
                    $product->$field = (float) str_replace([' ', ','], ['', '.'], $value);
                } elseif ($field === 'image' && !empty($value)) {
                    try {
                        $imageId = $this->processImage($value);
                        $product->image = $imageId;
                        $logger->info('ProductImporter: Изображение обработано', [
                            'image_id' => $imageId
                        ]);
                    } catch (\Exception $e) {
                        $logger->error('ProductImporter: Ошибка обработки изображения', [
                            'error' => $e->getMessage(),
                            'url' => $value
                        ]);
                    }
                } else {
                    $product->$field = $value;
                }
            }

            $logger->info('ProductImporter: Запись подготовлена', [
                'product' => $product->toArray()
            ]);

            return $product;

        } catch (\Exception $e) {
            $logger->error('ProductImporter: Критическая ошибка', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data ?? null
            ]);
            throw $e;
        }
    }

    protected function processImage(string $imageUrl): ?int
    {
        try {
            $tempDir = storage_path('app/temp');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $imageContent = file_get_contents($imageUrl);
            if ($imageContent === false) {
                throw new \Exception("Не удалось загрузить изображение: {$imageUrl}");
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
        } catch (\Exception $e) {
            Log::error('ProductImporter: Ошибка обработки изображения', [
                'error' => $e->getMessage(),
                'url' => $imageUrl
            ]);
            return null;
        } finally {
            if (isset($tempImagePath) && file_exists($tempImagePath)) {
                @unlink($tempImagePath);
            }
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return "Импорт товаров успешно завершен! Импортировано записей: {$import->successful_rows}";
    }
}
