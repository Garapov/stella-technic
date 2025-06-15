<?php

namespace App\Livewire\General\Header;

use App\Models\ProductCategory;

use Livewire\Component;

class Burger extends Component
{
    public function render()
    {
        return view('livewire.general.header.burger', [
            "categories" => ProductCategory::where('parent_id', -1)->get()
        ]);
    }
}
