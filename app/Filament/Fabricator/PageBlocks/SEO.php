<?php

namespace App\Filament\Fabricator\PageBlocks;

use Filament\Forms\Components\Builder\Block;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class SEO extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('s-e-o')
            ->icon('heroicon-o-rectangle-stack')
            ->label('SEO данные')
            ->schema([
                Builder::make('seo')
                    ->label('SEO данные')
                    ->addActionLabel('Добавить данные')
                    ->blockNumbers(false)
                    ->blocks([
                        Builder\Block::make('title')
                            ->label("Заголовок")
                            ->schema([
                                TextInput::make("title")->label("Заголовок")->required(),
                            ])->maxItems(1),
                        Builder\Block::make('description')
                            ->label("Описание")
                            ->schema([
                                Textarea::make("description")->label("Описание")->required(),
                            ])->maxItems(1),
                        Builder\Block::make('image')
                            ->label("Картинка")
                            ->schema([
                                FileUpload::make("image")
                                    ->required()
                                    ->image()
                                    ->label("Картинка")
                                    ->directory("pages/seo")
                                    ->visibility("public")
                                    ->imageEditor()
                                    ->preserveFilenames()
                                    ->imageCropAspectRatio("1:1")
                                    ->imageEditorMode(2),
                            ])->maxItems(1)
                    ])
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}