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

    protected Import $import;

    public function __construct(Import $import)
    {
        $this->import = $import;
    }

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Название товара')
                ->rules(['required']),

            ImportColumn::make('price')
                ->label('Цена')
                ->numeric()
                ->rules(['nullable']),

            ImportColumn::make('new_price')
                ->label('Новая цена')
                ->numeric()
                ->rules(['nullable']),

            ImportColumn::make('short_description')
                ->label('Короткое описание')
                ->rules(['nullable']),

            ImportColumn::make('description')
                ->label('Описание')
                ->rules(['nullable']),

            ImportColumn::make('image')
                ->label('Превью товара')
                ->rules(['nullable']),
        ];
    }

    public function resolveRecord(): ?Product
    {
        $data = $this->data;

        try {
            if (! isset($data['name'])) {
                throw new \Exception('Отсутствует обязательное поле "name"');
            }

            $product = Product::where('name', $data['name'])->first();
            $isNew = ! $product;

            if (!$product) {
                $product = new Product;
            }

            if (isset($data['image'])) {
                Log::info('Картинка передана: '.$data['image']);
                // Загружаем изображение по ссылке во временную директорию
                $tempDir = storage_path('app/temp');
                if (! file_exists($tempDir)) {
                    mkdir($tempDir, 0755, true);
                }

                $imageContent = file_get_contents($data['image']);
                $extension = pathinfo(parse_url($data['image'], PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'png';
                $tempImagePath = $tempDir.'/'.uniqid().'.'.$extension;
                file_put_contents($tempImagePath, $imageContent);

                // Создаем UploadedFile из временного файла
                $uploadedFile = new \Illuminate\Http\UploadedFile(
                    $tempImagePath,
                    basename($tempImagePath),
                    mime_content_type($tempImagePath),
                    null,
                    true
                );

                // Загружаем через Image модель
                $attributes = [
                    'title' => json_encode(['Product Image']),
                    'alt' => json_encode(['Product Image']),
                ];

                $image = \App\Models\Image::upload(
                    $uploadedFile,
                    'public',
                    $attributes
                );

                // Очищаем временный файл
                @unlink($tempImagePath);

                // Записываем ID изображения
                $data['image'] = $image->id;
                $data['gallery'] = json_encode([]);
            }

            $product->fill($data);

            if ($isNew) {
                $this->import->increment('created_rows');
            }

            Log::info('Товар успешно '.($isNew ? 'создан' : 'обновлен'), [
                'name' => $product->name,
                'id' => $product->id ?? null,
                'action' => $isNew ? 'create' : 'update',
            ]);

            return $product;
        } catch (\Exception $e) {
            Log::error('Ошибка при обработке товара', [
                'name' => $data['name'] ?? 'Не указано',
                'error' => $e->getMessage(),
                'raw_data' => $data,
            ]);

            return null;
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return 'Импорт товаров успешно завершен! Импортировано записей: '.$import->successful_rows;
    }

    public function started(Import $import): void
    {
        // ... your existing code
    }
}
