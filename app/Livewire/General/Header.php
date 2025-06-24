<?php

namespace App\Livewire\General;

use App\Models\ProductCategory;
use App\Models\ProductVariant;
use Datlechin\FilamentMenuBuilder\Models\Menu;
use Livewire\Attributes\On;
use Livewire\Component;

class Header extends Component
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


        // Получаем минимальные цены
        $this->minPrices = ProductVariant::selectRaw('MIN(COALESCE(product_variants.new_price, product_variants.price)) as min_price, product_product_category.product_category_id')
            ->join('products', 'products.id', '=', 'product_variants.product_id')
            ->join('product_product_category', 'products.id', '=', 'product_product_category.product_id')
            ->whereIn('product_product_category.product_category_id', $this->allCategoryIds)
            ->groupBy('product_product_category.product_category_id')
            ->pluck('min_price', 'product_product_category.product_category_id');
    }

    public function render()
    {
        // dd(ProductCategory::all());
        return view('livewire.general.header', [
            'categories' => $this->categories,
            'variationCounts' => $this->variationCounts,
            'minPrices' => $this->minPrices,
            'allCategoryIds' => $this->allCategoryIds,
            'topmenu' => Menu::location('top_menu')
        ]);
    }
    #[On('check-if-products-exists')]
    public function checkIfFavoritesExists($products)
    {
        if (!is_array($products)) return [];
        $existingProducts = ProductVariant::whereIn('id', $products)->get()->pluck('id')->toArray();
        $this->dispatch('exact-favorites', $existingProducts);
    }
}
