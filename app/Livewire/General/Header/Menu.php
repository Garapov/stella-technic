<?php

namespace App\Livewire\General\Header;

use Livewire\Component;
use Datlechin\FilamentMenuBuilder\Models\Menu as MenuModel;

class Menu extends Component
{
    public function render()
    {
        return view('livewire.general.header.menu', [
            'menu' => MenuModel::location('header')
        ]);
    }
}
