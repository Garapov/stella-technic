<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use App\Forms\Components\ImagePicker;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->label('Название'),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('р.')
                    ->label('Цена'),
                TextInput::make('new_price')
                    ->numeric()
                    ->lt('price')
                    ->prefix('р.')
                    ->label('Цена со скидкой'),
                ImagePicker::make('image')
                    ->label('Картинка')
                    ->required(),
                Toggle::make('is_default')
                    ->label('Вариант по умолчанию')
                    ->afterStateUpdated(function ($state, Forms\Set $set, $record) {
                        if ($state && $record && $record->exists) {
                            // Get the owning product through the relationship manager
                            $product = $this->getOwnerRecord();
                            
                            // Unset other default variants for this product
                            $product->variants()
                                ->where('id', '!=', $record->id)
                                ->update(['is_default' => false]);
                        }
                    })
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('new_price')
                    ->money()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_default')
                    ->label('По умолчанию')
                    ->beforeStateUpdated(function ($record, $state) {
                        if ($state && $record && $record->exists) {
                            // Get the owning product through the relationship manager
                            $product = $this->getOwnerRecord();
                            
                            // Unset other default variants for this product
                            $product->variants()
                                ->where('id', '!=', $record->id)
                                ->update(['is_default' => false]);
                        }
                        return $state;
                    })
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
