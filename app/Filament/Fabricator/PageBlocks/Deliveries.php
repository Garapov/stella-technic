<?php

namespace App\Filament\Fabricator\PageBlocks;

use App\Models\Delivery;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Select;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class Deliveries extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('deliveries')
            ->icon('heroicon-o-rectangle-stack')
            ->label('Доставка')
            ->schema([
                Select::make('deliveries')
                    ->label('Варианты доставки')
                    ->options(Delivery::all()->pluck('name', 'id'))
                    ->multiple()
                    ->required()
                    ->searchable()
            ]);
    }

    public static function mutateData(array $data): array
    {
        $data['deliveries'] = Delivery::whereIn('id', $data['deliveries'])->get();

        return $data;
    }
}