<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PartnerResource\Pages;
use App\Filament\Resources\PartnerResource\RelationManagers;
use App\Models\Partner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PartnerResource extends Resource
{
    protected static ?string $model = Partner::class;
    protected static ?string $navigationIcon = "carbon-partnership";

    protected static ?string $navigationLabel = "Партнеры";
    protected static ?string $modelLabel = "Партнера";
    protected static ?string $pluralModelLabel = "Партнеры";
    protected static ?string $navigationGroup = "Страницы";

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make("name")
                ->label("Название")
                ->required(),
            Forms\Components\TextInput::make("link")
                ->label("Ссылка")
                ->required(),
            Forms\Components\FileUpload::make("image")
                ->required()
                ->image()
                ->label("Картинка")
                ->directory("partners")
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
                Tables\Columns\TextColumn::make("link")
                    ->label("Ссылка")
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
            "index" => Pages\ListPartners::route("/"),
            "create" => Pages\CreatePartner::route("/create"),
            "edit" => Pages\EditPartner::route("/{record}/edit"),
        ];
    }
}
