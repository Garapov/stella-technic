<?php

namespace App\Livewire\Catalog;

use App\Models\ProductVariant;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Url;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

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
        $firstSelectedId = null;
        foreach (["paramItems", "parametrs"] as $filterKey) {
            if (isset($this->filters[$filterKey])) {
                foreach (['$hasid', '$related', '$includes'] as $type) {
                    if (!empty($this->filters[$filterKey][$type])) {
                        foreach ($this->filters[$filterKey][$type] as $id) {
                            $this->selectedParams[$id] = $filterKey;
                            if ($firstSelectedId === null) {
                                $firstSelectedId = $id;
                            }
                        }
                    }
                }
            }
        }
        if ($firstSelectedId !== null) {
            $this->setFirstSelectedGroupIds($firstSelectedId);
        } else {
            $this->firstSelectedGroup = [];
        }

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

        /**
     * Определяет и возвращает массив id параметров первой выбранной группы по id выбранного параметра.
     * Если filters пустой — возвращает все id из группы, где находится переданный id.
     * Если filters не пустой — ищет id первого выбранного параметра и возвращает все id из его группы.
     * Записывает результат в $this->firstSelectedGroup.
     */
    public function setFirstSelectedGroupIds($paramId)
    {
        
        if (empty($this->filters)) {
            $this->firstSelectedGroup = [];
        }
        if (!$this->firstSelectedGroup) {        

            foreach ($this->parameters as $groupName => $params) {
                if (isset($this->parameters[$groupName]) && isset($this->parameters[$groupName][$paramId])) {
                    $this->firstSelectedGroup = $this->parameters[$groupName]->keys()->toArray();
                }
            }
        };

        
        
        return $this->firstSelectedGroup;
    }


    public function checkParamsvAilability($change = false)
    {
        
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

        // dd($filteredIds);

        // объединяем id первой группы и id из фильтрации, оставляя только уникальные
        $this->availableFilters = array_unique(array_merge($this->firstSelectedGroup, $filteredIds));

        // if ($change) {
        //     dd([$this->firstSelectedGroup, $filteredIds]);
        // }

        // dd($this->availableFilters);

        // dd([$this->firstSelectedGroup, $filteredIds, $this->availableFilters]);
        return $this->availableFilters;
        
    }

    public function updatedSelectedParams()
    {
        // dd($this->filters, $this->selectedParams, $this->firstSelectedGroup);
        if (isset($this->filters['paramItems'])) {
            unset($this->filters['paramItems']);
        };
        if (isset($this->filters['parametrs'])) {
            unset($this->filters['parametrs']);
        };
        foreach ($this->selectedParams as $id => $source) {
            if (in_array($id, $this->firstSelectedGroup)) {
                $this->filters[$source]['$hasid'][] = (int) $id;
            } else {
                $this->filters[$source]['$related'][] = (int) $id;
            }
        }

        $this->dispatch("filters-changed", filters: $this->filters, availableParams: $this->checkParamsvAilability());
    }

    function filterParamsByValues(array $params, array $values): array
    {
        $min = min($values);
        $max = max($values);

        return array_filter($params, function ($param) use ($min, $max) {
            return (float)$param['value'] >= $min && (float)$param['value'] <= $max;
        });
    }

    public function setSliderFilter($items, $values, $paramName) {
        $params = collect($this->filterParamsByValues($items, $values))->sortBy('value');

        // dd($params);    
        $key = Str::snake(Str::of($paramName)->transliterate()->toString());
        $this->filters['$includes'][$key] = [];

        $this->filters['$includes'][$key] = $params->pluck('id')->toArray();

        foreach($params as $param) {
            $this->setFirstSelectedGroupIds($param['id']);
        }

        // dd($this->filters);
        $this->dispatch("filters-changed", filters: $this->filters, availableParams: $this->checkParamsvAilability(true));
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
        $mergedParams = collect([]);
        if (!Cache::has('filter_params:' . url()->current())) {
            $primaryParams = $this->products->flatMap(fn($product) => $product->paramItems ?? []);
            $secondaryParams = $this->products->flatMap(fn($product) => $product->parametrs ?? []);

            $mergedParams = $primaryParams
                ->merge($secondaryParams)
                ->sortBy('productParam.sort')
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
                        'sort' => $item->sort,
                        'source' => $primaryParams->contains($item) ? 'paramItems' : 'parametrs',
                    ];
                });
        }
        $this->parameters = Cache::rememberForever('filter_params:' . url()->current(), function () use ($mergedParams) {
            return $mergedParams->groupBy('group')->map(fn($items) =>
                $items->keyBy('id')
            );
        });
    }

    public function render()
    {   
        return view("livewire.catalog.filter");
    }

    public function toggleParam($id, $source)
    {
        // dd($source);
        if (isset($this->selectedParams[$id])) {
            unset($this->selectedParams[$id]);
        } else {
            $this->selectedParams[$id] = $source;
        }
        
        if (empty($this->selectedParams)) {
            $this->firstSelectedGroup = [];
        } else {
            $this->setFirstSelectedGroupIds($id);
        }
        
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
        $this->firstSelectedGroup = [];

        $this->startPriceRange = $this->priceRangeToDisplay = [
            $this->products->min("price"),
            $this->products->max("price"),
        ];

        $this->js('setTimeout(() => {
            window.location.reload();
        }, 100);'); 
    }
}
