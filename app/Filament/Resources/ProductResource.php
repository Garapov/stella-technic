<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Forms\Components\ImagePicker;
use App\Models\Product;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Outerweb\FilamentImageLibrary\Filament\Forms\Components\ImageLibraryPicker;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Товары';

    protected static ?string $navigationGroup = 'Магазин';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('Основная информация')->schema([
                            Section::make()->columns([
                                'sm' => 1,
                                'xl' => 2,
                                '2xl' => 3,
                            ])
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->label('Название'),
                                Forms\Components\TextInput::make('price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('р.')
                                    ->label('Цена'),
                                Forms\Components\TextInput::make('new_price')
                                    ->numeric()
                                    ->lt('price')
                                    ->prefix('р.')
                                    ->label('Цена со скидкой'),
                                Forms\Components\TextInput::make('count')
                                    ->required()
                                    ->numeric()
                                    ->label('Остаток'),
                                Forms\Components\Toggle::make('is_popular')
                                    ->label('Популярный')
                                    ->inline(false),
                            ])
                        ])->columnSpan('full'),
                        Tab::make('Изображения')->schema([
                            Section::make()->columns([
                                'sm' => 1,
                                'xl' => 2,
                                '2xl' => 3,
                            ])
                            ->schema([
                                ImagePicker::make('image')
                                    ->label('Картинка')
                                    ->columnSpan('1'),
                                ImagePicker::make('gallery')
                                    ->label('Галерея')
                                    ->multiple()
                                    ->columnSpan('2')
                            ])
                        ])->columnSpan('full'),
                        Tab::make('Категории')->schema([
                            SelectTree::make('categories')
                            ->label('Категории')
                            ->relationship('categories', 'title', 'product_category_id')
                        ])->columnSpan('full'),
                    ])->columnSpan('full'), 
                
                
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('new_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('count')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
