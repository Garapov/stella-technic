<?php

namespace App\Livewire\Catalog;

use Livewire\Component;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;

class Filter extends Component
{

    public $products;
    #[Url()]
    public $filters = [];
    public $availableFilters = [];
    public $parameters = [];
    public $debugData = [];

    public function mount($products = new Collection())
    {
        $this->products = $products;
        
        // Загружаем отношения и сразу фильтруем по allow_filtering
        if (!$this->products->first()?->relationLoaded('paramItems')) {
            $this->products->load(['paramItems.productParam' => function($query) {
                $query->where('allow_filtering', true);
            }]);
        }
        
        $this->initializeParameters();
    }

    protected function initializeParameters()
    {
        // Добавим отладку для просмотра структуры первого продукта
        if ($this->products->first()) {
            logger()->info('First product structure:', [
                'product' => $this->products->first()->toArray(),
            ]);
        }

        $this->parameters = $this->products
            ->flatMap(function ($product) {
                return $product->paramItems ?? collect();
            })
            ->filter(function ($paramItem) {
                return $paramItem 
                    && $paramItem->productParam 
                    && $paramItem->productParam->name
                    && $paramItem->productParam->allow_filtering;
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
                                'type' => $item->productParam->type ?? 'default',
                                'allow_filtering' => $item->productParam->allow_filtering
                            ]
                        ];
                    });
            });

        // Добавим отладочную информацию
        logger()->info('Filtered parameters:', [
            'params' => $this->parameters->map(function($items) {
                return $items->map(function($item) {
                    return [
                        'id' => $item['param_id'],
                        'allow_filtering' => $item['allow_filtering']
                    ];
                });
            })->toArray()
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