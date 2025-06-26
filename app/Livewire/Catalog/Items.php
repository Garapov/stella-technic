<?php

namespace App\Livewire\Catalog;

use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductVariant;
use Livewire\Attributes\On;
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

    #[Url]
    public $sort = "id:asc";

    public $filters = [];

    #[Url]
    public $displayMode = "block";

    public $showSorting = false;
    public $display_filter = false;
    public $type = "category";

    public function mount($path = null, $brand_slug = null, $products = null, $display_filter = false)
    {
        $this->display_filter = $display_filter;

        if ($path) {
            $slug = collect(explode('/', $path))->last();
            $this->category = ProductCategory::with([
                'products:id',
                'products.variants:id,product_id',
                'variations:id',
                'paramItems:id',
            ])->where("slug", $slug)->first();

            if (!$this->category) return;

            switch ($this->category->type) {
                case 'duplicator':
                    if ($this->category->duplicate_id) {
                        $this->product_ids = ProductCategory::with('products:id')
                            ->find($this->category->duplicate_id)
                            ?->products->pluck("id") ?? collect();
                    }
                    break;

                case 'filter':
                    $paramItemIds = $this->category->paramItems->pluck('id');

                    $byParamItems = ProductVariant::whereHas('paramItems', fn($q) =>
                        $q->whereIn('product_param_items.id', $paramItemIds)
                    )->pluck('id');

                    $byParametrs = ProductVariant::whereHas('parametrs', fn($q) =>
                        $q->whereIn('product_param_items.id', $paramItemIds)
                    )->pluck('id');

                    $this->product_ids = $byParamItems->merge($byParametrs)->unique()->values();
                    break;

                case 'variations':
                    $this->product_ids = $this->category->variations->pluck('id');
                    break;

                default:
                    $this->product_ids = $this->category->products->pluck("id");
                    break;
            }

            $this->type = "category";
        }

        if ($brand_slug) {
            $this->product_ids = Brand::where("slug", $brand_slug)
                ->with('products:id')
                ->first()
                ?->products->pluck("id") ?? collect();

            $this->type = "brand";
        }

        if ($products) {
            $this->product_ids = $products;
            $this->type = "products";
        }
    }

    public function getSortOptions()
    {
        return [
            "id:asc" => ["label" => "По умолчанию", "icon" => "M3 4.5h14.25..."],
            "price:asc" => ["label" => "Подешевле", "icon" => "M3 4.5h14.25..."],
            "price:desc" => ["label" => "Подороже", "icon" => "M3 4.5h14.25..."],
            "name:asc" => ["label" => "По названию А-Я", "icon" => "M3 4.5h14.25..."],
            "name:desc" => ["label" => "По названию Я-А", "icon" => "M3 4.5h14.25..."],
        ];
    }

    public function updateSort($sort)
    {
        $this->sort = $sort;
    }

    #[On("filters-changed")]
    public function updateFilters($filters)
    {
        $this->filters = $filters;
        $this->resetPage();
    }

    public function render()
    {
        $builder = ProductVariant::filter($this->filters)
            ->sort([$this->sort])
            ->with(['parametrs', 'paramItems']);

        if ($this->type === 'products' || in_array($this->category?->type, ['variations', 'filter'])) {
            $builder->whereIn('id', $this->product_ids);
        } else {
            $builder->whereIn('product_id', $this->product_ids);
        }

        return view("livewire.catalog.items", [
            "products" => $builder,
            "mode" => $this->displayMode,
        ]);
    }

    public function changeDisplayMode($mode)
    {
        $this->resetPage();
        $this->displayMode = $mode;
    }

    public function resetFilters()
    {
        $this->filters = [];
    }
}
