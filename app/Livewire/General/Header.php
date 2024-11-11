<?php

namespace App\Livewire\General;

use App\Models\Category;
use Livewire\Component;

class Header extends Component
{
    public function render()
    {
        return view('livewire.general.header', [
            'categories' => Category::where('category_id', null)->get(),
        ]);
    }
}
