<?php

namespace App\Filament\Resources\MainSliderResource\Pages;

use App\Filament\Resources\MainSliderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMainSlider extends EditRecord
{
    protected static string $resource = MainSliderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
