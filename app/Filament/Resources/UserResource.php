<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationLabel = 'Пользователи';
    protected static ?string $modelLabel = 'Пользователя';
    protected static ?string $pluralModelLabel = 'Пользователи';
    protected static ?string $navigationGroup = 'Страницы';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('Основная информация')->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('ФИО')
                                ->required(),
                            Forms\Components\TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->required(),
                            Forms\Components\TextInput::make('phone')
                                ->label('Телефон')
                                ->mask('9 (999) 999-99-99'),
                            Forms\Components\TextInput::make('password')
                                ->label('Пароль')
                                ->password()
                                ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                                ->dehydrated(fn (?string $state): bool => filled($state)),
                            Forms\Components\Select::make('roles')
                                ->multiple()
                                ->relationship('roles', 'name')
                                ->preload()
                                ->label('Роли'),
                        ])->columns([
                            'sm' => 1,
                            'xl' => 2,
                            '2xl' => 3,
                        ]),
                        Tab::make('Юридическая информация')->schema([
                            Forms\Components\TextInput::make('company_name')
                                ->label('Название компании'),
                            Forms\Components\TextInput::make('inn')
                                ->label('ИНН'),
                            Forms\Components\TextInput::make('kpp')
                                ->label('КПП'),
                            Forms\Components\TextInput::make('bik')
                                ->label('БИК'),
                            Forms\Components\TextInput::make('correspondent_account')
                                ->label('К/С'),
                            Forms\Components\TextInput::make('bank_account')
                                ->label('Р/С'),
                            Forms\Components\TextInput::make('yur_address')
                                    ->label('Юр. адрес'),
                        ])->columns([
                            'sm' => 1,
                            'xl' => 2,
                            '2xl' => 3,
                        ])
                    ])->columnSpan('full')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('ФИО')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('company_name')
                    ->label('Название компании')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Телефон')
                    ->searchable(),
                Tables\Columns\TextColumn::make('inn')
                    ->label('ИНН')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kpp')
                    ->label('КПП')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('bik')
                    ->label('БИК')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('correspondent_account')
                    ->label('К/С')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('bank_account')
                    ->label('Р/С')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('yur_address')
                    ->label('Юр. адрес')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
