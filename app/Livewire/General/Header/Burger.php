<?php

namespace App\Livewire\General\Header;

use App\Models\ProductCategory;

use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Burger extends Component
{
    public $categories;
    public $topmenu;
    public function mount($categories = [])
    {
        $this->categories = $categories;
        $this->topmenu = Cache::rememberForever('menus:top_menu', function () { return Menu::location('top_menu'); });
    }
    public function render()
    {
        return view('livewire.general.header.burger', [
            "categories" => $this->categories,
        ]);
    }
}
