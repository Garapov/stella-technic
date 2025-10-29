<?php

namespace App\Livewire\General;

use Datlechin\FilamentMenuBuilder\Models\Menu;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Footer extends Component
{
    public function render()
    {
        return view('livewire.general.footer', [
            'menu' => Cache::rememberForever('menus:footer', function () { return Menu::location('footer'); }),
        ]);
    }
}
