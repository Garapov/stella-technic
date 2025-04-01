<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BatchResource\Pages;
use App\Filament\Resources\BatchResource\RelationManagers;
use App\Models\Batch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BatchResource extends Resource
{
    protected static ?string $model = Batch::class;

    protected static ?string $navigationIcon = "carbon-pull-request";
    protected static ?string $navigationLabel = "Серии";
    protected static ?string $modelLabel = "Серия";
    protected static ?string $pluralModelLabel = "Серии";
    protected static ?string $navigationGroup = "Магазин";

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\FileUpload::make("image")
                ->directory("batches")
                ->visibility("public")
                ->imageEditor()
                ->preserveFilenames()
                ->imageCropAspectRatio("1:1")
                ->imageEditorMode(2)
                ->label("Картинка")
                ->image()
                ->required(),
            Forms\Components\TextInput::make("name")->required(),
            Forms\Components\RichEditor::make("description")
                ->required()
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make("image"),
                Tables\Columns\TextColumn::make("name")->searchable(),
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
            "index" => Pages\ListBatches::route("/"),
            "create" => Pages\CreateBatch::route("/create"),
            "edit" => Pages\EditBatch::route("/{record}/edit"),
        ];
    }
}
