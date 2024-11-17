<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FormerResource\Pages;
use App\Filament\Resources\FormerResource\RelationManagers;
use App\Models\Former;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FormerResource extends Resource
{
    protected static ?string $model = Former::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->label('Название формы')
                    ->columnSpanFull(),
                Textarea::make('thanks_text')
                    ->required()
                    ->label('Текст спасибо')
                    ->columnSpanFull(),
                TextInput::make('button_text')
                    ->required()
                    ->label('Текст кнопки')
                    ->columnSpanFull(),
                Repeater::make('fields')
                    ->schema([
                        TextInput::make('name')
                            ->label('Имя поля')
                            ->required(),
                        TextInput::make('label')
                            ->label('Заголовок поля')
                            ->required(),
                        Select::make('type')
                            ->options([
                                'text' => 'Текстовое поле',
                                'textarea' => 'Область текста',
                                'select' => 'Выпадающий список',
                            ])
                            ->live()
                            ->label('Тип поля')
                            ->required(),
                        Textarea::make('options')
                            ->label('Опции (разделять запятой)')
                            ->hidden(fn (Get $get): bool => $get('type') != 'select')
                            ->required(fn (Get $get): bool => $get('type') != 'select'),
                        TextInput::make('rules')
                            ->label('Правила валидации')
                            ->columnSpanFull(),
                        Hidden::make('value')
                    ])
                    ->label('Поля формы')
                    ->reorderableWithButtons()
                    ->grid(2)
                    ->defaultItems(2)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
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
            'index' => Pages\ListFormers::route('/'),
            'create' => Pages\CreateFormer::route('/create'),
            'edit' => Pages\EditFormer::route('/{record}/edit'),
        ];
    }
}
