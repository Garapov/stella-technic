<?php

namespace App\Filament\Fabricator\PageBlocks;

use Filament\Forms\Components\Builder\Block;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;


class TextColumns extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('text-columns')
            ->icon('heroicon-o-rectangle-stack')
            ->label('Текстовые колонки')
            ->schema([
                TextInput::make('title')
                    ->label('Заголовок')
                    ->required(),
                TextInput::make('grid')
                    ->label('Количество колонок')
                    ->numeric()
                    ->default(2)
                    ->required(),
                Repeater::make('columns')
                    ->label('Колонки')
                    ->required()
                    ->simple(
                        RichEditor::make('text')
                            ->label('Текст')
                            ->required(),
                    )
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}