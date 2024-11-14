<?php

namespace App\Filament\Fabricator\PageBlocks;

use App\Models\MainSlider;
use Filament\Forms\Components\Builder\Block;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;
use Filament\Forms\Components\Select;

class HeroSlider extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('hero-slider')
            ->schema([
                Select::make('slides')
                    ->label('Слайды')
                    ->options(MainSlider::all()->pluck('title', 'id'))
                    ->searchable()
                    ->multiple()
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}