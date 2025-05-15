<?php

namespace App\Filament\Fabricator\PageBlocks;

use Filament\Forms\Components\Builder\Block;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class MapCallback extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('map-callback')
            ->icon("heroicon-o-rectangle-stack")
            ->label("Карта с формой")
            ->schema([
                //
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}