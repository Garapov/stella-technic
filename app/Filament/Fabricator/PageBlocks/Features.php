<?php

namespace App\Filament\Fabricator\PageBlocks;

use App\Models\Feature;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Select;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;
use Filament\Forms\Components\TextInput;

class Features extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('features')
            ->icon('heroicon-o-rectangle-stack')
            ->label('Преимущества')
            ->schema([
                TextInput::make('title')
                    ->label('Заголовок')
                    ->required(),
                TextInput::make('subtitle')
                    ->label('Подзаголовок')
                    ->required(),
                Select::make('features')
                    ->label('Преимущества')
                    // ->options(Feature::all()->pluck('text', 'id'))
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search): array => Feature::where('text', 'like', "%{$search}%")->limit(50)->pluck('text', 'id')->toArray())
                    ->getOptionLabelsUsing(fn (array $values): array => Feature::whereIn('id', $values)->pluck('text', 'id')->toArray())
                    ->multiple()
                    ->required()
            ]);
    }

    public static function mutateData(array $data): array
    {
        // Process the selected features and get the model data for each of id.
        $data['features'] = Feature::findMany($data['features']);

        return $data;
    }
}