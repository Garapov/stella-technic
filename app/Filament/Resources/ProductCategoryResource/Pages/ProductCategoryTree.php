<?php

namespace App\Filament\Resources\ProductCategoryResource\Pages;

use App\Filament\Resources\ProductCategoryResource;
use Filament\Pages\Actions\CreateAction;
// use SolutionForest\FilamentTree\Actions;
// use SolutionForest\FilamentTree\Concern;
// use SolutionForest\FilamentTree\Resources\Pages\TreePage as BasePage;
// use SolutionForest\FilamentTree\Support\Utils;

class ProductCategoryTree
{
    protected static string $resource = ProductCategoryResource::class;

    protected static int $maxDepth = 10;

    protected function getActions(): array
    {
        return [
            // $this->getCreateAction(),
            // SAMPLE CODE, CAN DELETE
            //\Filament\Pages\Actions\Action::make('sampleAction'),
        ];
    }

    protected function hasDeleteAction(): bool
    {
        return true;
    }

    protected function hasEditAction(): bool
    {
        return true;
    }

    protected function hasViewAction(): bool
    {
        return false;
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    protected function getFooterWidgets(): array
    {
        return [];
    }

    // CUSTOMIZE ICON OF EACH RECORD, CAN DELETE
    // public function getTreeRecordIcon(?\Illuminate\Database\Eloquent\Model $record = null): ?string
    // {
    //     return null;
    // }
}