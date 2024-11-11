<?php

namespace App\Livewire\General\Header;

use App\Models\Category;
use Livewire\Component;

class Catalog extends Component
{
    public function render()
    {
        return view('livewire.general.header.catalog', [
            'categories' => Category::where('category_id', null)->get(),
        ]);
    }
}
