<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeliveryResource\Pages;
use App\Filament\Resources\DeliveryResource\RelationManagers;
use App\Models\Delivery;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeliveryResource extends Resource
{
    protected static ?string $model = Delivery::class;

    protected static ?string $navigationIcon = 'carbon-delivery';

    protected static ?string $navigationLabel = 'Доставка';
    protected static ?string $modelLabel = 'Доставку';
    protected static ?string $pluralModelLabel = 'Доставки';
    protected static ?string $navigationGroup = 'Магазин';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Split::make([
                    Section::make([
                        Forms\Components\TextInput::make('name')
                            ->label('Название')
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->label('Описание')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('points')
                            ->label('Точки')
                            ->hidden(fn (Get $get): bool => $get('type') != 'map')
                            ->columnSpanFull(),
                        // Forms\Components\Textarea::make('settings')
                        //     ->label('Настройки')
                        //     ->columnSpanFull(),
                    ]),
                    Section::make([
                        Select::make('type')
                            ->label('Тип')
                            ->options([
                                'text' => 'Текст',
                                'map' => 'Карта',
                                'delivery_systems' => 'Системы доставки',
                            ])
                            ->selectablePlaceholder(false)
                            ->default('text')
                            ->required()
                            ->live(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Активен')
                            ->required(),
                    ])->grow(false),
                ])->from('md')->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
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
            'index' => Pages\ListDeliveries::route('/'),
            'create' => Pages\CreateDelivery::route('/create'),
            'edit' => Pages\EditDelivery::route('/{record}/edit'),
        ];
    }
}
