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

    public function setUp(): void
    {
        parent::setUp();
        $this->options = [
            'updateExisting' => true,
        ];
    }

    public static function getOptionsFormComponents(): array
    {
        return [];
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function __construct(Import $import)
    {
        parent::__construct($import);
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
                ->label('Превью товара')
                ->rules(['nullable', 'string']),
        ];
    }

    public function resolveRecord(): ?Product
    {
        try {
            Log::info('Starting to resolve record', ['data' => $this->data]);
            
            // Validate required fields
            $requiredFields = ['name', 'price'];
            foreach ($requiredFields as $field) {
                if (empty($this->data[$field])) {
                    throw new \Exception("Поле '{$field}' обязательно для заполнения");
                }
            }

            $product = Product::where('name', $this->data['name'])->first();
            $isNew = !$product;

            if (!$product) {
                $product = new Product;
            }

            // Process image if present
            if (!empty($this->data['image'])) {
                try {
                    $imageId = $this->processImage($this->data['image']);
                    $this->data['image'] = $imageId;
                    $this->data['gallery'] = json_encode([]);
                } catch (\Exception $e) {
                    Log::error('Error processing image', [
                        'error' => $e->getMessage(),
                        'image_url' => $this->data['image']
                    ]);
                    // Don't fail the whole import if image processing fails
                    $this->data['image'] = null;
                }
            }

            $product->fill($this->data);

            if ($isNew) {
                $this->import->increment('created_rows');
            }

            Log::info('Successfully processed product', [
                'name' => $product->name,
                'id' => $product->id ?? null,
                'action' => $isNew ? 'create' : 'update'
            ]);

            return $product;
        } catch (\Exception $e) {
            Log::error('Error processing product record', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $this->data
            ]);
            throw $e;
        }
    }

    protected function processImage(string $imageUrl): ?int
    {
        Log::info('Processing image', ['url' => $imageUrl]);
        
        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $imageContent = file_get_contents($imageUrl);
        if ($imageContent === false) {
            throw new \Exception("Не удалось загрузить изображение по URL: {$imageUrl}");
        }

        $extension = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'png';
        $tempImagePath = $tempDir.'/'.uniqid().'.'.$extension;
        
        if (!file_put_contents($tempImagePath, $imageContent)) {
            throw new \Exception("Не удалось сохранить временный файл: {$tempImagePath}");
        }

        try {
            $uploadedFile = new \Illuminate\Http\UploadedFile(
                $tempImagePath,
                basename($tempImagePath),
                mime_content_type($tempImagePath),
                null,
                true
            );

            $attributes = [
                'title' => json_encode(['Product Image']),
                'alt' => json_encode(['Product Image']),
            ];

            $image = \App\Models\Image::upload(
                $uploadedFile,
                'public',
                $attributes
            );

            return $image->id;
        } finally {
            // Always clean up the temporary file
            @unlink($tempImagePath);
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return "Импорт товаров успешно завершен! Импортировано записей: {$import->successful_rows}";
    }
}
