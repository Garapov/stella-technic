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
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Параметры товаров';
    protected static ?string $modelLabel = 'Параметр товара';
    protected static ?string $pluralModelLabel = 'Параметры товаров';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Название')
                    ->required(),
                Forms\Components\FileUpload::make("icon")
                    ->image()
                    ->label("Иконка")
                    ->directory("param-icons")
                    ->visibility("public")
                    ->imageEditor()
                    ->preserveFilenames()
                    ->imageEditorMode(2),
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
                Forms\Components\Toggle::make('fixed')
                    ->label('Зафиксирован')
                    ->inline(false)
                    ->required(),
                Forms\Components\Toggle::make('is_hidden')
                    ->label('Скрыт')
                    ->inline(false),
                Forms\Components\Toggle::make('show_on_preview')
                    ->label('Показывать в листинге')
                    ->inline(false)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Название')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Тип')
                    ->searchable()
                    ->formatStateUsing(fn ($record) => $record ? match ($record->type) {
                        'text' => 'Текст',
                        'number' => 'Числовой',
                        'checkboxes' => 'Список чекбоксов',
                        'color' => 'Цвет',
                    } : ''),
                Tables\Columns\ToggleColumn::make('allow_filtering')
                    ->label('Разрешить фильтрацию'),
                Tables\Columns\ToggleColumn::make('show_on_preview')
                    ->label('Показывать в листинге'),
                Tables\Columns\ToggleColumn::make('show_on_table')
                    ->label('Показывать в таблице'),
                Tables\Columns\ToggleColumn::make('fixed')
                    ->label('Зафиксирован'),
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
