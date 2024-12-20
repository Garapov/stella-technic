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

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Название товара'),
            
            ImportColumn::make('price')
                ->label('Цена')
                ->numeric(),

            ImportColumn::make('short_description')
                ->label('Короткое описание'),
            
            ImportColumn::make('description')
                ->label('Описание'),
        ];
    }

    public function resolveRecord(): ?Product
    {
        $data = $this->data;
        
        Log::info('Начало обработки товара', [
            'name' => $data['name'] ?? 'Не указано',
            'price' => $data['price'] ?? 'Не указано',
        ]);

        try {
            // Проверяем наличие обязательных полей
            if (!isset($data['name'])) {
                throw new \Exception('Отсутствует обязательное поле "name"');
            }

            // Ищем существующий товар по имени
            $product = Product::where('name', $data['name'])->first();
            
            if ($product) {
                Log::info('Найден существующий товар, обновляем', [
                    'name' => $product->name,
                    'id' => $product->id
                ]);
            } else {
                $product = new Product();
                Log::info('Создаем новый товар', [
                    'name' => $data['name']
                ]);
            }
            
            $product->fill($data);
            
            Log::info('Товар успешно ' . ($product->exists ? 'обновлен' : 'создан'), [
                'name' => $product->name,
                'id' => $product->id ?? null,
                'action' => $product->exists ? 'update' : 'create'
            ]);

            return $product;
        } catch (\Exception $e) {
            Log::error('Ошибка при обработке товара', [
                'name' => $data['name'] ?? 'Не указано',
                'error' => $e->getMessage(),
                'raw_data' => $data
            ]);
            
            return null;
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        Log::info('Импорт завершен', [
            'total_rows' => $import->total_rows,
            'successful_rows' => $import->successful_rows,
            'failed_rows' => $import->failed_rows
        ]);

        return 'Импорт товаров успешно завершен! Импортировано записей: ' . $import->successful_rows;
    }

    public function started(Import $import): void
    {
        Log::info('Начат новый импорт товаров', [
            'import_id' => $import->id,
            'user_id' => auth()->id()
        ]);
    }
} 