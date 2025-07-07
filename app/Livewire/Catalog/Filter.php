<?php

namespace App\Livewire\Catalog;

use App\Models\ProductVariant;
use Livewire\Component;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
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
    public $firstSelectedGroup = null; // Для совместимости с шаблоном (primary)
    public $firstSelectedGroupPrimary = null; // Для paramItems
    public $firstSelectedGroupSecondary = null; // Для parametrs
    public $selectedBrands = [];
    public $selectedBatches = [];

    public function mount($products = new Collection())
        
    {
        $this->products = $products;

        $this->calculatePriceRangeOnMount();
        $this->initializeBrands();
        $this->initializeParameters();

        // Восстанавливаем выбранные параметры и первую группу строго по очередности в $this->filters
        $this->selectedParams = [];
        $this->firstSelectedGroupPrimary = null;
        $this->firstSelectedGroupSecondary = null;
        $firstGroupFound = false;
        foreach (["paramItems", "parametrs"] as $filterKey) {
            if (isset($this->filters[$filterKey])) {
                foreach (['$hasid', '$related'] as $type) {
                    if (!empty($this->filters[$filterKey][$type])) {
                        foreach ($this->filters[$filterKey][$type] as $id) {
                            $this->selectedParams[$id] = $filterKey;
                            if (!$firstGroupFound) {
                                // Найти имя группы по id
                                foreach ($this->parameters as $groupName => $params) {
                                    if (isset($params[$id])) {
                                        if ($filterKey === 'paramItems') {
                                            $this->firstSelectedGroupPrimary = $groupName;
                                        } else {
                                            $this->firstSelectedGroupSecondary = $groupName;
                                        }
                                        $firstGroupFound = true;
                                        break 2;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $this->firstSelectedGroup = $this->firstSelectedGroupPrimary;

        Log::info('[Filter] mount: параметры при загрузке', [
            'selectedParams' => $this->selectedParams,
            'firstSelectedGroupPrimary' => $this->firstSelectedGroupPrimary,
            'firstSelectedGroupSecondary' => $this->firstSelectedGroupSecondary,
            'filters' => $this->filters,
            'availableFilters' => $this->availableFilters,
            'parameters' => $this->parameters,
        ]);

        $this->dispatch("filters-changed", filters: $this->filters, availableParams: $this->checkParamsvAilability());
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
        $this->dispatch("filters-changed", filters: $this->filters, availableParams: $this->checkParamsvAilability());
    }

    public function updatedSelectedBrands()
    {
        if (empty($this->selectedBrands)) {
            unset($this->filters['$hasbrand']);
        } else {
            $this->filters['$hasbrand'] = $this->selectedBrands;
        }
        $this->dispatch("filters-changed", filters: $this->filters, availableParams: $this->checkParamsvAilability());
    }


    public function checkParamsvAilability()
    {
        // 1. Определяем первую выбранную группу
        $firstGroup = null;
        if ($this->firstSelectedGroupPrimary) {
            $firstGroup = $this->firstSelectedGroupPrimary;
        } elseif ($this->firstSelectedGroupSecondary) {
            $firstGroup = $this->firstSelectedGroupSecondary;
        }

        $firstGroupIds = [];
        if ($firstGroup && isset($this->parameters[$firstGroup])) {
            $firstGroupIds = array_keys($this->parameters[$firstGroup]->toArray());
        }

        // id параметров товаров после фильтрации
        $filteredIds = ProductVariant::filter($this->filters)
            ->with(['paramItems', 'parametrs'])
            ->get()
            ->flatMap(function ($variant) {
                $paramItemsIds = $variant->paramItems->pluck('id')->toArray();
                $parametrsIds = $variant->parametrs->pluck('id')->toArray();
                return array_merge($paramItemsIds, $parametrsIds);
            })
            ->unique()
            ->values()
            ->toArray();

        // объединяем id первой группы и id из фильтрации, оставляя только уникальные
        $this->availableFilters = array_unique(array_merge($firstGroupIds, $filteredIds));
        return $this->availableFilters;
    }

    public function updatedSelectedParams()
    {
        $primary_related = [];
        $primary_hasid = [];
        $secondary_related = [];
        $secondary_hasid = [];

        // Определяем первую выбранную группу отдельно для paramItems и parametrs
        $firstSelectedGroupPrimary = null;
        $firstSelectedGroupSecondary = null;

        // Найти первый выбранный paramItems и parametrs
        foreach ($this->selectedParams as $id => $source) {
            if ($firstSelectedGroupPrimary === null && $source === 'paramItems') {
                foreach ($this->parameters as $groupName => $params) {
                    if (isset($params[$id])) {
                        $firstSelectedGroupPrimary = $groupName;
                        break 2;
                    }
                }
            }
        }
        foreach ($this->selectedParams as $id => $source) {
            if ($firstSelectedGroupSecondary === null && $source === 'parametrs') {
                foreach ($this->parameters as $groupName => $params) {
                    if (isset($params[$id])) {
                        $firstSelectedGroupSecondary = $groupName;
                        break 2;
                    }
                }
            }
        }

        Log::info('[Filter] updatedSelectedParams', [
            'selectedParams' => $this->selectedParams,
            'firstSelectedGroupPrimary' => $firstSelectedGroupPrimary,
            'firstSelectedGroupSecondary' => $firstSelectedGroupSecondary,
            'parameters' => $this->parameters,
        ]);

        Log::info('[Filter] availableFilters before split', [
            'availableFilters' => $this->availableFilters
        ]);

        // Для совместимости с шаблоном оставим firstSelectedGroup = primary
        $this->firstSelectedGroup = $firstSelectedGroupPrimary;

        // 1. Все параметры из первой выбранной группы (primary/secondary) — в $hasid
        // 2. Остальные выбранные параметры — в $related, только если они есть в availableFilters
        $available = $this->checkParamsvAilability();
        // Явно обновляем $availableFilters для корректной работы Blade после выбора
        $this->availableFilters = $available;
        // primary
        if ($firstSelectedGroupPrimary && isset($this->parameters[$firstSelectedGroupPrimary])) {
            foreach ($this->parameters[$firstSelectedGroupPrimary] as $id => $param) {
                if (isset($this->selectedParams[$id]) && $this->selectedParams[$id] === 'paramItems') {
                    $primary_hasid[] = (int) $id;
                }
            }
        }
        // secondary
        if ($firstSelectedGroupSecondary && isset($this->parameters[$firstSelectedGroupSecondary])) {
            foreach ($this->parameters[$firstSelectedGroupSecondary] as $id => $param) {
                if (isset($this->selectedParams[$id]) && $this->selectedParams[$id] === 'parametrs') {
                    $secondary_hasid[] = (int) $id;
                }
            }
        }
        // Остальные выбранные параметры (не из первой группы), только если они доступны
        foreach ($this->selectedParams as $id => $source) {
            if ($source === 'paramItems') {
                $inFirstGroup = $firstSelectedGroupPrimary && isset($this->parameters[$firstSelectedGroupPrimary][$id]);
                if (!$inFirstGroup && in_array((int)$id, $available)) {
                    $primary_related[] = (int) $id;
                }
            } elseif ($source === 'parametrs') {
                $inFirstGroup = $firstSelectedGroupSecondary && isset($this->parameters[$firstSelectedGroupSecondary][$id]);
                if (!$inFirstGroup && in_array((int)$id, $available)) {
                    $secondary_related[] = (int) $id;
                }
            }
        }

        Log::info('[Filter] updatedSelectedParams split', [
            'primary_hasid' => $primary_hasid,
            'primary_related' => $primary_related,
            'secondary_hasid' => $secondary_hasid,
            'secondary_related' => $secondary_related,
        ]);


        // Формируем фильтры с нужными ключами: одновременно $hasid и $related, если есть
        if (!empty($primary_hasid) || !empty($primary_related)) {
            $this->filters['paramItems'] = [];
            if (!empty($primary_hasid)) {
                $this->filters['paramItems']['$hasid'] = $primary_hasid;
            }
            if (!empty($primary_related)) {
                $this->filters['paramItems']['$related'] = $primary_related;
            }
        } else {
            unset($this->filters['paramItems']);
        }

        if (!empty($secondary_hasid) || !empty($secondary_related)) {
            $this->filters['parametrs'] = [];
            if (!empty($secondary_hasid)) {
                $this->filters['parametrs']['$hasid'] = $secondary_hasid;
            }
            if (!empty($secondary_related)) {
                $this->filters['parametrs']['$related'] = $secondary_related;
            }
        } else {
            unset($this->filters['parametrs']);
        }

        Log::info('[Filter] updatedSelectedParams filters', [
            'filters' => $this->filters
        ]);

        Log::info('[Filter] availableFilters after split', [
            'availableFilters' => $this->availableFilters
        ]);

        $this->dispatch("filters-changed", filters: $this->filters, availableParams: $this->checkParamsvAilability());
    }

    public function updatedPriceRange()
    {
        $this->filters["price"]['$between'] = $this->priceRange;
        $this->dispatch("filters-changed", filters: $this->filters, availableParams: $this->checkParamsvAilability());
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
        Log::info('[Filter] toggleParam called', [
            'id' => $id,
            'source' => $source,
            'selectedParams_before' => $this->selectedParams
        ]);
        if (isset($this->selectedParams[$id])) {
            unset($this->selectedParams[$id]);
        } else {
            $this->selectedParams[$id] = $source;
        }
        Log::info('[Filter] toggleParam after', [
            'selectedParams_after' => $this->selectedParams
        ]);
        $this->updatedSelectedParams(); // вручную вызываем фильтрацию
    }

    public function updatedFilters()
    {
        $this->dispatch("filters-changed", filters: $this->filters, availableParams: $this->checkParamsvAilability());
    }

    public function resetFilters()
    {
        $this->filters = [];
        $this->selectedParams = [];
        $this->selectedBrands = [];
        $this->selectedBatches = [];
        $this->availableFilters = [];

        $this->startPriceRange = $this->priceRangeToDisplay = [
            $this->products->min("price"),
            $this->products->max("price"),
        ];

        $this->js('setTimeout(() => {
            window.location.reload();
        }, 100);'); 
    }
}
