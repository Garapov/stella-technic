<?php

namespace App\Livewire\Catalog;

use App\Models\ProductCategory;
use Illuminate\Support\Facades\Request;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ItemsLazy extends Component
{

    public function mount() {
        if (setting('points_catalog')) {
            $this->js(setting('points_catalog')); 
        }
    }

    #[Computed()]
    public function category()
    {
        return ProductCategory::where("slug", Request::segment(count(Request::segments())))->first();
    }

    #[Computed()]
    public function nonTagCategories()
    {
        return $this->category?->categories->where('is_tag', false) ?? collect([]);
    }

    #[Computed()]
    public function tagCategories()
    {
        return $this->category?->categories->where('is_tag', true) ?? collect([]);
    }

    
    public function render()
    {
        // dd($this->category->variations);
        return view('livewire.catalog.items-lazy');
    }
}
