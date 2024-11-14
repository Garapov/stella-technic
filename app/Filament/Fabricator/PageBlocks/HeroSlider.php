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
            ->icon('heroicon-o-rectangle-stack')
            ->schema([
                Select::make('slides')
                    ->label('Слайды')
                    ->options(MainSlider::all()->pluck('title', 'id'))
                    ->searchable()
                    ->multiple()
                    ->required()
            ]);
    }

    public static function mutateData(array $data): array
    {
        // Process the selected slides and get the model data for each of id.
        $data['slides'] = MainSlider::findMany($data['slides']);

        return $data;
    }
}