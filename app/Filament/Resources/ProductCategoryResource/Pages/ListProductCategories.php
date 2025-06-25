<?php

namespace App\Filament\Resources\ProductCategoryResource\Pages;

use App\Filament\Resources\ProductCategoryResource;
use App\Filament\Resources\ProductCategoryResource\Widgets\ProductCategoryWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

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
            // Action::make('tree-list')
            //     ->url('/admin/product-categories/tree-list')
        ];
    }

    public function getTabs(): array
{
        return [
            'Первого уровня' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('parent_id', -1)),
            'Все' => Tab::make(),
        ];
    }
}
