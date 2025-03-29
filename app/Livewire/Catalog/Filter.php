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
    public $debugData = [];
    public $priceRange = [0, 100000];
    public $startPriceRange = [0, 100000];
    public $priceRangeToDisplay = [0, 100000];
    public $selectedParams = [];

    public function mount($products = new Collection())
    {
        $this->products = $products;

        if (
            isset($this->filters["paramItems"]) &&
            !empty($this->filters["paramItems"]) &&
            $this->filters["paramItems"]['$related']
        ) {
            $this->selectedParams = $this->filters["paramItems"]['$related'];
        }

        $this->calculatePriceRangeOnMount();

        $this->dispatch("filters-changed", filters: $this->filters);

        $this->initializeParameters();
    }
    public function updatedSelectedParams()
    {
        if (
            empty($this->selectedParams) &&
            isset($this->filters["paramItems"])
        ) {
            unset($this->filters["paramItems"]);
        } else {
            $this->filters = array_merge($this->filters, [
                "paramItems" => ['$related' => $this->selectedParams],
            ]);
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
        if (
            isset($this->filters["price"]) &&
            $this->filters["price"]['$between']
        ) {
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

    protected function initializeParameters()
    {
        $this->parameters = $this->products
            ->flatMap(function ($product) {
                return $product->paramItems ?? collect();
            })
            ->filter(function ($paramItem) {
                // Сначала проверяем существование productParam
                if (!$paramItem || !$paramItem->productParam) {
                    return false;
                }
                // Затем проверяем allow_filtering
                return $paramItem->productParam->allow_filtering &&
                    $paramItem->productParam->name;
            })
            ->groupBy(function ($paramItem) {
                return $paramItem->productParam->name;
            })
            ->map(function ($items) {
                return $items->unique("id")->mapWithKeys(function ($item) {
                    return [
                        $item->id => [
                            "title" => $item->title ?? "",
                            "value" => $item->value ?? "",
                            "param_id" => $item->product_param_id,
                            "type" => $item->productParam->type ?? "default",
                        ],
                    ];
                });
            });
    }

    public function render()
    {
        return view("livewire.catalog.filter");
    }

    public function updatedFilters()
    {
        $this->dispatch("filters-changed", filters: $this->filters);
    }
}
