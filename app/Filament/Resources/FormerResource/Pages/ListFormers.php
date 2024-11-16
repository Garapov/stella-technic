<?php

namespace App\Filament\Resources\FormerResource\Pages;

use App\Filament\Resources\FormerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFormers extends ListRecords
{
    protected static string $resource = FormerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
