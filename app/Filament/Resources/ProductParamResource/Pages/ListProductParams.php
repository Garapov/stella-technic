<?php

namespace App\Filament\Resources\ProductParamResource\Pages;

use App\Filament\Resources\ProductParamResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListProductParams extends ListRecords
{
    protected static string $resource = ProductParamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $productParams = $this->getResource()::getModel()::query()
            ->pluck('tags')
            ->filter()
            ->flatMap(function ($tags) {
                if (is_array($tags)) {
                    // если уже массив (JSON) — просто вернуть его
                    return $tags;
                }

                if (is_string($tags)) {
                    // если строка — разбить по запятой
                    return array_map('trim', explode(',', $tags));
                }

                return [];
            })
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        // Базовые вкладки
        $tabs = [
            'Все' => Tab::make(),
        ];

        // Динамически создаём вкладки по тегам
        foreach ($productParams as $tag) {
            $count = $this->getResource()::getModel()::query()
                ->whereJsonContains('tags', $tag)
                ->count();

            $tabs["{$tag} ({$count})"] = Tab::make()
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereJsonContains('tags', $tag)
                );
        }

        return $tabs;
    }
}
