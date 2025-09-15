<?php

namespace App\Livewire\General\Header;

use Livewire\Component;
use Spatie\SchemaOrg\Schema;

class Topmenu extends Component
{
    public $menu;
    public $schema;

    public function mount($menu)
    {
        $this->menu = $menu;

        $this->schema = $this->makeSchema()->toScript();
    }

    protected function makeSchema()
    {
        $menu = Schema::menu()->name('Верхнее меню');

        $sections = [];
        $items = [];

        foreach ($this->menu->menuItems as $menuItem) {
            if ($menuItem->children && $menuItem->children->count() > 0) {
                $sections[] = $this->makeMenuSection($menuItem);
            } else {
                $items[] = $this->makeMenuItem($menuItem);
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
            ->url($item->url);
    }

    protected function makeMenuSection($item)
    {
        $section = Schema::menuSection()->name($item->title);

        $childSections = [];
        $childItems = [];

        foreach ($item->children as $child) {
            if ($child->children && $child->children->count() > 0) {
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
        return view('livewire.general.header.topmenu', [
            'schema' => $this->schema,
        ]);
    }
}
