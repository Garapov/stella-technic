<?php

namespace App\Filament\Fabricator\PageBlocks;

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
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class Products extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        $products = ProductVariant::all();
        $categories = ProductCategory::all();
        return Block::make("products")
            ->icon("heroicon-o-rectangle-stack")
            ->icon("heroicon-o-rectangle-stack")
            ->label("Товары")
            ->schema([
                Split::make([
                    Section::make([
                        TextInput::make("title")->label("Заголовок"),
                        Select::make("items")
                            ->label("Товары")
                            ->live()
                            ->options(function () {
                                return ProductVariant::query()
                                    ->get()
                                    ->mapWithKeys(function ($item) {
                                        return [
                                            $item->id => "{$item->name} {$item->sku}",
                                        ];
                                    });
                            })
                            ->searchable()
                            ->visible(
                                fn(Get $get) => $get("type") == "products"
                            )
                            ->multiple()
                            ->required(),
                        Select::make("category")
                            ->label("Категория")
                            ->live()
                            ->options(
                                $categories
                                    ? $categories->pluck("title", "slug")
                                    : []
                            )
                            ->searchable()
                            ->visible(
                                fn(Get $get) => $get("type") == "category"
                            )
                            ->required(),
                        Select::make("parametrs")
                            ->multiple()
                            ->label("Параметры")
                            ->live()
                            ->required()
                            ->preload()
                            ->visible(fn(Get $get) => $get("type") == "filter")
                            ->options(function () {
                                return ProductParamItem::query()
                                    ->with("productParam")
                                    ->get()
                                    ->mapWithKeys(function ($item) {
                                        return [
                                            $item->id => "{$item->productParam->name}: {$item->title}",
                                        ];
                                    });
                            }),
                    ]),
                    Section::make([
                        Select::make("type")
                            ->label("Тип выбора товаров")
                            ->live()
                            ->default("products")
                            ->required()
                            ->selectablePlaceholder(false)
                            ->options([
                                "products" => "Выбор товаров",
                                "category" => "Выбор категории",
                                "filter" => "Выбор параметров",
                            ]),
                        Toggle::make("filter")->label("Показывать фильтр"),
                    ])->grow(false),
                ])->from("md"),
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}
