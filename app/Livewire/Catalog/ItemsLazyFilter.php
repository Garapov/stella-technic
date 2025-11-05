<?php

namespace App\Livewire\Catalog;

use App\Models\ProductVariant;
use App\Services\ProductSelector;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Lazy()]
class ItemsLazyFilter extends Component
{

    public $category = null;

    #[Url()]
    public array $filters = [
        'parametrs' => [
            '$hasid' => [], // создаём массив под ключом "$hasid"
        ],
    ];

    protected ProductSelector $selector;

    public function boot(ProductSelector $selector)
    {
        $this->selector = $selector;
    }

    public function mount($category = null)
    {
        $this->category = $category;
    }

    public function render()
    {
        return view('livewire.catalog.items-lazy-filter');
    }

    public function updatedFilters()
    {
        // dd($this->filters);
        $this->dispatch("filters-changed", filters: $this->filters);
        // Log::info('Filters changed', $this->filters);
    }

    public function variationsBuilder()
    {
        return ProductVariant::whereIn('id', 
            Cache::remember('catalog:all_products:' . $this->category->slug, 60, function () {
                return $this->selector->fromCategory($this->category)->where('is_hidden', false)->pluck('id')->toArray();
            })
        );
    }

    public function setPrice($priceRange)
    {
        $this->filters['price']['$between'] = $priceRange;
        $this->dispatch("filters-changed", filters: $this->filters);
    }

    #[Computed()]
    public function variations()
    {
        if ($this->category) {
            return $this->variationsBuilder()->get();
        }
        return collect();
    }

    #[Computed()]
    public function filterParams()
    {
        return $this->filters;
    }

    #[Computed()]
    public function paramGroups()
    {
        $parametrs = [];

        if ($this->variations) {
            $parametrs = $this->variations->flatMap(fn($variant) => $variant->parametrs->concat($variant->paramItems))->flatten()->filter(fn ($item) =>  $item->productParam->allow_filtering)->unique('id')->sortBy('productParam.sort')->groupBy('productParam.name');
        }

        // dd($parametrs);

        return $parametrs;
    }

    #[Computed()]
    public function filteredVariations()
    {
        return $this->variationsBuilder()->filter($this->filters)->get();
    }

    #[Computed()]
    public function availableParams()
    {
        return $this->filteredVariations->flatMap(fn($variant) => $variant->parametrs->concat($variant->paramItems))->flatten()->filter(fn ($item) =>  $item->productParam->allow_filtering)->pluck('id')->unique()->toArray();
    }

    public function placeholder()
    {
        // sleep(4);
        return view('placeholders.catalog.items-lazy-filter');
    }
}
