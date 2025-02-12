<?php

namespace App\Filament\Resources\ProductCategoryResource\RelationManagers;

use App\Forms\Components\ImagePicker;
use App\Models\ProductParamItem;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    public function form(Form $form): Form
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->modalWidth(MaxWidth::SevenExtraLarge),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]));
    }
}
