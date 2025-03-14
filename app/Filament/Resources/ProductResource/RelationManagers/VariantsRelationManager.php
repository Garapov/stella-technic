<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use App\Forms\Components\ImagePicker;
use App\Models\ProductParamItem;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Tables\Columns\ImageByIdColumn;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Livewire\Attributes\On;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    protected static ?string $title = 'Варианты товара';

    #[On('refreshVariations')]
    public function refresh(): void
    {}

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('Основная информация')->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->live()
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
                                Forms\Components\TextInput::make('sku')
                                    ->required()
                                    ->label('Артикул'),
                                Forms\Components\Toggle::make('is_popular')
                                    ->label('Популярный')
                                    ->inline(false),
                        ])->columns([
                            'sm' => 1,
                            'xl' => 2,
                            '2xl' => 3,
                        ])->columnSpan('full'),
                        Tab::make('Описание')->schema([
                                Forms\Components\Textarea::make('short_description')
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
                        ])->columns([
                            'sm' => 1,
                            'xl' => 1,
                            '2xl' => 1,
                        ])->columnSpan('full'),
                        Tab::make('Изображения')->schema([
                                ImagePicker::make('image')
                                    ->label('Картинка')
                                    ->required()
                                    ->columnSpan('1'),
                                ImagePicker::make('gallery')
                                    ->label('Галерея')
                                    ->multiple()
                                    ->columnSpan('2')
                        ])->columns([
                            'sm' => 1,
                            'xl' => 2,
                            '2xl' => 3,
                        ])->columnSpan('full'),
                        Tab::make('Параметры')->schema([
                            Forms\Components\Select::make('parametrs')
                                ->multiple()
                                ->relationship('parametrs', 'title')
                                ->preload()
                                ->options(function () {
                                    return ProductParamItem::query()
                                        ->with('productParam')
                                        ->get()
                                        ->mapWithKeys(function ($item) {
                                            return [$item->id => "{$item->productParam->name}: {$item->title}"];
                                        });
                                })
                                ->columnSpanFull()
                        ])->columnSpan('full')
                    ])->columnSpan('full'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                ImageByIdColumn::make('image')
                    ->label('Картинка'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Название'),
                Tables\Columns\TextColumn::make('sku')
                    ->label('Артикул'),
                Tables\Columns\TextColumn::make('price')
                    ->money('RUB', locale: 'ru')
                    ->sortable()
                    ->label('Цена'),
                Tables\Columns\ToggleColumn::make('is_popular')
                    ->label('Популярный'),
                Tables\Columns\ToggleColumn::make('is_default')
                    ->label('По умолчанию')
                    ->beforeStateUpdated(function ($record, $state) {
                        if ($state && $record && $record->exists) {
                            // Get the owning product through the relationship manager
                            $product = $this->getOwnerRecord();

                            // Unset other default variants for this product
                            $product->variants()
                                ->where('id', '!=', $record->id)
                                ->update(['is_default' => false]);
                        }
                        return $state;
                    })
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
