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
use Illuminate\Support\Str;

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
        // Increase PHP execution time limit
        set_time_limit(600); // 10 minutes
        
        // Increase memory limit
        ini_set('memory_limit', '512M');
        
        Log::info('Starting product import', [
            'import_id' => $this->import->id,
            'user_id' => $this->import->user_id,
        ]);
        
        parent::__construct($import, $columnMap, $options);
    }

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255']),
            ImportColumn::make('image')
                ->rules(['nullable', 'string']),
            ImportColumn::make('slug')
                ->rules(['nullable', 'string']),
            ImportColumn::make('gallery')
                ->rules(['nullable', 'string']),
            ImportColumn::make('short_description')
                ->rules(['nullable', 'string']),
            ImportColumn::make('description')
                ->rules(['nullable', 'string']),
            ImportColumn::make('category')
                ->rules(['nullable', 'string']),
            ImportColumn::make('price')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer', 'min:0']),
            ImportColumn::make('new_price')
                ->numeric()
                ->rules(['nullable', 'integer', 'min:0']),
            ImportColumn::make('is_popular')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('count')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer', 'min:0']),
            ImportColumn::make('synonims')
                ->rules(['nullable', 'string'])
        ];
    }

    public function getJobQueue(): ?string
    {
        return 'imports';
    }

    public function resolveRecord(): ?Product
    {
        
        try {
            if (empty($this->data['name'])) {
                Log::warning('Пустое название продукта', ['row' => $this->data]);
                return null;
            }

            if (!isset($this->data['price'])) {
                Log::warning('Отсутствует цена', ['product' => $this->data['name']]);
                return null;
            }

            $product = Product::where('name', $this->data['name'])->first();

            if (!$product) {
                $product = new Product();
                var_dump('new product');
            } else {
                var_dump('old product');
            }

            
            $product->forceFill([
                'name' => $this->data['name'],
                'slug' => Str::slug($this->data['slug'] ?? $this->data['name']),
                'image' => $this->data['image'] ?? null,
                'gallery' => $this->data['gallery'] ?? null,
                'short_description' => $this->data['short_description'] ?? null,
                'description' => $this->data['description'] ?? null,
                'price' => $this->data['price'],
                'new_price' => $this->data['new_price'] ?? null,
                'is_popular' => $this->data['is_popular'] ?? false,
                'count' => $this->data['count'],
                'synonims' => $this->data['synonims'] ?? null,
            ]);

            $product->save();

            if (!empty($this->data['category'])) {
                $category = ProductCategory::firstOrCreate([
                    'title' => $this->data['category'],
                    'icon' => 'fas-table-list',
                    'is_visible' => true
                ]);
                $product->categories()->sync([$category->id]);
            }
            
            return $product;
        } catch (\Exception $e) {
            Log::error('Ошибка импорта', [
                'product' => $this->data['name'] ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function fillRecord(): void
    {
        try {
            if (!$this->record) {
                throw new \Exception('Отсутствует запись для заполнения');
            }

            // Определяем разрешенные поля из базы данных
            $allowedFields = [
                'name', 'image', 'slug', 'gallery', 'short_description', 'description', 'category', 'price', 'new_price', 'is_popular', 'count', 'synonims'
            ];

            

            foreach ($this->data as $field => $value) {
                // dump(['field' => $field, 'value' => $value]);
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

                    }
                } else if ($field === 'image' && !empty($value)) {
                    $imageId = $this->processImage($value);
                    if ($imageId) {
                        $this->record->image = $imageId;
                    }
                } else if ($field === 'category' && !empty($value)) {
                    $category = ProductCategory::firstOrCreate(['title' => $this->data['category'],  'icon' => 'fas-table-list', 'is_visible' => true]);
                    
                    $this->record->categories()->attach($category->id);
                    // dump($category);
                } else {
                    $this->record->$field = $value;
                }
            }

        } catch (\Exception $e) {
            throw $e;
        }
    }

    protected function beforeValidate(): void
    {
        $this->import->update([
            'status' => 'processing'
        ]);
    }

    protected function afterValidate(): void
    {

    }

    protected function beforeSave(): void
    {

    }

    protected function afterSave(): void
    {

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
}
