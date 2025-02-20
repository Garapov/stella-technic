<?php

namespace App\Livewire\Main;

use Livewire\Component;
use App\Models\ProductCategory;

class CategoriesInBlock extends Component
{
    public function render()
    {
        return view('livewire.main.categories-in-block',  [
            'categories' => ProductCategory::where('parent_id', '-1')->get(),
        ]);
    }
}
