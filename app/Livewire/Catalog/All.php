<?php

namespace App\Livewire\Catalog;

use App\Models\ProductCategory;
use Livewire\Component;

class All extends Component
{
    public function render()
    {
        return view('livewire.catalog.all', [
            'categories' => ProductCategory::where('parent_id', -1)->get(),
        ]);
    }
}
