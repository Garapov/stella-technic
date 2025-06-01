<?php

namespace App\Filament\Exports;

use App\Models\ProductVariant;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductVariantExporter extends Exporter
{
    protected static ?string $model = ProductVariant::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('id'),
            ExportColumn::make('product_id')
                ->state(function ($record): string {
                    $categories = null;
                    $data = array(
                        'name' => $record->product->name,
                        'image' => $record->product->gallery[0],
                        'categories' => array()
                    );

                    foreach($record->product->categories as $key=>$category) {
                        $categories = array(
                            'name' => $category->title,
                            'image' => Storage::disk(config('filesystems.default'))->url($category->image),
                            'parent' => $categories
                        );
                    }
                    $data['categories'][] = $categories;
                    return json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                })
                ->label('product_id'),
            // ExportColumn::make('product_param_item_id'),
            ExportColumn::make('name')->label('name'),
            ExportColumn::make('price')->label('price'),
            ExportColumn::make('new_price')->label('new_price'),
            // ExportColumn::make('image'),
            // ExportColumn::make('deleted_at'),
            // ExportColumn::make('created_at'),
            // ExportColumn::make('updated_at'),
            ExportColumn::make('is_default')
                ->label('is_default')
                ->state(function ($record): bool {
                    return $record->is_default ? 'TRUE' : 'FALSE';
                }),
            ExportColumn::make('sku')
                ->label('sku'),
            ExportColumn::make('slug')->label('slug'),
            ExportColumn::make('short_description')->label('short_description'),
            ExportColumn::make('description')->label('description'),
            ExportColumn::make('is_popular')
                ->label('is_popular')
                ->state(function ($record): bool {
                    return $record->is_popular ? 'TRUE' : 'FALSE';
                }),
            ExportColumn::make('count')->label('count'),
            ExportColumn::make('paramItems')
                ->label('paramItems')
                ->state(function ($record): string {
                    $data = array();
                    if ($record->paramItems) {
                        foreach($record->paramItems as $param) {
                            $data[] = array(
                                'param' => $param->productParam->name,
                                'name' => $param->title,
                                'value' => $param->value,
                            );
                        }
                    }
                    return json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }),
            ExportColumn::make('parametrs')
                ->label('parametrs')
                ->state(function ($record): string {
                    $data = array();
                    if ($record->parametrs) {
                        foreach($record->parametrs as $param) {
                            $data[] = array(
                                'param' => $param->productParam->name,
                                'name' => $param->title,
                                'value' => $param->value,
                            );
                        }
                    }
                    return json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }),
            ExportColumn::make('synonims')->label('synonims'),
            ExportColumn::make('gallery')
                ->label('gallery')
                ->state(function ($record): string {
                    $gallery = "";
                    if ($record->gallery) {
                        foreach ($record->gallery as $key => $image) {
                            Log::info($image);

                            $url = Storage::disk(config('filesystems.default'))->url($image);

                            // Log::info(['url', $url]);
                            $delimeter = $key == (count($record->gallery) - 1) ? '' : ',';

                            Log::info(['delimeter3', $key, count($record->gallery), $delimeter]);

                            $gallery .= $url . $delimeter;

                            Log::info(['gallery', $gallery]);
                        }
                    }
                    return $gallery;
                }),
            // ExportColumn::make('links')
            //     ->state(function ($record): string {
            //         return json_encode($record->links, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            //     }),
            // ExportColumn::make('batch.name'),
            ExportColumn::make('auth_price')->label('auth_price'),
            ExportColumn::make('seo')
                ->label('seo')
                ->state(function ($record): string {
                    return json_encode($record->seo, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }),
            ExportColumn::make('is_constructable')
                ->label('is_constructable')
                ->state(function ($record): bool {
                    return $record->is_constructable ? 'TRUE' : 'FALSE';
                }),
            ExportColumn::make('constructor_type')->label('constructor_type'),
            ExportColumn::make('rows')
                ->label('rows')
                ->state(function ($record): string {
                    return json_encode($record->rows, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }),
            ExportColumn::make('show_category_files')->label('show_category_files'),
            ExportColumn::make('files')
                ->label('files')
                ->state(function ($record): string {
                    $files = [];
                    if ($record->files) {
                        foreach ($record->files as $file) {
                            $files[] = array(
                                'name' => $file['name'],
                                'file' => Storage::disk(config('filesystems.default'))->url($file['file'])
                            );
                        }
                    }
                    // Log::info(json_encode($record->files, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
                    return json_encode($files, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                }),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Экспорт завершен. Экспортированно ' . number_format($export->successful_rows) .' строк.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' ошибок.';
        }

        return $body;
    }
}
