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
        // Сначала вызываем родительский конструктор
        parent::__construct($import, $options, $records);
        
        $logger = Log::channel('daily');
        $logger->info('ProductImporter: Конструктор', [
            'import_id' => $import->id,
            'options' => $options,
            'file_path' => $import->file_path,
            'file_exists' => file_exists($import->file_path ?? ''),
        ]);
    }

    public function setUp(): void
    {
        $logger = Log::channel('daily');
        
        try {
            $logger->info('ProductImporter: Начало настройки импорта', [
                'import_id' => $this->import->id,
                'file_path' => $this->import->file_path,
                'file_exists' => file_exists($this->import->file_path),
                'file_size' => file_exists($this->import->file_path) ? filesize($this->import->file_path) : 0,
                'mime_type' => mime_content_type($this->import->file_path),
                'file_contents' => file_exists($this->import->file_path) ? mb_substr(file_get_contents($this->import->file_path), 0, 1000) : null,
            ]);

            if (!file_exists($this->import->file_path)) {
                $logger->error('ProductImporter: Файл импорта не найден', [
                    'file_path' => $this->import->file_path
                ]);
                throw new \Exception('Файл импорта не найден');
            }

            parent::setUp();
            
            $this->options = [
                'updateExisting' => true,
            ];
            
            // Проверяем формат файла
            $extension = pathinfo($this->import->file_path, PATHINFO_EXTENSION);
            $logger->info('ProductImporter: Формат файла', [
                'extension' => $extension,
                'mime_type' => mime_content_type($this->import->file_path)
            ]);
            
        } catch (\Exception $e) {
            $logger->error('ProductImporter: Ошибка настройки импорта', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
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

    public function validateRecordData($data): bool
    {
        $logger = Log::channel('daily');
        
        if (!is_array($data)) {
            $logger->error('ProductImporter: Неверный формат данных', [
                'type' => gettype($data)
            ]);
            return false;
        }
        
        $requiredFields = ['name', 'price'];
        $missingFields = array_diff($requiredFields, array_keys($data));
        
        if (!empty($missingFields)) {
            $logger->error('ProductImporter: Отсутствуют обязательные поля', [
                'missing_fields' => $missingFields
            ]);
            return false;
        }
        
        return true;
    }

    public function resolveRecord(): ?Product
    {
        $logger = Log::channel('daily');
        
        try {
            if (empty($this->record)) {
                $logger->warning('ProductImporter: Пустая запись', [
                    'record' => $this->record
                ]);
                return null;
            }

            $data = $this->record;
            
            // Создаем или обновляем продукт
            $product = Product::firstOrNew(['name' => $data['name']]);
            
            $logger->info('ProductImporter: Обработка записи', [
                'data' => $data,
                'is_new' => !$product->exists
            ]);

            // Заполняем поля
            foreach ($data as $field => $value) {
                if ($field === 'price') {
                    $product->$field = (float) str_replace([' ', ','], ['', '.'], $value);
                } elseif ($field === 'image' && !empty($value)) {
                    try {
                        $imageId = $this->processImage($value);
                        $product->image = $imageId;
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
                'product_data' => $product->toArray()
            ]);

            return $product;

        } catch (\Exception $e) {
            $logger->error('ProductImporter: Ошибка обработки записи', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $this->record ?? null
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

    public function beforeImport(): void
    {
        $logger = Log::channel('daily');
        $logger->info('ProductImporter: Перед beforeImport()', [
            'import_id' => $this->import->id,
            'records' => $this->records,
            'records_count' => count($this->records ?? []),
            'file_path' => $this->import->file_path,
            'file_exists' => file_exists($this->import->file_path),
            'import_status' => $this->import->status,
            'has_record_property' => property_exists($this, 'record'),
            'record_value' => $this->record ?? null,
        ]);
        
        parent::beforeImport();
        
        $logger->info('ProductImporter: После beforeImport()', [
            'records' => $this->records,
            'records_count' => count($this->records ?? []),
            'has_record_property' => property_exists($this, 'record'),
            'record_value' => $this->record ?? null,
        ]);
    }

    public function afterImport(): void
    {
        parent::afterImport();
        
        $logger = Log::channel('daily');
        $logger->info('ProductImporter: После импорта', [
            'import_id' => $this->import->id,
            'records' => $this->records,
            'records_count' => count($this->records ?? []),
            'successful_rows' => $this->import->successful_rows,
            'import_status' => $this->import->status
        ]);
    }

    // Переопределяем метод для чтения строк
    public function readRows()
    {
        $logger = Log::channel('daily');
        $logger->info('ProductImporter: Начало чтения строк', [
            'file_path' => $this->import->file_path,
            'file_exists' => file_exists($this->import->file_path),
        ]);

        try {
            $records = $this->readCsv();
            
            $logger->info('ProductImporter: Результат чтения строк', [
                'records_count' => count($records),
                'first_record' => $records[0] ?? null
            ]);

            return $records;
        } catch (\Exception $e) {
            $logger->error('ProductImporter: Ошибка чтения строк', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    // Добавляем метод для чтения CSV
    protected function readCsv(): array
    {
        $logger = Log::channel('daily');
        
        try {
            $file = fopen($this->import->file_path, 'r');
            if ($file === false) {
                throw new \Exception("Не удалось открыть файл");
            }

            $headers = fgetcsv($file);
            if ($headers === false) {
                throw new \Exception("Не удалось прочитать заголовки CSV");
            }

            $logger->info('ProductImporter: Заголовки CSV', [
                'headers' => $headers
            ]);

            $records = [];
            while (($row = fgetcsv($file)) !== false) {
                if (count($headers) !== count($row)) {
                    $logger->warning('ProductImporter: Пропущена строка - несоответствие количества колонок', [
                        'headers_count' => count($headers),
                        'row_count' => count($row),
                        'row' => $row
                    ]);
                    continue;
                }

                $record = array_combine($headers, $row);
                $records[] = $record;
            }

            fclose($file);

            $logger->info('ProductImporter: Прочитано записей из CSV', [
                'count' => count($records),
                'first_record' => $records[0] ?? null
            ]);

            return $records;
        } catch (\Exception $e) {
            $logger->error('ProductImporter: Ошибка чтения CSV', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
