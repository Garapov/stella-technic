<?php

namespace App\Filament\Fabricator\PageBlocks;

use Filament\Forms\Components\Builder\Block;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;

class ImageTextBlock extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('image-text')
            ->icon('heroicon-o-rectangle-stack')
            ->label('Блок с изображением и текстом')
            ->schema([
                FileUpload::make('image')
                    ->label('Изображение')
                    ->required(),
                RichEditor::make('text')
                    ->label('Текст')
                    ->required(),
                Toggle::make('center')
                    ->label('Выравниваать по центру')
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}