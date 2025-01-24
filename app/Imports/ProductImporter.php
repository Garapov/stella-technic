<?php

namespace App\Imports;

use App\Models\Product;
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

class ProductImporter extends Importer
{
    use InteractsWithQueue, Queueable;

    protected static ?string $model = Product::class;
    protected int $maxAttempts = 3;
    protected int $retryDelay = 500;

    public function __construct(
        protected Import $import,
        protected array $columnMap,
        protected array $options,
    ) {
        // Увеличиваем время выполнения PHP скрипта
        set_time_limit(600); // 10 минут
        
        // Увеличиваем память
        ini_set('memory_limit', '512M');
        
        // Настройка таймаута SQLite
        DB::statement('PRAGMA busy_timeout = 5000');
        
        parent::__construct($import, $columnMap, $options);
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
                ->rules(['required', 'string'])
                ->example('Товар 1'),

            ImportColumn::make('price')
                ->label('Цена')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'numeric', 'min:0'])
                ->example('100.00'),

            ImportColumn::make('new_price')
                ->label('Новая цена')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0'])
                ->example('90.00'),

            ImportColumn::make('short_description')
                ->label('Короткое описание')
                ->rules(['nullable', 'string'])
                ->example('Краткое описание товара'),

            ImportColumn::make('description')
                ->label('Описание')
                ->rules(['nullable', 'string'])
                ->example('Полное описание товара'),

            ImportColumn::make('image')
                ->label('Изображение')
                ->rules(['nullable', 'string'])
                ->example('https://example.com/image.jpg'),
        ];
    }

    public function getJobConnection(): ?string
    {
        return 'database';
    }

    public function getJobQueue(): ?string
    {
        return 'imports';
    }

    public function getJobTimeout(): ?int 
    {
        return 3600; // 1 час
    }

    public function getJobMaxTries(): ?int
    {
        return 3;
    }

    public function getJobRetryUntil(): ?CarbonInterface
    {
        return Carbon::now()->addHours(2);
    }

    public function getJobMemory(): ?int
    {
        return 512; // 512MB
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

    public function import(): void
    {
        $logger = Log::channel('daily');
        $logger->info('ProductImporter: Начало импорта');

        $logger->info($this->import);
        
        try {
            // Читаем данные из файла напрямую
            $content = file_get_contents($this->import->file_path);
            if ($content === false) {
                throw new \Exception("Не удалось прочитать файл");
            }

            // Определяем кодировку и конвертируем в UTF-8 если нужно
            $encoding = mb_detect_encoding($content, ['UTF-8', 'Windows-1251'], true);
            if ($encoding && $encoding !== 'UTF-8') {
                $content = mb_convert_encoding($content, 'UTF-8', $encoding);
            }

            // Разбираем CSV
            $rows = array_map('str_getcsv', explode("\n", $content));
            $headers = array_shift($rows);

            if (!$headers) {
                throw new \Exception("Не удалось прочитать заголовки CSV");
            }

            $logger->info('ProductImporter: Заголовки CSV', [
                'headers' => $headers
            ]);

            // Формируем записи
            $records = [];
            foreach ($rows as $row) {
                if (count($row) === count($headers)) {
                    $record = array_combine($headers, $row);
                    if ($record && !empty(array_filter($record))) {
                        $records[] = $record;
                    }
                }
            }

            $logger->info('ProductImporter: Прочитаны записи', [
                'records_count' => count($records),
                'first_record' => $records[0] ?? null
            ]);

            // Импортируем каждую запись
            foreach ($records as $record) {
                $this->record = $record;
                
                if ($this->validateRecordData($record)) {
                    $product = $this->resolveRecord();
                    if ($product && $product->save()) {
                        $this->import->increment('successful_rows');
                        $logger->info('ProductImporter: Сохранен продукт', [
                            'product' => $product->toArray()
                        ]);
                    }
                }
            }

        } catch (\Exception $e) {
            $logger->error('ProductImporter: Ошибка импорта', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function resolveRecord(): ?Product
    {
        $logger = Log::channel('daily');
        
        try {
            if (empty($this->data['name'])) {
                $logger->error('ProductImporter: Отсутствует name', [
                    'data' => $this->data
                ]);
                return null;
            }

            if (!isset($this->data['price'])) {
                $logger->error('ProductImporter: Отсутствует price', [
                    'data' => $this->data
                ]);
                return null;
            }

            return Product::firstOrNew(['name' => $this->data['name']]);

        } catch (\Exception $e) {
            $logger->error('ProductImporter: Ошибка в resolveRecord', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $this->data
            ]);
            throw $e;
        }
    }

    public function fillRecord(): void
    {
        $logger = Log::channel('daily');
        
        try {
            if (!$this->record) {
                throw new \Exception('Отсутствует запись для заполнения');
            }

            // Определяем разрешенные поля из базы данных
            $allowedFields = [
                'name', 'price', 'new_price', 'short_description', 
                'description', 'image', 'slug'
            ];

            foreach ($this->data as $field => $value) {
                // Пропускаем поля, которых нет в таблице
                if (!in_array($field, $allowedFields)) {
                    continue;
                }

                // Пропускаем пустые значения для необязательных полей
                if (!in_array($field, ['name', 'price']) && empty($value)) {
                    continue;
                }

                if ($field === 'price' || $field === 'new_price') {
                    $cleanValue = str_replace([' ', ','], ['', '.'], $value);
                    if (is_numeric($cleanValue)) {
                        $this->record->$field = (float) $cleanValue;
                    } else {
                        $logger->warning("ProductImporter: Некорректное значение цены", [
                            'field' => $field,
                            'value' => $value
                        ]);
                    }
                } else if ($field === 'image' && !empty($value)) {
                    $imageId = $this->processImage($value);
                    if ($imageId) {
                        $this->record->image = $imageId;
                    }
                } else {
                    $this->record->$field = $value;
                }
            }

        } catch (\Exception $e) {
            $logger->error('ProductImporter: Ошибка в fillRecord', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $this->data,
                'record' => $this->record ? $this->record->toArray() : null
            ]);
            throw $e;
        }
    }

    protected function beforeValidate(): void
    {
        $logger = Log::channel('daily');
        $logger->info('ProductImporter: Перед валидацией', [
            'data' => $this->data,
            'rules' => $this->getValidationRules(),
        ]);
    }

    protected function afterValidate(): void
    {
        $logger = Log::channel('daily');
        $logger->info('ProductImporter: После валидации', [
            'data' => $this->data
        ]);
    }

    protected function beforeSave(): void
    {
        // Логируем только в случае ошибки
        if (!$this->record || empty($this->record->name) || empty($this->record->price)) {
            $logger = Log::channel('daily');
            $logger->error('ProductImporter: Некорректные данные перед сохранением', [
                'record' => $this->record ? $this->record->toArray() : null
            ]);
        }
    }

    protected function afterSave(): void
    {
        // Логируем только в случае ошибки
        if (!$this->record || !$this->record->exists) {
            $logger = Log::channel('daily');
            $logger->error('ProductImporter: Ошибка после сохранения', [
                'record' => $this->record ? $this->record->toArray() : null,
                'successful_rows' => $this->import->successful_rows
            ]);
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return "Импорт товаров завершен. Успешно импортировано: {$import->successful_rows} записей.";
    }

    public function saveRecord(): void
    {
        $logger = Log::channel('daily');
        $attempt = 1;

        while (true) {
            try {
                DB::beginTransaction();
                
                try {
                    parent::saveRecord();
                    DB::commit();
                    break;
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }

            } catch (QueryException $e) {
                if (str_contains($e->getMessage(), 'database is locked')) {
                    if ($attempt >= $this->maxAttempts) {
                        throw $e;
                    }
                    usleep($this->retryDelay * 1000);
                    $attempt++;
                    continue;
                }
                throw $e;
            }
        }
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
}
