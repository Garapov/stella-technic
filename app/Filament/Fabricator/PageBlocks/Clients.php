<?php

namespace App\Filament\Fabricator\PageBlocks;

use App\Models\Client;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class Clients extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('clients')
            ->icon('heroicon-o-rectangle-stack')
            ->label('Клиенты')
            ->columns([
                'default' => 2,
            ])
            ->schema([
                TextInput::make('title')
                    ->label('Заголовок'),
                Toggle::make('mainlink')
                    ->inline(false)
                    ->label('Показать общую ссылку'),
                Select::make('clients')
                    ->label('Клиенты')
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search): array => Client::where('name', 'like', "%{$search}%")->limit(50)->pluck('name', 'id')->toArray())
                    ->getOptionLabelsUsing(fn (array $values): array => Client::whereIn('id', $values)->pluck('name', 'id')->toArray())
                    ->multiple()
                    ->required()
                    ->columnSpan(2)
            ]);
    }

    public static function mutateData(array $data): array
    {
        // Process the selected clients and get the model data for each of id.
        $data['clients'] = Client::findMany($data['clients']);
        // dd($data);
        return $data;
    }
}