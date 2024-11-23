<?php

namespace App\Livewire\General;

use App\Models\ProductCategory;
use Datlechin\FilamentMenuBuilder\Models\Menu;
use Livewire\Component;

class Header extends Component
{
    public function render()
    {
        // dd(ProductCategory::all());
        return view('livewire.general.header', [
            'categories' => ProductCategory::where('parent_id', -1)->get(),
            'topmenu' => Menu::location('top_menu')
        ]);
    }
}
