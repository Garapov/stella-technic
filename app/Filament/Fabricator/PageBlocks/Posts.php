<?php

namespace App\Filament\Fabricator\PageBlocks;

use App\Models\Post;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class Posts extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        // dd(Post::all());
        return Block::make('posts')
            ->icon('heroicon-o-rectangle-stack')
            ->label('Посты блога')
            ->schema([
                TextInput::make('title')
                    ->label('Заголовок'),
                Toggle::make('mainlink')
                    ->inline(false)
                    ->label('Показать общую ссылку'),
                Select::make('news')
                    ->label('Посты')
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search): array => Post::where('title', 'like', "%{$search}%")->limit(50)->pluck('title', 'id')->toArray())
                    ->getOptionLabelsUsing(fn (array $values): array => Post::whereIn('id', $values)->pluck('title', 'id')->toArray())
                    ->multiple()
                    ->required()
                    ->columnSpan(2)
            ]);
    }

    public static function mutateData(array $data): array
    {
        // Process the selected news and get the model data for each of id.
        $data['news'] = Post::findMany($data['news']);

        return $data;
    }
}