<?php

namespace App\Filament\Fabricator\PageBlocks;

use App\Models\Partner;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Select;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class Partners extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('partners')
            ->schema([
                Select::make('partners')
                    ->label('Партнеры')
                    ->options(Partner::all()->pluck('name', 'id'))
                    ->searchable()
                    ->multiple()
                    ->required()
            ]);
    }

    public static function mutateData(array $data): array
    {
        // Process the selected partners and get the model data for each of id.
        $data['partners'] = Partner::findMany($data['partners']);

        return $data;
    }
}