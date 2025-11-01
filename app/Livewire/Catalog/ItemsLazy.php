<?php

namespace App\Livewire\Catalog;

use App\Models\ProductCategory;
use Illuminate\Support\Facades\Request;
use Livewire\Component;

class ItemsLazy extends Component
{
    public ?ProductCategory $category = null;
    public function mount($brand_slug = null, $products = null, $display_filter = false, $inset = false)
    {

        $slug = Request::segment(count(Request::segments()));
        $this->category = ProductCategory::with(['products:id', 'variations:id', 'paramItems:id', 'categories:id,parent_id,is_tag,title'])
            ->where("slug", $slug)->first();

        dd($this->category);
    }
    public function render()
    {
        return view('livewire.catalog.items-lazy');
    }
}
