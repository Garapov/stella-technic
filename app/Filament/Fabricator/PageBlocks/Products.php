<?php

namespace App\Filament\Fabricator\PageBlocks;

use App\Models\Product;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Select;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class Products extends PageBlock
{
    public static function getBlockSchema(): Block
    {

        return Block::make('products')
            ->schema([
                Select::make('items')
                    ->label('Товары')
                    ->options(Product::all()->pluck('name', 'id'))
                    ->searchable()
                    // ->getSearchResultsUsing(fn (string $search): array => Product::where('text', 'like', "%{$search}%")->limit(50)->pluck('text', 'id')->toArray())
                    // ->getOptionLabelsUsing(fn (array $values): array => Product::whereIn('id', $values)->pluck('text', 'id')->toArray())
                    ->multiple()
                    ->required()
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}