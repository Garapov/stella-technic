<?php

namespace App\Livewire\Catalog;

use App\Models\ProductCategory;
use Illuminate\Support\Facades\Request;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ItemsLazy extends Component
{

    #[Computed()]
    public function category()
    {
        return ProductCategory::where("slug", Request::segment(count(Request::segments())))->first();
    }

    
    public function render()
    {
        // dd($this->category->variations);
        return view('livewire.catalog.items-lazy');
    }
}
