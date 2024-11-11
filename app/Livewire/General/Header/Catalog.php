<?php

namespace App\Livewire\General\Header;

use Livewire\Component;

class Catalog extends Component
{
    public $categories;
    public function mount($categories)
    {
        $this->categories = $categories;
    }
    public function render()
    {
        return view('livewire.general.header.catalog');
    }
}
