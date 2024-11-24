<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductParamResource\Pages;
use App\Filament\Resources\ProductParamResource\RelationManagers;
use App\Models\ProductParam;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductParamResource extends Resource
{
    protected static ?string $model = ProductParam::class;
    protected static ?string $navigationGroup = 'Магазин';
    protected static ?string $navigationLabel = 'Параметры товаров';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Название')
                    ->required(),
                Forms\Components\Select::make('type')
                    ->label('Тип')
                    ->options([
                        'number' => 'Числовой',
                        'checkboxes' => 'Список чекбоксов',
                        'color' => 'Цвет',
                    ]),
                Forms\Components\Toggle::make('allow_filtering')
                    ->label('Разрешить фильтрацию')
                    ->inline(false)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\IconColumn::make('allow_filtering')
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
            RelationManagers\ParamsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductParams::route('/'),
            'create' => Pages\CreateProductParam::route('/create'),
            'edit' => Pages\EditProductParam::route('/{record}/edit'),
        ];
    }
}
