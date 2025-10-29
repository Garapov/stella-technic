<?php

namespace App\Livewire\General;

use App\Models\ProductCategory;
use App\Models\ProductVariant;
use Datlechin\FilamentMenuBuilder\Models\Menu;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;
use Spatie\SchemaOrg\Schema;
use Illuminate\Support\Facades\Cache;

class Header extends Component
{
    public $categories;
    public $organization;

     public function mount()
    {

         $this->categories = Cache::rememberForever('header:categories', function () {
            return ProductCategory::where('parent_id', '-1')->with([
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
        });
       

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
            ->toScript();
    }

    public function render()
    {
        // dd(ProductCategory::all());
        return view('livewire.general.header', [
            'categories' => $this->categories,
            'topmenu' => Cache::rememberForever('menus:top_menu', function () { return Menu::location('top_menu'); }),
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
