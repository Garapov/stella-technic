<?php

namespace App\Livewire\General\Header;

use Livewire\Component;
use Datlechin\FilamentMenuBuilder\Models\Menu;

class Popular extends Component
{
    public function render()
    {
        return view('livewire.general.header.popular', [
            'menu' => Menu::location('search_menu')
        ]);
    }
}
