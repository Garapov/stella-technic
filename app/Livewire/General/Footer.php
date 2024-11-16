<?php

namespace App\Livewire\General;

use Datlechin\FilamentMenuBuilder\Models\Menu;
use Livewire\Component;

class Footer extends Component
{
    public function render()
    {
        return view('livewire.general.footer', [
            'menu' => Menu::location('footer')
        ]);
    }
}
