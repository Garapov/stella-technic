<?php

namespace App\Livewire\Catalog;

use Livewire\Component;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;

class Filter extends Component
{

    public $products;
    #[Url()]
    public $filters = array();
    public $availableFilters = [];
    public $parameters = [];
    public $debugData = [];

    public function mount($products = new Collection())
    {
        $this->products = $products;
        
        // Убедимся, что все отношения загружены
        if (!$this->products->first()?->relationLoaded('paramItems')) {
            $this->products->load(['paramItems.productParam']);
        }
        $this->dispatch('filters-changed',  filters: $this->filters);
        
        $this->initializeParameters();
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
                return $paramItem->productParam->allow_filtering 
                    && $paramItem->productParam->name;
            })
            ->groupBy(function ($paramItem) {
                return $paramItem->productParam->name;
            })
            ->map(function ($items) {
                return $items
                    ->unique('id')
                    ->mapWithKeys(function ($item) {
                        return [
                            $item->id => [
                                'title' => $item->title ?? '',
                                'value' => $item->value ?? '',
                                'param_id' => $item->product_param_id,
                                'type' => $item->productParam->type ?? 'default'
                            ]
                        ];
                    });
            });

        // Отладочная информация
        logger()->info('Parameters structure:', [
            'first_param_item' => $this->products->first()?->paramItems->first()?->toArray(),
            'first_product_param' => $this->products->first()?->paramItems->first()?->productParam?->toArray()
        ]);

        $this->debugData = [
            'products_count' => $this->products->count(),
            'first_product' => $this->products->first() ? $this->products->first()->toArray() : null,
            'parameters' => $this->parameters->toArray()
        ];
    }

    public function render()
    {
        return view('livewire.catalog.filter');
    }

    public function updatedFilters()
    {
        $this->dispatch('filters-changed',  filters: $this->filters);
    }
} 