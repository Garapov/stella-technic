<?php

namespace App\Filament\Fabricator\PageBlocks;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use App\Models\Sertificate;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class Certificates extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        $certificates = Sertificate::all();

        return Block::make('certificates')
        ->icon('heroicon-o-rectangle-stack')
            ->label('Сертификаты')
            ->schema([
                TextInput::make('title')
                    ->label('Заголовок')
                    ->required(),
                Select::make('certificates')
                    ->label('Сертификаты')
                    ->searchable()
                    ->options($certificates ? $certificates->pluck('name', 'id') : [])
                    // ->getSearchResultsUsing(fn (string $search): array => \App\Models\Certificate::where('title', 'like', "%{$search}%")->limit(50)->pluck('title', 'id')->toArray())
                    // ->getOptionLabelsUsing(fn (array $values): array => \App\Models\Certificate::whereIn('id', $values)->pluck('title', 'id')->toArray())
                    ->multiple()
                    ->required(),
                ToggleButtons::make('type')
                    ->options([
                        'slider' => 'Слайдер',
                        'list' => 'Список'
                    ]),
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}