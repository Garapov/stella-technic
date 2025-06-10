<?php

namespace App\Filament\Fabricator\PageBlocks;

use App\Models\Image;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;
use Filament\Forms\Components\TextInput;

class Gallery extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make("gallery")
            ->icon("heroicon-o-rectangle-stack")
            ->label("Галерея")
            ->schema([
                FileUpload::make("images")
                    ->required()
                    ->image()
                    ->label("Галерея")
                    ->directory("gallery")
                    ->visibility("public")
                    ->multiple()
                    ->reorderable()
                    ->panelLayout("grid")
                    ->imageEditor()
                    ->preserveFilenames()
                    ->imageEditorMode(2),
                TextInput::make("grid")
                    ->label("Количество колонок")
                    ->numeric()
                    ->required(),
            ]);
    }

    public static function mutateData(array $data): array
    {
        // dd(Image::whereIn('id', $data['gallery'])->get()->toArray());
        // $data["gallery"] = Image::whereIn("id", $data["gallery"])->get();
        return $data;
    }
}
