<?php

namespace App\Livewire\Dashboard\Products;

use App\Models\Category;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        return view('livewire.dashboard.products.index', [
            'categories' => Category::where('category_id', null)->get()
        ]);
    }
}
