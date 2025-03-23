<?php

namespace App\Filament\Fabricator\PageBlocks;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class Banner extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make("banner")
            ->icon("heroicon-o-rectangle-stack")
            ->label("Баннер")
            ->schema([
                FileUpload::make("image")
                    ->required()
                    ->image()
                    ->label("Изображение")
                    ->directory("banners")
                    ->visibility("public")
                    ->imageEditor()
                    ->preserveFilenames()
                    ->imageCropAspectRatio("21:9")
                    ->imageEditorMode(2),
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}
