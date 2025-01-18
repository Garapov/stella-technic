<?php

namespace App\Filament\Fabricator\PageBlocks;

use App\Models\Former as ModelsFormer;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Select;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class Former extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('former')
            ->icon('heroicon-o-rectangle-stack')
            ->label('Форма')
            ->schema([
                Select::make('form')
                    ->label('Форма')
                    // ->options(ModelsFormer::all()->pluck('name', 'id'))
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search): array => ModelsFormer::where('name', 'like', "%{$search}%")->limit(50)->pluck('name', 'id')->toArray())
                    ->getOptionLabelsUsing(fn (array $values): array => ModelsFormer::whereIn('id', $values)->pluck('name', 'id')->toArray())
                    ->required()
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}