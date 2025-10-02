<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeatureResource\Pages;
use App\Filament\Resources\FeatureResource\RelationManagers;
use App\Models\Feature;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Guava\FilamentIconPicker\Forms\IconPicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FeatureResource extends Resource
{
    protected static ?string $model = Feature::class;
    protected static ?string $navigationIcon = 'carbon-heat-map';

    protected static ?string $navigationLabel = 'Преимущества';
    protected static ?string $modelLabel = 'Преимущество';
    protected static ?string $pluralModelLabel = 'Преимущества';
    protected static ?string $navigationGroup = 'Страницы';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make("icon")
                    ->required()
                    ->image()
                    ->label("Иконка")
                    ->directory("features")
                    ->visibility("public")
                    ->preserveFilenames(),
                Forms\Components\TextInput::make('text')
                    ->label('Текст')
                    ->required(),
                Forms\Components\Toggle::make('show_on_main')
                    ->label('Показывать на главной')
                    ->inline(false)
                    ->required(),
                Forms\Components\TextInput::make('sort')
                    ->label('Сортировка')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make("icon")
                    ->label('Иконка'),
                Tables\Columns\TextColumn::make('text')
                    ->label('Текст')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата создания')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextInputColumn::make('sort')->label("Сортировка"),
                Tables\Columns\ToggleColumn::make('show_on_main')
                    ->label('Показывать на главной'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeatures::route('/'),
            'create' => Pages\CreateFeature::route('/create'),
            'edit' => Pages\EditFeature::route('/{record}/edit'),
        ];
    }
}
