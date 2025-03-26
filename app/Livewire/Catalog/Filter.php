<?php

namespace App\Livewire\Catalog;

use Livewire\Component;
use App\Models\ProductParamItem;
use App\Models\Brand;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class Filter extends Component
{
    public $category;
    public $selectedVariations = [];
    public $selectedParams = [];
    public $priceFrom = null;
    public $priceTo = null;
    public $selectedBrands = [];
    public $product_ids = [];
    public $items = null;

    protected $queryString = [
        "selectedParams" => ["except" => []],
        "priceFrom" => ["as" => "price_from", "except" => null],
        "priceTo" => ["as" => "price_to", "except" => null],
        "selectedBrands" => ["as" => "brand", "except" => []],
    ];

    public function mount($category = null, $product_ids = [], $items = null)
    {
        $this->category = $category;
        $this->product_ids = $product_ids;
        $this->items = $items;

        try {
            // Получаем диапазон цен
            $priceRange = $this->getPriceRangeProperty();
            
            // Устанавливаем значения только если они еще не установлены из URL
            if ($this->priceFrom === null) {
                $this->priceFrom = $priceRange->min_price ?? 0;
            }
            if ($this->priceTo === null) {
                $this->priceTo = $priceRange->max_price ?? 100000;
            }

            // Загрузка параметров из URL
            if (request()->has("selectedParams")) {
                $urlParams = request()->get("selectedParams");
                if (is_array($urlParams)) {
                    $this->selectedParams = $urlParams;
                    $this->selectedVariations = [];
                    foreach ($this->selectedParams as $paramValues) {
                        if (is_array($paramValues)) {
                            foreach ($paramValues as $value) {
                                $this->selectedVariations[] = (int) $value;
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // В случае ошибки устанавливаем значения по умолчанию
            $this->priceFrom = 0;
            $this->priceTo = 100000;
        }
    }

    public function updateParamSelection($paramName, $paramItemId)
    {
        // Логика обновления выбранных параметров
        // ... (код из оригинального компонента) ...

        $this->dispatch('filter-changed');
    }

    public function updatedPriceFrom()
    {
        if ($this->priceFrom > $this->priceTo) {
            $this->priceFrom = $this->priceTo;
        }
        $this->dispatch('filter-changed');
    }

    public function updatedPriceTo()
    {
        if ($this->priceTo < $this->priceFrom) {
            $this->priceTo = $this->priceFrom;
        }
        $this->dispatch('filter-changed');
    }

    public function resetFilters()
    {
        $this->selectedVariations = [];
        $this->selectedParams = [];
        $this->priceFrom = $this->priceRange->min_price;
        $this->priceTo = $this->priceRange->max_price;
        $this->selectedBrands = [];
        
        $this->dispatch('filter-reset');
        $this->dispatch('filter-changed');
    }

    // Геттеры для получения данных
    public function getPriceRangeProperty()
    {
        try {
            $query = ProductVariant::query();

            if ($this->items === "variants") {
                $query->whereIn('id', $this->product_ids);
            } elseif ($this->category) {
                $query->whereHas('product', function ($q) {
                    $q->whereHas('productCategories', function ($q) {
                        $q->where('product_categories.id', $this->category->id);
                    });
                });
            } elseif ($this->product_ids) {
                $query->whereHas('product', function ($q) {
                    $q->whereIn('id', $this->product_ids);
                });
            }

            $minPrice = $query->min(DB::raw('COALESCE(new_price, price)')) ?? 0;
            $maxPrice = $query->max(DB::raw('COALESCE(new_price, price)')) ?? 100000;

            return (object) [
                'min_price' => $minPrice,
                'max_price' => $maxPrice
            ];
        } catch (\Exception $e) {
            // В случае ошибки возвращаем объект с значениями по умолчанию
            return (object) [
                'min_price' => 0,
                'max_price' => 100000
            ];
        }
    }

    public function getFiltersProperty(): Collection
    {
        try {
            $filters = collect();

            // Добавляем параметры в фильтры
            $paramItems = $this->getAvailableParamItemsProperty();
            
            if ($paramItems) {
                foreach ($paramItems as $paramGroup) {
                    $filters->push([
                        'id' => $paramGroup['id'],
                        'name' => $paramGroup['name'],
                        'type' => 'param',
                        'items' => $paramGroup['items'] ?? collect(),
                    ]);
                }
            }

            // Добавляем фильтр по брендам
            $brands = $this->getAvailableBrandsProperty();
            if ($brands && $brands->count() > 0) {
                $filters->push([
                    'id' => 'brand',
                    'name' => 'Бренд',
                    'type' => 'brand',
                    'items' => $brands->map(function ($brand) {
                        return [
                            'id' => $brand->id,
                            'title' => $brand->name,
                            'selected' => in_array($brand->id, $this->selectedBrands),
                            'would_have_results' => $brand->would_have_results ?? true,
                        ];
                    }),
                ]);
            }

            return $filters;
        } catch (\Exception $e) {
            return collect();
        }
    }

    public function getAvailableParamItemsProperty(): Collection
    {
        try {
            $paramGroups = collect();
            
            // Получаем все параметры для текущей категории или для переданных вариантов
            $query = ProductVariant::query();

            if ($this->items === "variants") {
                $query->whereIn('id', $this->product_ids);
            } elseif ($this->category) {
                $query->whereHas('product', function ($q) {
                    $q->whereHas('productCategories', function ($q) {
                        $q->where('product_categories.id', $this->category->id);
                    });
                });
            } elseif ($this->product_ids) {
                $query->whereHas('product', function ($q) {
                    $q->whereIn('id', $this->product_ids);
                });
            }

            $variants = $query->with(['paramItems.productParam', 'parametrs.productParam'])->get();

            // Собираем все параметры
            foreach ($variants as $variant) {
                // Обрабатываем paramItems
                if ($variant->paramItems) {
                    foreach ($variant->paramItems as $paramItem) {
                        if (!$paramItem->productParam || !$paramItem->productParam->allow_filtering) {
                            continue;
                        }

                        $paramId = $paramItem->productParam->id;
                        if (!$paramGroups->has($paramId)) {
                            $paramGroups[$paramId] = [
                                'id' => $paramId,
                                'name' => $paramItem->productParam->name,
                                'items' => collect(),
                            ];
                        }

                        if (!$paramGroups[$paramId]['items']->contains('id', $paramItem->id)) {
                            $paramGroups[$paramId]['items']->push([
                                'id' => $paramItem->id,
                                'title' => $paramItem->title,
                                'value' => $paramItem->value,
                                'selected' => in_array($paramItem->id, $this->selectedVariations),
                                'would_have_results' => true,
                            ]);
                        }
                    }
                }

                // Обрабатываем parametrs аналогично
                if ($variant->parametrs) {
                    // ... аналогичная логика для parametrs ...
                }
            }

            return $paramGroups->values();
        } catch (\Exception $e) {
            return collect();
        }
    }

    public function getAvailableBrandsProperty(): Collection
    {
        try {
            return Brand::all()->map(function ($brand) {
                $brand->would_have_results = true;
                $brand->selected = in_array($brand->id, $this->selectedBrands);
                return $brand;
            });
        } catch (\Exception $e) {
            return collect();
        }
    }

    public function render()
    {
        return view('livewire.catalog.filter', [
            'filters' => $this->filters,
            'priceRange' => $this->priceRange,
        ]);
    }
} 