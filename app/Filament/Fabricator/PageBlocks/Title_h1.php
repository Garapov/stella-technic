<?php

namespace App\Filament\Fabricator\PageBlocks;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\TextInput;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;


class Title_h1 extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('title_h1')
            ->icon('heroicon-o-rectangle-stack')
            ->label('Заголовок H1')
            ->schema([
                TextInput::make('title')
                    ->label('Текст заголовка')
                    ->required()
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}