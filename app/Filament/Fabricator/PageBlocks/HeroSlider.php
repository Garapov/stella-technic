<?php

namespace App\Filament\Fabricator\PageBlocks;

use App\Models\MainSlider;
use Filament\Forms\Components\Builder\Block;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Cache;

class HeroSlider extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('hero-slider')
            ->icon('heroicon-o-rectangle-stack')
            ->label('Главный слайдер')
            ->schema([
                Select::make('slides')
                    ->label('Слайды')
                    // ->options(MainSlider::all()->pluck('title', 'id'))
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search): array => MainSlider::where('title', 'like', "%{$search}%")->limit(50)->pluck('title', 'id')->toArray())
                    ->getOptionLabelsUsing(fn (array $values): array => MainSlider::whereIn('id', $values)->pluck('title', 'id')->toArray())
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