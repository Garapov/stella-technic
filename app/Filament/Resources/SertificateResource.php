<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SertificateResource\Pages;
use App\Filament\Resources\SertificateResource\RelationManagers;
use App\Models\Sertificate;
use App\Tables\Columns\ImageByIdColumn;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SertificateResource extends Resource
{
    protected static ?string $model = Sertificate::class;

    protected static ?string $navigationIcon = "carbon-certificate";
    protected static ?string $navigationLabel = "Сертификаты";
    protected static ?string $modelLabel = "Сертификат";
    protected static ?string $pluralModelLabel = "Сертификаты";
    protected static ?string $navigationGroup = "Страницы";

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make("name")
                ->label("Название")
                ->required(),
            Forms\Components\FileUpload::make("image")
                ->required()
                ->image()
                ->label("Картинка")
                ->directory("sertificates")
                ->visibility("public")
                ->imageEditor()
                ->preserveFilenames()
                ->imageEditorMode(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make("image")->label("Картинка"),
                Tables\Columns\TextColumn::make("name")
                    ->label("Название")
                    ->searchable(),
                Tables\Columns\TextColumn::make("created_at")
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make("updated_at")
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([Tables\Actions\EditAction::make()])
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
            "index" => Pages\ListSertificates::route("/"),
            "create" => Pages\CreateSertificate::route("/create"),
            "edit" => Pages\EditSertificate::route("/{record}/edit"),
        ];
    }
}
