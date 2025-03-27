<?php

namespace App\Livewire\Catalog;

use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductVariant;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Items extends Component
{
    use WithPagination;

    protected $paginationTheme = "tailwind";

    public $items = null;
    public $product_ids = [];
    public ?ProductCategory $category = null;
    #[Url()]
    public $sort = 'null';
    
    public $displayMode = "block";
    public $showSorting = false;
    public $display_filter = false;
    public $type = 'category'; 

    public function mount($slug = null, $brand_slug = null, $products = null, $display_filter = false)
    {
        $this->$display_filter = $display_filter;
        if ($slug) {
            $this->category = ProductCategory::where("slug", $slug)->first();
            $this->product_ids = $this->category->products->pluck('id');
            $this->type = 'category';
        }
        
        if ($brand_slug) {
            $brand = Brand::where("slug", $brand_slug)->first();
            $this->product_ids = $brand->products()->pluck("id");
            $this->type = 'brand';
        }
        
        if ($products) {
            $this->product_ids = $products;
            $this->type = 'products';
        }
    }

    public function getSortOptions()
    {
        return [
            "null" => [
                "label" => "По умолчанию",
                "icon" => "M3 4.5h14.25M3 9h9.75M3 13.5h9.75m4.5-4.5v12m0 0l-3.75-3.75M17.25 21L21 17.25",
            ],
            "price:asc" => [
                "label" => "Дешевле",
                "icon" => "M3 4.5h14.25M3 9h9.75M3 13.5h5.25m5.25-.75L17.25 9m0 0L21 12.75M17.25 9v12",
            ],
            "price:desc" => [
                "label" => "Дороже",
                "icon" => "M3 4.5h14.25M3 9h9.75M3 13.5h5.25m5.25-.75L17.25 9m0 0L21 12.75M17.25 9v12",
            ],
            "name:asc" => [
                "label" => "По названию А-Я",
                "icon" => "M3 4.5h14.25M3 9h9.75M3 13.5h9.75m4.5-4.5v12m0 0l-3.75-3.75M17.25 21L21 17.25",
            ],
            "name:desc" => [
                "label" => "По названию Я-А",
                "icon" => "M3 4.5h14.25M3 9h9.75M3 13.5h9.75m4.5-4.5v12m0 0l-3.75-3.75M17.25 21L21 17.25",
            ],
        ];
    }

    public function updateSort($sort)
    {
        $this->sort = $sort;
    }

    public function render()
    {
        
        if ($this->type == 'products') {
            $products = ProductVariant::whereIn('id', $this->product_ids)->sort($this->sort)->paginate(12);
        } else {
            $products = ProductVariant::whereIn('product_id', $this->product_ids)->sort([$this->sort])->paginate(12);
        }




        return view('livewire.catalog.items', [
            'products' => $products,
            'mode' => $this->displayMode,
        ]);
    }

    public function changeDisplayMode($mode)
    {
        $this->displayMode = $mode;
    }
}
