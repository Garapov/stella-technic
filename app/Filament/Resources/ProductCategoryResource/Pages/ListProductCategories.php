<?php

namespace App\Filament\Resources\ProductCategoryResource\Pages;

use App\Filament\Resources\ProductCategoryResource;
use App\Filament\Resources\ProductCategoryResource\Widgets\ProductCategoryWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;

class ListProductCategories extends ListRecords
{
    protected static string $resource = ProductCategoryResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
 
    protected function getHeaderWidgets(): array
    {
        return [
            // ProductCategoryWidget::class
        ];
    }
    protected function getHeaderActions(): array
    {
        return [
            Action::make('tree-list')
                ->url('/admin/product-categories/tree-list')
        ];
    }
}
