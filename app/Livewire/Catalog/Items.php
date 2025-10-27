<?php

namespace App\Livewire\Catalog;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductVariant;
use App\Services\ProductSelector;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Items extends Component
{
    use WithPagination;

    public $items = null;
    public $product_ids = [];
    public ?ProductCategory $category = null;
    public $nonTagCategories = [];
    public $tagCategories = [];

    #[Url]
    public $sort = "count:asc";
    #[Url]
    public $filters = [];

    #[Url]
    public $displayMode = "block";
    public $showSorting = false;
    public $display_filter = false;
    public $inset = false;
    public $type = "category";

    protected ProductSelector $selector;

    public function boot(ProductSelector $selector)
    {
        $this->selector = $selector;
    }

    public function mount($path = null, $brand_slug = null, $products = null, $display_filter = false, $inset = false)
    {
        $this->display_filter = $display_filter;
        $this->inset = $inset;

        if ($path) {
            $slug = collect(explode('/', $path))->last();
            $this->category = ProductCategory::with(['products:id', 'variations:id', 'paramItems:id', 'categories:id,parent_id,is_tag,title'])
                ->where("slug", $slug)->first();


            if (!$this->category || !$this->category->is_visible) abort(404);

            $this->product_ids = $this->selector->fromCategory($this->category);
            $this->nonTagCategories = $this->category?->categories->where('is_tag', false) ?? [];
            $this->tagCategories = $this->category?->categories->where('is_tag', true) ?? [];
            $this->type = 'category';
        }

        if ($brand_slug) {
            $this->product_ids = $this->selector->fromBrandSlug($brand_slug);
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
            "count:asc" => [
                "label" => "По умолчанию",
                "icon" => "M3 4.5h14.25M3 9h9.75M3 13.5h9.75m4.5-4.5v12m0 0l-3.75-3.75M17.25 21L21 17.25",
            ],
            "price:asc" => [
                "label" => "Подешевле",
                "icon" => "M3 4.5h14.25M3 9h9.75M3 13.5h5.25m5.25-.75L17.25 9m0 0L21 12.75M17.25 9v12",
            ],
            "price:desc" => [
                "label" => "Подороже",
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

    #[On("filters-changed")]
    public function updateFilters($filters, $availableParams)
    {
        $this->filters = $filters;
        // dd([$this->filters, $availableParams]);
        $this->resetPage();
    }

    public function render()
    {

        // dd($this->filters);
        $builder = ProductVariant::where('is_hidden', false)
            ->whereHas('product', function ($q) {
                $q->where('is_hidden', false);
            })
            ->filter($this->filters)
            ->sort([$this->sort]);

        if ($this->type === 'products' || in_array($this->category?->type, ['variations'])) {
            $builder->whereIn('id', $this->product_ids);
        } else {
            $builder->whereIn('product_id', $this->product_ids);
        }


        $all_products = $builder->with([
            'product.brand',
            'product.categories',
            'paramItems.productParam',
            'parametrs.productParam',
            'batch',
        ])->get();

        $products = $builder->with([
            'product.brand',
            'product.categories',
            'paramItems.productParam',
            'parametrs.productParam',
            'batch',
        ])->paginate(40);

        return view("livewire.catalog.items", [
            "products" => $products,
            "all_products" => $all_products,
            "mode" => $this->displayMode,
            "nonTagCategories" => $this->nonTagCategories,
            "tagCategories" => $this->tagCategories,
            "category" => $this->category
        ]);
    }

    public function changeDisplayMode($mode)
    {
        $this->resetPage();
        $this->displayMode = $mode;
    }

    #[On("filters-reset")]
    public function resetFilters()
    {
        $this->filters = [];
    }
}
