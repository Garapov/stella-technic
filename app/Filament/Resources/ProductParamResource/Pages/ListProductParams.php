<?php

namespace App\Filament\Resources\ProductParamResource\Pages;

use App\Filament\Resources\ProductParamResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductParams extends ListRecords
{
    protected static string $resource = ProductParamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
