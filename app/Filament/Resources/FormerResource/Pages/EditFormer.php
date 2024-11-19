<?php

namespace App\Filament\Resources\FormerResource\Pages;

use App\Filament\Resources\FormerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFormer extends EditRecord
{
    protected static string $resource = FormerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
            // ...
        ];
    }
}
