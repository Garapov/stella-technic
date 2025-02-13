<?php

namespace App\Filament\Fabricator\PageBlocks;

use App\Models\Product;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class Products extends PageBlock
{
    public static function getBlockSchema(): Block
    {

        return Block::make('products')
            ->icon('heroicon-o-rectangle-stack')
            ->label('Товары')
            ->schema([
                Select::make('items')
                    ->label('Товары')
                    // ->options(Product::all()->pluck('name', 'id'))
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search): array => Product::where('name', 'like', "%{$search}%")->limit(50)->pluck('name', 'id')->toArray())
                    ->getOptionLabelsUsing(fn (array $values): array => Product::whereIn('id', $values)->pluck('name', 'id')->toArray())
                    ->multiple()
                    ->required(),
                Toggle::make('filter')
                    ->label('Показывать фильтр')
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}