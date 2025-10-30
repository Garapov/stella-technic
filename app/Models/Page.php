<?php

namespace App\Models;

use Datlechin\FilamentMenuBuilder\Concerns\HasMenuPanel;
use Datlechin\FilamentMenuBuilder\Contracts\MenuPanelable;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Z3d0X\FilamentFabricator\Models\Page as ModelsPage;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class Page extends ModelsPage implements Searchable,MenuPanelable
{
    use HasMenuPanel;

    public $searchableType = 'Страницы';

    public function getMenuPanelTitleColumn(): string
    {
        return 'title';
    }

    public function getSearchResult(): SearchResult
    {
        // dd($this);
        $url = $this->slug;

        // dd($this);
        $searchResult = new \Spatie\Searchable\SearchResult(
            $this,
            $this->title,
            $url
        );

        return $searchResult;
    }

    public function getMenuPanelUrlUsing(): callable
    {
        return fn (self $model) => '/' . $model->slug;
    }

    public function getMenuPanelName(): string
    {
        return "Страницы";
    }
}
