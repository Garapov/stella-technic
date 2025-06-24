<?php

namespace App\Livewire\Main;

use App\Models\Feature;
use App\Models\ProductCategory;
use App\Models\ProductVariant;
use Livewire\Component;

class Features extends Component
{
    public $categories;
    public $allCategoryIds;
    public $variationCounts;
    public $minPrices;

    public function mount()
    {
        $this->categories = ProductCategory::where('parent_id', '-1')->with([
            'categories',
            'products'
        ])->get();

        $this->allCategoryIds = ProductCategory::all()->pluck('id')->toArray();

        // Получаем счётчики вариантов
        $this->variationCounts = ProductVariant::selectRaw('count(*) as count, product_product_category.product_category_id')
            ->join('products', 'products.id', '=', 'product_variants.product_id')
            ->join('product_product_category', 'products.id', '=', 'product_product_category.product_id')
            ->whereIn('product_product_category.product_category_id', $this->allCategoryIds)
            ->groupBy('product_product_category.product_category_id')
            ->pluck('count', 'product_product_category.product_category_id');
    }
    
    public function render()
    {
        return view('livewire.main.features', [
            'features' => Feature::all(),
            'categories' => $this->categories,
            'counts' => $this->variationCounts,
            'minPrices' => $this->minPrices,
            'allCategoryIds' => $this->allCategoryIds,
        ]);
    }
}
