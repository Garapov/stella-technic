<?php

namespace App\Livewire\Catalog;

use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductVariant;
use Livewire\Component;
use Livewire\WithPagination;

class Items extends Component
{
    use WithPagination;

    protected $paginationTheme = "tailwind";

    public $items = null;
    public $product_ids = [];
    public ?ProductCategory $category = null;
    public $selectedSort = "default";
    public $displayMode = "block";
    public $showSorting = false;
    public $display_filter = true;

    protected $queryString = [
        "selectedSort" => ["as" => "sort", "except" => "default"],
        "displayMode" => ["as" => "display_mode", "except" => "block"],
    ];

    public function mount($slug = null, $brand_slug = null, $products = null)
    {
        if ($slug) {
            $this->category = ProductCategory::where("slug", $slug)->first();
            $this->product_ids = $this->category->products->pluck('id');
        }
        
        if ($brand_slug) {
            $brand = Brand::where("slug", $brand_slug)->first();
            $this->product_ids = $brand->products()->pluck("id");
        }
        
        if ($products) {
            $this->product_ids = $products;
        }
    }

    public function getSortOptions()
    {
        return [
            "default" => [
                "label" => "По умолчанию",
                "icon" => "M3 4.5h14.25M3 9h9.75M3 13.5h9.75m4.5-4.5v12m0 0l-3.75-3.75M17.25 21L21 17.25",
            ],
            "price_asc" => [
                "label" => "Сначала дешевые",
                "icon" => "M3 4.5h14.25M3 9h9.75M3 13.5h5.25m5.25-.75L17.25 9m0 0L21 12.75M17.25 9v12",
            ],
            "price_desc" => [
                "label" => "Сначала дорогие",
                "icon" => "M3 4.5h14.25M3 9h9.75M3 13.5h5.25m5.25-.75L17.25 9m0 0L21 12.75M17.25 9v12",
            ],
            "name_asc" => [
                "label" => "По названию А-Я",
                "icon" => "M3 4.5h14.25M3 9h9.75M3 13.5h9.75m4.5-4.5v12m0 0l-3.75-3.75M17.25 21L21 17.25",
            ],
            "name_desc" => [
                "label" => "По названию Я-А",
                "icon" => "M3 4.5h14.25M3 9h9.75M3 13.5h9.75m4.5-4.5v12m0 0l-3.75-3.75M17.25 21L21 17.25",
            ],
        ];
    }

    public function render()
    {
        return view('livewire.catalog.items', [
            'products' => ProductVariant::whereIn('product_id', $this->product_ids)->paginate(12),
        ]);
    }
}
