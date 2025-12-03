<?php

namespace App\Livewire\Catalog;

use App\Models\ProductVariant;
use App\Services\ProductSelector;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Url;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\On;

#[Lazy()]
class ItemsLazyFilter extends Component
{

    public $category = null;

    #[Url()]
    public array $filters = [
        'parametrs' => [
            '$hasid' => [],
            '$first' => [],
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
        // $this->refresh();
        // Log::info('Filters changed', $this->filters);
    }

    public function variationsBuilder()
    {
        return ProductVariant::whereIn(
            'id',
            Cache::remember('catalog:all_products:' . $this->category->slug, 60, function () {
                return $this->selector->fromCategory($this->category)->where('is_hidden', false)->pluck('id')->toArray();
            })
        );
    }

    function filterParamsByValues(array $params, array $values): array
    {
        $min = min($values);
        $max = max($values);

        return array_filter($params, function ($param) use ($min, $max) {
            return (float) $param['value'] >= $min && (float) $param['value'] <= $max;
        });
    }

    public function setSliderFilter($value, $name)
    {

        $this->filters['$includes'][$name] = $value;

        // foreach($params as $param) {
        //     $this->setFirstSelectedGroupIds($param['id']);
        // }

        // dd($this->filters);
        $this->dispatch("filters-changed", filters: $this->filters);
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
            return Cache::rememberForever('filter:variations:' . $this->category->slug, function () {
                return $this->variationsBuilder()->get();
            });
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
        if ($this->category) {

            return Cache::rememberForever('filter:paramGroups:' . $this->category->slug, function () {
                return $this->variations->flatMap(fn($variant) => $variant->parametrs->concat($variant->paramItems))->flatten()->filter(fn($item) => $item->productParam->allow_filtering)->unique('id')->sortBy('productParam.sort')->groupBy('productParam.name')->filter(fn($group) => count($group) > 1);
            });
        }
        return collect();
    }

    #[Computed()]
    public function filteredVariations()
    {
        return $this->variationsBuilder()->filter($this->filters)->get();
    }

    #[Computed()]
    public function availableParams()
    {
        return $this->filteredVariations->flatMap(fn($variant) => $variant->parametrs->concat($variant->paramItems))->flatten()->filter(fn($item) => $item->productParam->allow_filtering)->pluck('id')->unique()->toArray();
    }

    #[On('filter_reset')]
    public function resetFilters()
    {
        $this->filters = [
            'parametrs' => [
                '$hasid' => [],
                '$first' => [],
            ],
        ];
    }

    public function placeholder()
    {
        // sleep(4);
        return view('placeholders.catalog.items-lazy-filter');
    }
}
