<?php

namespace App\Livewire\General\Header;

use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Spatie\SchemaOrg\Schema;

class Catalog extends Component
{
    public $categories;
    public $schema;

    public function mount($categories)
    {
        $this->categories = $categories;        
        $this->schema = Cache::rememberForever('schemes:catalog', function () {
            return $this->makeSchema()->toScript();
        });
    }

    protected function makeSchema()
    {
        $menu = Schema::menu()->name('Каталог товаров');

        $sections = [];
        $items = [];

        foreach ($this->categories as $category) {
            if ($category->categories && $category->categories->count() > 0) {
                $sections[] = $this->makeMenuSection($category);
            } else {
                $items[] = $this->makeMenuItem($category);
            }
        }

        if ($sections) {
            $menu->hasMenuSection($sections);
        }

        if ($items) {
            $menu->hasMenuItem($items);
        }

        return $menu;
    }

    protected function makeMenuItem($item)
    {
        return Schema::menuItem()
            ->name($item->title)
            ->url($item->urlChain());
    }

    protected function makeMenuSection($item)
    {
        $section = Schema::menuSection()
            ->name($item->title)
            ->url($item->urlChain());

        $childSections = [];
        $childItems = [];

        foreach ($item->categories as $child) {
            if ($child->categories && $child->categories->count() > 0) {
                $childSections[] = $this->makeMenuSection($child);
            } else {
                $childItems[] = $this->makeMenuItem($child);
            }
        }

        if ($childSections) {
            $section->hasMenuSection($childSections);
        }

        if ($childItems) {
            $section->hasMenuItem($childItems);
        }

        return $section;
    }

    public function render()
    {
        return view('livewire.general.header.catalog', [
            'categories' => $this->categories,
            'schema' => $this->schema
        ]);
    }
}
