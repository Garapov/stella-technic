<?php

namespace App\Livewire\Catalog;

use Livewire\Component;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;

class Filter extends Component
{
    public $products;

    #[Url]
    public $filters = [];

    public $availableFilters = [];
    public $parameters = [];
    public $brands = [];
    public $batches = [];

    public $priceRange = [0, 100000];
    public $startPriceRange = [0, 100000];
    public $priceRangeToDisplay = [0, 100000];

    public $selectedParams = []; // [paramItemId => 'paramItems' | 'parametrs']
    public $selectedBrands = [];
    public $selectedBatches = [];

    public function mount($products = new Collection())
    {
        $this->products = $products;

        if (
            isset($this->filters["paramItems"]) &&
            !empty($this->filters["paramItems"]['$related'])
        ) {
            foreach ($this->filters["paramItems"]['$related'] as $id) {
                $this->selectedParams[$id] = 'paramItems';
            }
        }

        if (
            isset($this->filters["parametrs"]) &&
            !empty($this->filters["parametrs"]['$related'])
        ) {
            foreach ($this->filters["parametrs"]['$related'] as $id) {
                $this->selectedParams[$id] = 'parametrs';
            }
        }

        $this->calculatePriceRangeOnMount();
        $this->initializeBrands();
        $this->initializeParameters();

        $this->dispatch("filters-changed", filters: $this->filters);
    }

    public function updatedSelectedBatches()
    {
        if (empty($this->selectedBatches)) {
            unset($this->filters["batch_id"]);
        } else {
            $this->filters["batch_id"] = [
                '$in' => $this->selectedBatches,
            ];
        }
        $this->dispatch("filters-changed", filters: $this->filters);
    }

    public function updatedSelectedBrands()
    {
        if (empty($this->selectedBrands)) {
            unset($this->filters['$hasbrand']);
        } else {
            $this->filters['$hasbrand'] = $this->selectedBrands;
        }
        $this->dispatch("filters-changed", filters: $this->filters);
    }

    public function updatedSelectedParams()
    {
        $primary = [];
        $secondary = [];

        foreach ($this->selectedParams as $id => $source) {
            if ($source === 'paramItems') {
                $primary[] = (int) $id;
            } elseif ($source === 'parametrs') {
                $secondary[] = (int) $id;
            }
        }

        if (!empty($primary)) {
            $this->filters['paramItems'] = ['$related' => $primary];
        } else {
            unset($this->filters['paramItems']);
        }

        if (!empty($secondary)) {
            $this->filters['parametrs'] = ['$related' => $secondary];
        } else {
            unset($this->filters['parametrs']);
        }

        $this->dispatch("filters-changed", filters: $this->filters);
    }

    public function updatedPriceRange()
    {
        $this->filters["price"]['$between'] = $this->priceRange;
        $this->dispatch("filters-changed", filters: $this->filters);
    }

    public function calculatePriceRangeOnMount()
    {
        if (isset($this->filters["price"]['$between'])) {
            $this->startPriceRange = $this->priceRangeToDisplay =
                $this->filters["price"]['$between'];
        } else {
            $this->startPriceRange = $this->priceRangeToDisplay = [
                $this->products->min("price"),
                $this->products->max("price"),
            ];
        }

        $this->priceRange = [
            $this->products->min("price"),
            $this->products->max("price"),
        ];
    }

    protected function initializeBrands()
    {
        $this->brands = $this->products
            ->map(fn($product) => $product->product->brand ?? null)
            ->filter()
            ->unique("id")
            ->values()
            ->all();
    }

    protected function initializeBatches()
    {
        $this->batches = $this->products
            ->map(fn($product) => $product->batch ?? null)
            ->filter()
            ->unique("id")
            ->values()
            ->all();
    }

    protected function initializeParameters()
    {
        $allParams = collect();

        $primaryParams = $this->products->flatMap(fn($product) => $product->paramItems ?? []);
        $secondaryParams = $this->products->flatMap(fn($product) => $product->parametrs ?? []);

        $mergedParams = $primaryParams
            ->merge($secondaryParams)
            ->filter(fn($paramItem) =>
                $paramItem &&
                $paramItem->productParam &&
                $paramItem->productParam->allow_filtering &&
                $paramItem->productParam->name
            )
            ->map(function ($item) use ($primaryParams) {
                return [
                    'id' => $item->id,
                    'title' => $item->title ?? '',
                    'value' => $item->value ?? '',
                    'param_id' => $item->product_param_id,
                    'type' => $item->productParam->type ?? 'default',
                    'group' => $item->productParam->name,
                    'source' => $primaryParams->contains($item) ? 'paramItems' : 'parametrs',
                ];
            });

        $this->parameters = $mergedParams->groupBy('group')->map(fn($items) =>
            $items->keyBy('id')
        );
    }

    public function render()
    {
        return view("livewire.catalog.filter");
    }

    public function toggleParam($id, $source)
    {
        if (isset($this->selectedParams[$id])) {
            unset($this->selectedParams[$id]);
        } else {
            $this->selectedParams[$id] = $source;
        }

        $this->updatedSelectedParams(); // вручную вызываем фильтрацию
}

    public function updatedFilters()
    {
        $this->dispatch("filters-changed", filters: $this->filters);
    }

    public function resetFilters()
    {
        $this->filters = [];
        $this->selectedParams = [];
        $this->selectedBrands = [];
        $this->selectedBatches = [];

        $this->startPriceRange = $this->priceRangeToDisplay = [
            $this->products->min("price"),
            $this->products->max("price"),
        ];

        $this->dispatch("filters-changed", filters: $this->filters);
    }
}
