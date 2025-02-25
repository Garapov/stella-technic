<?php

namespace App\Filament\Fabricator\PageBlocks;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class Contacts extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('contacts')
            ->icon('heroicon-o-rectangle-stack')
            ->label('Контакты')
            ->schema([
                TextInput::make('title')
                    ->label('Заголовок')
                    ->required(),
                RichEditor::make('description')
                    ->label('Описание')
                    ->required()
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}