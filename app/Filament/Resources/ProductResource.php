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
use App\Models\ProductParamItem;
use Illuminate\Database\Eloquent\Model;
use App\Tables\Columns\ImageByIdColumn;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Товары';
    protected static ?string $modelLabel = 'Товар';
    protected static ?string $pluralModelLabel = 'Товары';
    protected static ?string $navigationGroup = 'Магазин';


    protected static ?string $recordTitleAttribute = 'name';

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
                        Tab::make('Описание')->schema([
                            Section::make()->columns([
                                'sm' => 1,
                                'xl' => 1,
                                '2xl' => 1,
                            ])
                            ->schema([
                                Forms\Components\Textarea::make('short_description')
                                    ->required()
                                    ->label('Короткое описание')
                                    ->columnSpanFull(),
                                Forms\Components\RichEditor::make('description')
                                    ->toolbarButtons([
                                        'blockquote',
                                        'bold',
                                        'bulletList',
                                        'codeBlock',
                                        'h2',
                                        'h3',
                                        'italic',
                                        'link',
                                        'orderedList',
                                        'redo',
                                        'strike',
                                        'underline',
                                        'undo',
                                    ])
                                    ->required()
                                    ->label('Описание')
                                    ->columnSpanFull(),
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
                                    ->required()
                                    ->columnSpan('1'),
                                ImagePicker::make('gallery')
                                    ->label('Галерея')
                                    ->multiple()
                                    ->columnSpan('2')
                            ])
                        ])->columnSpan('full'),
                        Tab::make('Категории')->schema([
                            Forms\Components\Select::make('categories')
                            ->label('Категории')
                            ->placeholder('Выберите категории')
                            ->multiple()
                            ->relationship('categories', 'title')
                            ->preload()
                        ])->columnSpan('full'),
                        Tab::make('Параметры')->schema([
                            Forms\Components\Select::make('paramItems')
                                ->multiple()
                                ->relationship('paramItems', 'title')
                                ->preload()
                                ->createOptionForm([
                                    Forms\Components\Select::make('product_param_id')
                                        ->relationship('productParam', 'name')
                                        ->required(),
                                    Forms\Components\TextInput::make('title')
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('value')
                                        ->required()
                                ])
                                ->options(function () {
                                    return ProductParamItem::query()
                                        ->with('productParam')
                                        ->get()
                                        ->mapWithKeys(function ($item) {
                                            return [$item->id => "{$item->productParam->name}: {$item->title}"];
                                        });
                                })
                                ->columnSpanFull()
                        ])->columnSpan('full'),
                        Tab::make('Поиск')->schema([
                            Section::make()->columns([
                                'sm' => 1,
                                'xl' => 1,
                                '2xl' => 1,
                            ])
                            ->schema([
                                Forms\Components\Textarea::make('synonims')
                                    ->label('Синонимы для поиска')
                                    ->columnSpanFull(),
                            ])
                        ])
                    ])->columnSpan('full'), 
                
                
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageByIdColumn::make('image')
                    ->label('Картинка'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Название')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Цена')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('new_price')
                    ->label('Цена со скидкой')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('count')
                    ->label('Количество')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата создания')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Дата обновления')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        // dd($record->categories->pluck('title')->implode(', '));
        return [
            'Категории' => $record->categories->pluck('title')->implode(', '),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'slug', 'short_description', 'description'];
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\VariantsRelationManager::class,
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
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
