<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;
    protected static ?string $navigationIcon = "carbon-user-certification";

    protected static ?string $navigationLabel = "Клиенты";
    protected static ?string $modelLabel = "Клиента";
    protected static ?string $pluralModelLabel = "Клиенты";
    protected static ?string $navigationGroup = "Страницы";

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\FileUpload::make("image")
                ->required()
                ->image()
                ->label("Логотип")
                ->directory("clients")
                ->visibility("public")
                ->imageEditor()
                ->preserveFilenames()
                ->imageEditorMode(2),
            Forms\Components\TextInput::make("name")
                ->label("Название")
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make("image")->label("Логотип"),
                Tables\Columns\TextColumn::make("name")
                    ->label("Название")
                    ->searchable(),
                Tables\Columns\TextColumn::make("created_at")
                    ->label("Дата создания")
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make("updated_at")
                    ->label("Дата обновления")
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
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
            "index" => Pages\ListClients::route("/"),
            "create" => Pages\CreateClient::route("/create"),
            "edit" => Pages\EditClient::route("/{record}/edit"),
        ];
    }
}
