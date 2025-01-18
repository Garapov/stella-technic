<?php

namespace App\Filament\Fabricator\PageBlocks;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class Articles extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('articles')
            ->icon('heroicon-o-rectangle-stack')
            ->label('Статьи')
            ->schema([
                TextInput::make('title')
                    ->label('Заголовок')
                    ->required(),
                Select::make('articles')
                    ->label('Статьи')
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search): array => \App\Models\Article::where('title', 'like', "%{$search}%")->limit(50)->pluck('title', 'id')->toArray())
                    ->getOptionLabelsUsing(fn (array $values): array => \App\Models\Article::whereIn('id', $values)->pluck('title', 'id')->toArray())
                    ->multiple()
                    ->required()
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}