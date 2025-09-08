<?php

namespace App\Filament\Resources\ProductVariantResource\Pages;

use App\Filament\Resources\ProductVariantResource;
use App\Models\ProductVariant;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductVariant extends EditRecord
{
    protected static string $resource = ProductVariantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('open')
                ->label('Открыть на сайте')
                ->icon('ionicon-open-outline')
                ->url(fn (ProductVariant $record): string => route('client.catalog', $record->urlChain()))
                ->openUrlInNewTab(),
        ];
    }
}
