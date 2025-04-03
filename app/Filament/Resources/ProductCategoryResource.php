<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductCategoryResource\Pages;
use App\Filament\Resources\ProductCategoryResource\RelationManagers\ProductsRelationManager;
use App\Models\ProductCategory;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Split;
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
    protected static ?string $navigationLabel = "Категории товаров";
    protected static ?string $modelLabel = "Категорию товаров";
    protected static ?string $pluralModelLabel = "Категории товаров";
    protected static ?string $navigationGroup = "Магазин";
    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = "title";

    public static function form(Form $form): Form
    {
        return $form->schema([
            Split::make([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Основная информация')
                            ->schema([
                                TextInput::make("title")->label("Заголовок")->required(),
                                Textarea::make("description")->label("Описание")->required(),
                                // Select::make("paramItems")
                                //     ->multiple()
                                //     ->relationship("paramItems", "title")
                                //     ->preload()
                                //     ->options(function () {
                                //         return ProductParamItem::query()
                                //             ->with("productParam")
                                //             ->get()
                                //             ->mapWithKeys(function ($item) {
                                //                 return [
                                //                     $item->id => "{$item->productParam->name}: {$item->title}",
                                //                 ];
                                //             });
                                //     }),
                            ]),
                        Tabs\Tab::make('Изображения')
                            ->schema([
                                FileUpload::make("image")
                                    ->directory("categories")
                                    ->label("Картинка")
                                    ->image(),
                                IconPicker::make("icon")->required(),
                            ]),
                        Tabs\Tab::make('SEO')
                            ->schema([
                                Builder::make('seo')
                                    ->label('SEO данные')
                                    ->addActionLabel('Добавить данные')
                                    ->blockNumbers(false)
                                    ->blocks([
                                        Builder\Block::make('title')
                                            ->label("Заголовок")
                                            ->schema([
                                                TextInput::make("title")->label("Заголовок")->required(),
                                            ])->maxItems(1),
                                        Builder\Block::make('description')
                                            ->label("Описание")
                                            ->schema([
                                                Textarea::make("description")->label("Описание")->required(),
                                            ])->maxItems(1),
                                        Builder\Block::make('image')
                                            ->label("Картинка")
                                            ->schema([
                                                FileUpload::make("image")
                                                    ->required()
                                                    ->image()
                                                    ->label("Картинка")
                                                    ->directory("categories/seo")
                                                    ->visibility("public")
                                                    ->imageEditor()
                                                    ->preserveFilenames()
                                                    ->imageCropAspectRatio("1:1")
                                                    ->imageEditorMode(2),
                                            ])->maxItems(1)
                                    ])
                            ]),
                    ]),                    
                Section::make([
                    Toggle::make("is_visible")->inline(false)->label("Видимость"),
                ])->grow(false),
            ])->columnSpanFull()->from('md')
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
