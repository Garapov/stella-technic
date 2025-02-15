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
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FormerResource extends Resource
{
    protected static ?string $model = Former::class;
    protected static ?string $navigationIcon = 'carbon-data-set';

    protected static ?string $navigationLabel = 'Формы';
    protected static ?string $modelLabel = 'Форму';
    protected static ?string $pluralModelLabel = 'Формы';
    protected static ?string $navigationGroup = 'Страницы';

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
                    ->label('Текст кнопки'),
                TagsInput::make('recipients')
                    ->separator(',')
                    ->label('Получатели')
                    ->placeholder('recipient@domain.com')
                    ->nestedRecursiveRules([
                        'email',
                    ]),
                Repeater::make('fields')
                    ->schema([
                        TextInput::make('name')
                            ->label('Атрибут "name"')
                            ->required(),
                        TextInput::make('label')
                            ->label('Заголовок поля')
                            ->required(),
                        Select::make('type')
                            ->options([
                                'text' => 'Текстовое поле',
                                'textarea' => 'Область текста',
                                'select' => 'Выпадающий список',
                                'email' => 'Email',
                            ])
                            ->live()
                            ->label('Тип поля')
                            ->required()
                            ->afterStateUpdated(function (?string $state, ?string $old, Set $set) {
                                if ($state == 'select') {
                                    $set('mask_enabled', false);
                                    // dd($get('mask_enabled'));
                                }
                            }),

                        TagsInput::make('options')
                            ->separator(',')
                            ->label('Опции')
                            ->hidden(fn (Get $get): bool => $get('type') != 'select')
                            ->required(fn (Get $get): bool => $get('type') != 'select'),
                        TextInput::make('rules')
                            ->label('Правила валидации')
                            ->hidden(fn (Get $get): bool => $get('type') === 'select')
                            ->columnSpanFull(),
                        Toggle::make('mask_enabled')
                            ->hidden(fn (Get $get): bool => $get('type') === 'select')
                            ->label('Включить маску ввода')
                            ->live(),
                        TextInput::make('mask')
                            ->label('Маска ввода')
                            ->hidden(fn (Get $get): bool => $get('mask_enabled') === false || $get('type') === 'select')
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
                    ->label('Название')
                    ->searchable(),
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
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ResultsRelationManager::class,
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
