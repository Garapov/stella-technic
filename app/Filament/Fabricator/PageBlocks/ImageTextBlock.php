<?php

namespace App\Filament\Fabricator\PageBlocks;

use Filament\Forms\Components\Builder\Block;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\ToggleButtons;

class ImageTextBlock extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make("image-text")
            ->icon("heroicon-o-rectangle-stack")
            ->label("Блок с изображением и текстом")
            ->schema([
                FileUpload::make("image")
                    ->required()
                    ->image()
                    ->label("Картинка")
                    ->directory("image-text")
                    ->visibility("public")
                    ->panelLayout("grid")
                    ->imageEditor()
                    ->preserveFilenames()
                    ->imageEditorMode(2),
                RichEditor::make("text")->label("Текст")->required(),
                ToggleButtons::make("alignment")
                    ->label("Выравнивание")
                    ->default("start")
                    ->required()
                    ->grouped()
                    ->options([
                        "start" => "По верху",
                        "center" => " По центру",
                        "end" => "По низу",
                    ])
                    ->icons([
                        "start" => "carbon-align-vertical-top",
                        "center" => "carbon-align-vertical-center",
                        "end" => "carbon-align-vertical-bottom",
                    ])
                    ->required(),
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}
