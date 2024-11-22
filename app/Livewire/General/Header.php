<?php

namespace App\Livewire\General;

use App\Models\ProductCategory;
use Datlechin\FilamentMenuBuilder\Models\Menu;
use Livewire\Component;

class Header extends Component
{
    public function render()
    {
        return view('livewire.general.header', [
            'categories' => ProductCategory::where('category_id', null)->get(),
            'topmenu' => Menu::location('top_menu')
        ]);
    }
}
