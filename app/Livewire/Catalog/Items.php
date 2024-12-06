<?php

namespace App\Livewire\Catalog;

use App\Models\Category;
use App\Models\ProductCategory;
use App\Models\ProductParam;
use App\Models\ProductParamItem;
use Livewire\Component;

class Items extends Component
{
    public $isFiltersOpened = false;
    public $isSortingOpened = false;
    public $selectedVariations = [];

    public ?ProductCategory $category = null;

    public function mount($slug)
    {
        $this->category = ProductCategory::where('slug', $slug)->first();
    }

    public function getProductsProperty()
    {
        $query = $this->category->products();

        if (!empty($this->selectedVariations)) {
            $query->whereHas('paramItems', function ($query) {
                $query->whereIn('product_param_items.id', $this->selectedVariations);
            });
        }

        return $query->get();
    }

    public function getAvailableFiltersProperty()
    {
        $categoryProductIds = $this->category->products->pluck('id');

        return ProductParam::where('allow_filtering', true)
            ->whereHas('params.products', function ($query) use ($categoryProductIds) {
                $query->whereIn('products.id', $categoryProductIds);
            })
            ->with(['params' => function ($query) use ($categoryProductIds) {
                $query->whereHas('products', function ($q) use ($categoryProductIds) {
                    $q->whereIn('products.id', $categoryProductIds);
                });
            }])
            ->get();
    }

    public function render()
    {
        return view('livewire.catalog.items');
    }

    public function toggleFilters()
    {
        $this->isFiltersOpened = !$this->isFiltersOpened;
    }

    public function closeFilters()
    {
        $this->isFiltersOpened = false;
    }

    public function toggleSorting()
    {
        $this->isSortingOpened = !$this->isSortingOpened;
    }

    public function closeSorting()
    {
        $this->isSortingOpened = false;
    }
}
