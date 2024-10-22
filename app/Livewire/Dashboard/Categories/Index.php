<?php

namespace App\Livewire\Dashboard\Categories;

use App\Models\Category;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        return view('livewire.dashboard.categories.index', [
            'categories' => Category::all(),
        ]);
    }
}
