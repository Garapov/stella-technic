<?php

namespace App\Livewire\General\Header;

use Livewire\Component;

class Topmenu extends Component
{
    public $menu;

    public function mount($menu)
    {
        $this->menu = $menu;
    }
    public function render()
    {
        return view('livewire.general.header.topmenu');
    }
}
