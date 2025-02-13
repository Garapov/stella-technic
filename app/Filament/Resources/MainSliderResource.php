<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MainSliderResource\Pages;
use App\Filament\Resources\MainSliderResource\RelationManagers;
use App\Models\MainSlider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Forms\Components\ImagePicker;

class MainSliderResource extends Resource
{
    protected static ?string $model = MainSlider::class;

    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';
    protected static ?string $navigationLabel = 'Слайды на главной';
    protected static ?string $modelLabel = 'Слайд на главной';
    protected static ?string $pluralModelLabel = 'Слайды на главной';
    protected static ?string $navigationGroup = 'Страницы';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Заголовок')
                    ->required()
                    ->columnSpanFull(),
                ImagePicker::make('image')
                    ->label('Картинка')
                    ->required(),
                Forms\Components\RichEditor::make('description')
                    ->label('Описание')
                    ->required(),
                Forms\Components\TextInput::make('button_text')
                    ->label('Текст кнопки')
                    ->required(),
                Forms\Components\TextInput::make('link')
                    ->label('Ссылка')
                    ->required(),
                Forms\Components\ColorPicker::make('background')
                    ->label('Цвет фона')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('button_text')
                    ->searchable(),
                Tables\Columns\TextColumn::make('link')
                    ->searchable(),
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
            'index' => Pages\ListMainSliders::route('/'),
            'create' => Pages\CreateMainSlider::route('/create'),
            'edit' => Pages\EditMainSlider::route('/{record}/edit'),
        ];
    }
    
}
