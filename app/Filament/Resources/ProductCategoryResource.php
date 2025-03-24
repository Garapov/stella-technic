<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductCategoryResource\Pages;
use App\Filament\Resources\ProductCategoryResource\RelationManagers\ProductsRelationManager;
use App\Models\ProductCategory;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Guava\FilamentIconPicker\Forms\IconPicker;
use App\Models\ProductParamItem;

class ProductCategoryResource extends Resource
{
    protected static ?string $model = ProductCategory::class;

    protected static ?string $navigationIcon = "carbon-center-circle";
    protected static ?string $navigationLabel = "Категории";
    protected static ?string $modelLabel = "Категорию";
    protected static ?string $pluralModelLabel = "Категории";
    protected static ?string $navigationGroup = "Магазин";

    protected static ?string $recordTitleAttribute = "title";

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make("title")->label("Заголовок")->required(),
            Select::make("paramItems")
                ->multiple()
                ->relationship("paramItems", "title")
                ->preload()
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
            Toggle::make("is_visible")->inline(false)->label("Видимость"),
            Textarea::make("description")->label("Описание")->required(),
            FileUpload::make("image")
                ->directory("categories")
                ->label("Картинка")
                ->image(),
            IconPicker::make("icon")->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make("image")->label("Картинка"),
                Tables\Columns\TextColumn::make("title")
                    ->label("Название")
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [ProductsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            "tree-list" => Pages\ProductCategoryTree::route("/tree-list"),
            "index" => Pages\ListProductCategories::route("/"),
            "create" => Pages\CreateProductCategory::route("/create"),
            "edit" => Pages\EditProductCategory::route("/{record}/edit"),
        ];
    }
}
