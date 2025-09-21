<?php

namespace App\Livewire\General;

use App\Models\ProductCategory;
use App\Models\ProductVariant;
use Datlechin\FilamentMenuBuilder\Models\Menu;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;
use Spatie\SchemaOrg\Schema;

class Header extends Component
{
    public $categories;
    public $allCategoryIds;
    public $variationCounts;
    public $minPrices;
    public $organization;

     public function mount()
    {
        $this->categories = ProductCategory::where('parent_id', '-1')->with([
            'categories',
            'products',
            'products.variants',
            'products.variants.paramItems',
            'products.variants.parametrs',
            'products.variants.paramItems.productParam',
            'products.variants.parametrs.productParam',
            'products.brand',
            'products.categories',
        ])->get();

        $this->organization = Schema::organization()
            ->name(setting("site_name") ?? env('APP_NAME'))
            ->url(url('/'))
            ->logo(Storage::disk(config('filesystems.default'))->url(setting('site_logo')))
            ->email(setting("site_email") ?? '')
            ->description(setting("site_description") ?? '')
            ->telephone([
                setting('site_phone') ?? '',
                setting('site_secondphone') ?? ''
            ])
            // ->address(
            //     Schema::postalAddress()
            //         ->streetAddress('ул. Амир Темур, 10')
            //         ->addressLocality('Ташкент')
            //         ->addressCountry('UZ')
            // )
            ->toScript();

        $this->allCategoryIds = ProductCategory::all()->pluck('id')->toArray();

        // Получаем счётчики вариантов
        // $this->variationCounts = ProductVariant::selectRaw('count(*) as count, product_product_category.product_category_id')
        //     ->join('products', 'products.id', '=', 'product_variants.product_id')
        //     ->join('product_product_category', 'products.id', '=', 'product_product_category.product_id')
        //     ->whereIn('product_product_category.product_category_id', $this->allCategoryIds)
        //     ->groupBy('product_product_category.product_category_id')
        //     ->pluck('count', 'product_product_category.product_category_id');


        // Получаем минимальные цены
        // $this->minPrices = ProductVariant::selectRaw('MIN(COALESCE(product_variants.new_price, product_variants.price)) as min_price, product_product_category.product_category_id')
        //     ->join('products', 'products.id', '=', 'product_variants.product_id')
        //     ->join('product_product_category', 'products.id', '=', 'product_product_category.product_id')
        //     ->whereIn('product_product_category.product_category_id', $this->allCategoryIds)
        //     ->groupBy('product_product_category.product_category_id')
        //     ->pluck('min_price', 'product_product_category.product_category_id');
    }

    public function render()
    {
        // dd(ProductCategory::all());
        return view('livewire.general.header', [
            'categories' => $this->categories,
            // 'variationCounts' => $this->variationCounts,
            'minPrices' => $this->minPrices,
            // 'allCategoryIds' => $this->allCategoryIds,
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
