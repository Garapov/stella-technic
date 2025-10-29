<?php

namespace App\Livewire\General\Header;

use App\Models\ProductCategory;

use Livewire\Component;

class Burger extends Component
{
    public $categories;
    public function mount($categories = [])
    {
        $this->categories = $categories;
    }
    public function render()
    {
        return view('livewire.general.header.burger', [
            "categories" => $this->categories,
        ]);
    }
}
