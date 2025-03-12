<?php

namespace App\Filament\Fabricator\PageBlocks;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductParamItem;
use App\Models\ProductVariant;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class Products extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        $products = ProductVariant::all();
        $categories = ProductCategory::all();
        return Block::make('products')
            ->icon('heroicon-o-rectangle-stack')
            ->icon('heroicon-o-rectangle-stack')
            ->label('Товары')
            ->schema([
                Split::make([
                    Section::make([
                        TextInput::make('title')
                            ->label('Заголовок'),
                        Select::make('items')
                            ->label('Товары')
                            ->options($products ? $products->pluck('name', 'id') : [])
                            ->searchable()
                            ->visible(fn (Get $get) => $get('type') == 'products')
                            ->multiple()
                            ->required(),
                        Select::make('category')
                            ->label('Категория')
                            ->options($categories ? $categories->pluck('name', 'id') : [])
                            ->searchable()
                            ->visible(fn (Get $get) => $get('type') == 'category')
                            ->required(),
                        Select::make('paramItems')
                            ->multiple()
                            ->label('Параметры')
                            ->required()
                            ->relationship('paramItems', 'title')
                            ->preload()
                            ->visible(fn (Get $get) => $get('type') == 'filtr')
                            ->options(function () {
                                return ProductParamItem::query()
                                    ->with('productParam')
                                    ->get()
                                    ->mapWithKeys(function ($item) {
                                        return [$item->id => "{$item->productParam->name}: {$item->title}"];
                                    });
                            }),
                    ]),
                    Section::make([
                        Select::make('type')
                            ->label('Тип выбора товаров')
                            ->live()
                            ->default('products')
                            ->required()
                            ->selectablePlaceholder(false)
                            ->options([
                                'products' => 'Выбор товаров',
                                'category' => 'Выбор категории',
                                'filtr' => 'Выбор параметров' 
                            ]),
                        Toggle::make('filter')
                            ->label('Показывать фильтр')
                    ])->grow(false),
                ])->from('md')
                
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}