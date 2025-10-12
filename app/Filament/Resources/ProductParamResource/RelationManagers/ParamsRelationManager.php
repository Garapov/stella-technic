<?php

namespace App\Filament\Resources\ProductParamResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ParamsRelationManager extends RelationManager
{
    protected static string $relationship = "params";
    protected static ?string $title = "Cвойства";

    public function form(Form $form): Form
    {
        $parentRecord = $this->getOwnerRecord();

        return $form->schema([
            Forms\Components\TextInput::make("title")
                ->label("Название")
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make("value")
                ->label("Значение")
                ->required()
                ->numeric()
                ->visible(
                    fn() => $parentRecord->type === "number" ||
                        $parentRecord->type === "slider",
                ),

            Forms\Components\ColorPicker::make("value")
                ->label("Цвет")
                ->required()
                ->visible(fn() => $parentRecord->type === "color"),

            Forms\Components\TextInput::make("value")
                ->label("Значение")
                ->required()
                ->visible(
                    fn() => $parentRecord->type === "checkboxes" ||
                        $parentRecord->type === "switch",
                ),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute("title")
            ->columns([
                Tables\Columns\TextColumn::make("id")->label("ID"),
                Tables\Columns\TextColumn::make("title")->label("Название"),
                Tables\Columns\TextInputColumn::make("sort")->label(
                    "Сортировка",
                ),
                Tables\Columns\TextColumn::make("value")
                    ->formatStateUsing(function ($state, $record) {
                        $parentRecord = $this->getOwnerRecord();
                        return match ($parentRecord->type) {
                            "color" => view("components.color-badge", [
                                "color" => $state,
                            ]),
                            // 'checkboxes' => $state ? 'Yes' : 'No',
                            default => $state,
                        };
                    })
                    ->label("Значение"),
            ])
            ->filters([
                //
            ])
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
