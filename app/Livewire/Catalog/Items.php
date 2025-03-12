<?php

namespace App\Livewire\Catalog;

// use App\Livewire\Cart\Components\Product;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductParam;
use App\Models\ProductParamItem;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class Items extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $selectedVariations = [];
    public $selectedParams = [];
    public $priceFrom = null;
    public $priceTo = null;
    public $selectedVariationNames = [];
    public $selectedSort = 'price_asc';
    public $items = null;

    public $product_ids = [];

    public ?ProductCategory $category = null;

    public $display_filter = true;
    public $selectedBrands = [];

    public $showFilters = false;
    public $showSorting = false;

    protected $queryString = [
        'selectedParams' => ['except' => []],
        'priceFrom' => ['as' => 'price_from', 'except' => null],
        'priceTo' => ['as' => 'price_to', 'except' => null],
        'selectedSort' => ['as' => 'sort', 'except' => 'default'],
        'selectedBrands' => ['as' => 'brand', 'except' => []]
    ];

    public function mount($slug = null, $brand_slug = null, $products = null, $filter = true)
    {
        try {
            \Illuminate\Support\Facades\Log::info('Метод mount вызван', [
                'slug' => $slug,
                'brand_slug' => $brand_slug,
                'products' => $products,
                'filter' => $filter,
                'request_query' => request()->query()
            ]);
            
            // Обработка параметров из URL
            if (request()->has('selectedParams')) {
                $urlParams = request()->get('selectedParams');
                if (is_array($urlParams)) {
                    $this->selectedParams = $urlParams;
                    
                    // Обновляем selectedVariations для обратной совместимости
                    $this->selectedVariations = [];
                    foreach ($this->selectedParams as $paramValues) {
                        if (is_array($paramValues)) {
                            foreach ($paramValues as $value) {
                                $this->selectedVariations[] = (int) $value;
                            }
                        }
                    }
                    
                    \Illuminate\Support\Facades\Log::info('Параметры загружены из URL', [
                        'selectedParams' => $this->selectedParams,
                        'selectedVariations' => $this->selectedVariations
                    ]);
                }
            }

        $this->display_filter = $filter;
        
        if ($slug) {
            $this->category = ProductCategory::where('slug', $slug)->first();
                \Illuminate\Support\Facades\Log::info('Категория найдена', [
                    'category_id' => $this->category->id ?? null,
                    'category_name' => $this->category->name ?? null
                ]);
        }
        if ($brand_slug) {
            $brand = Brand::where('slug', $brand_slug)->first();
            $this->product_ids = $brand->products()->pluck('id');
                \Illuminate\Support\Facades\Log::info('Бренд найден', [
                    'brand_id' => $brand->id ?? null,
                    'brand_name' => $brand->name ?? null,
                    'product_count' => count($this->product_ids)
                ]);
        }
        if ($products) {
            $this->product_ids = $products;
            \Illuminate\Support\Facades\Log::info('Установлены ID товаров', [
                'product_count' => count($this->product_ids)
            ]);
            // dd($this->product_ids);
        }
            
            // Устанавливаем диапазон цен по умолчанию, если не указан в URL
        if ($this->priceFrom === null && $this->priceTo === null) {
            $priceRange = $this->getPriceRangeProperty();
            $this->priceFrom = $priceRange->min_price;
            $this->priceTo = $priceRange->max_price;

                \Illuminate\Support\Facades\Log::info('Установлен диапазон цен по умолчанию', [
                    'priceFrom' => $this->priceFrom,
                    'priceTo' => $this->priceTo
                ]);
            } else {
                \Illuminate\Support\Facades\Log::info('Диапазон цен из URL', [
                    'priceFrom' => $this->priceFrom,
                    'priceTo' => $this->priceTo
                ]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Ошибка в методе mount', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Отправляем событие об ошибке
            $this->dispatch('filter-error', 'Произошла ошибка при загрузке компонента: ' . $e->getMessage());
            
            // Сбрасываем состояние в случае ошибки
            $this->selectedVariations = [];
            $this->selectedParams = [];
        }
    }

    #[On('filter-changed')]
    public function handleFilterChanged()
    {
        try {
            // Получаем информацию о выбранных параметрах для логирования
            $selectedParamInfo = [];
            if (!empty($this->selectedVariations)) {
                $selectedParamItems = \App\Models\ProductParamItem::whereIn('id', $this->selectedVariations)->get();
                foreach ($selectedParamItems as $item) {
                    $selectedParamInfo[$item->id] = [
                        'title' => $item->title,
                        'value' => $item->value
                    ];
                }
            }
            
            \Illuminate\Support\Facades\Log::info('Событие filter-changed обработано', [
                'selectedParams' => $this->selectedParams,
                'selectedVariations' => $this->selectedVariations,
                'selectedParamInfo' => $selectedParamInfo,
                'priceFrom' => $this->priceFrom,
                'priceTo' => $this->priceTo,
                'selectedBrands' => $this->selectedBrands,
                'selectedSort' => $this->selectedSort
            ]);
            
            // Убедимся, что все параметры имеют правильный тип
            $this->selectedVariations = array_map('intval', (array) $this->selectedVariations);
            
            // Преобразуем selectedParams в массив, чтобы избежать проблем с сериализацией
            $params = [];
            foreach ((array) $this->selectedParams as $key => $value) {
                $params[$key] = array_values(array_filter((array) $value));
            }
            $this->selectedParams = $params;
            
            // Обновляем selectedVariations для обратной совместимости
            $this->selectedVariations = [];
            foreach ($this->selectedParams as $paramValues) {
                if (is_array($paramValues)) {
                    foreach ($paramValues as $value) {
                        $this->selectedVariations[] = (int) $value;
                    }
                }
            }
            
            $this->priceFrom = is_numeric($this->priceFrom) ? (float) $this->priceFrom : null;
            $this->priceTo = is_numeric($this->priceTo) ? (float) $this->priceTo : null;
            $this->selectedBrands = array_map('intval', (array) $this->selectedBrands);
            
            // Сбрасываем страницу пагинации
            $this->resetPage();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Ошибка в методе handleFilterChanged', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Отправляем событие об ошибке
            $this->dispatch('filter-error', 'Произошла ошибка при обновлении фильтра: ' . $e->getMessage());
        }
    }

    public function updatedSelectedVariations($value)
    {
        \Illuminate\Support\Facades\Log::info('Метод updatedSelectedVariations вызван', [
            'value' => $value,
            'selectedVariations' => $this->selectedVariations
        ]);
        
        // Обновляем selectedParams на основе selectedVariations
        $this->updateSelectedParams();
        $this->resetPage();
        
        \Illuminate\Support\Facades\Log::info('После обновления параметров', [
            'selectedParams' => $this->selectedParams,
            'selectedVariations' => $this->selectedVariations
        ]);
    }
    
    /**
     * Обновляет selectedParams на основе selectedVariations
     */
    protected function updateSelectedParams()
    {
        \Illuminate\Support\Facades\Log::info('Метод updateSelectedParams вызван', [
            'selectedVariations' => $this->selectedVariations
        ]);
        
        // Очищаем текущие selectedParams
        $this->selectedParams = [];
        
        // Если нет выбранных вариаций, выходим
        if (empty($this->selectedVariations)) {
            \Illuminate\Support\Facades\Log::info('Нет выбранных вариаций, выходим');
            return;
        }
        
        // Получаем все выбранные элементы параметров
        $paramItems = ProductParamItem::whereIn('id', $this->selectedVariations)
            ->with('productParam')
            ->get();
        
        \Illuminate\Support\Facades\Log::info('Получены элементы параметров', [
            'count' => $paramItems->count(),
            'items' => $paramItems->map(function($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'param_name' => $item->productParam->name ?? 'null'
                ];
            })
        ]);
        
        // Группируем элементы по имени параметра
        foreach ($paramItems as $item) {
            $paramName = $item->productParam->name;
            
            if (!isset($this->selectedParams[$paramName])) {
                $this->selectedParams[$paramName] = [];
            }
            
            $this->selectedParams[$paramName][] = $item->id;
            
            \Illuminate\Support\Facades\Log::info('Добавлен параметр в группу', [
                'paramName' => $paramName,
                'itemId' => $item->id,
                'itemTitle' => $item->title
            ]);
        }
        
        \Illuminate\Support\Facades\Log::info('Результат группировки параметров', [
            'selectedParams' => $this->selectedParams
        ]);
    }
    
    /**
     * Обновляет выбранные параметры для конкретного параметра
     */
    public function updateParamSelection($paramName, $paramItemId)
    {
        try {
            // Преобразуем paramItemId в целое число, чтобы избежать проблем с типами
            $paramItemId = (int) $paramItemId;
            
            // Получаем информацию о параметре
            $paramItem = \App\Models\ProductParamItem::find($paramItemId);
            
            if (!$paramItem) {
                \Illuminate\Support\Facades\Log::error('Параметр не найден', [
                    'paramName' => $paramName,
                    'paramItemId' => $paramItemId
                ]);
                
                $this->dispatch('filter-error', 'Параметр не найден');
                return;
            }
            
            // Используем значение параметра вместо его ID
            $paramValue = $paramItem->value;
            $paramTitle = $paramItem->title;
            
            \Illuminate\Support\Facades\Log::info('Метод updateParamSelection вызван', [
                'paramName' => $paramName,
                'paramItemId' => $paramItemId,
                'paramValue' => $paramValue,
                'paramTitle' => $paramTitle,
                'currentSelectedParams' => $this->selectedParams
            ]);
            
            // Если параметр еще не выбран, создаем для него массив
            if (!isset($this->selectedParams[$paramName])) {
                $this->selectedParams[$paramName] = [];
                \Illuminate\Support\Facades\Log::info('Создан новый массив для параметра', [
                    'paramName' => $paramName
                ]);
            }
            
            // Проверяем, выбран ли уже этот элемент параметра по ID
            $index = array_search($paramItemId, $this->selectedParams[$paramName]);
            
            if ($index !== false) {
                // Если элемент уже выбран, удаляем его
                unset($this->selectedParams[$paramName][$index]);
                $this->selectedParams[$paramName] = array_values($this->selectedParams[$paramName]);
                
                \Illuminate\Support\Facades\Log::info('Удален элемент параметра', [
                    'paramName' => $paramName,
                    'paramItemId' => $paramItemId,
                    'paramValue' => $paramValue,
                    'paramTitle' => $paramTitle,
                    'remainingItems' => $this->selectedParams[$paramName]
                ]);
                
                // Если массив пуст, удаляем его
                if (empty($this->selectedParams[$paramName])) {
                    unset($this->selectedParams[$paramName]);
                    \Illuminate\Support\Facades\Log::info('Удален пустой массив параметра', [
                        'paramName' => $paramName
                    ]);
                }
            } else {
                // Если элемент не выбран, добавляем его ID
                $this->selectedParams[$paramName][] = $paramItemId;
                \Illuminate\Support\Facades\Log::info('Добавлен элемент параметра', [
                    'paramName' => $paramName,
                    'paramItemId' => $paramItemId,
                    'paramValue' => $paramValue,
                    'paramTitle' => $paramTitle,
                    'allItems' => $this->selectedParams[$paramName]
                ]);
            }
            
            // Обновляем selectedVariations для обратной совместимости
            $this->selectedVariations = [];
            foreach ($this->selectedParams as $paramValues) {
                if (is_array($paramValues)) {
                    foreach ($paramValues as $value) {
                        $this->selectedVariations[] = (int) $value;
                    }
                }
            }
            
            \Illuminate\Support\Facades\Log::info('Обновлены selectedVariations', [
                'selectedVariations' => $this->selectedVariations,
                'selectedParams' => $this->selectedParams
            ]);
            
            // Сбрасываем страницу пагинации
            $this->resetPage();
            
            // Используем дебаунс для сброса страницы, чтобы избежать слишком частых обновлений
            $this->dispatch('filter-changed');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Ошибка в методе updateParamSelection', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'paramName' => $paramName,
                'paramItemId' => $paramItemId
            ]);
            
            // Отправляем событие об ошибке
            $this->dispatch('filter-error', 'Произошла ошибка при обновлении параметра фильтра: ' . $e->getMessage());
            
            // Сбрасываем состояние фильтра в случае ошибки
            $this->resetPage();
        }
    }

    public function updatedPriceFrom($value)
    {
        \Illuminate\Support\Facades\Log::info('Метод updatedPriceFrom вызван', [
            'value' => $value,
            'currentPriceFrom' => $this->priceFrom,
            'currentPriceTo' => $this->priceTo
        ]);
        
        // Ensure priceFrom doesn't exceed priceTo
        if ($value > $this->priceTo) {
            $this->priceFrom = $this->priceTo;
            \Illuminate\Support\Facades\Log::info('Скорректирована нижняя граница цены', [
                'newPriceFrom' => $this->priceFrom,
                'priceTo' => $this->priceTo
            ]);
        }
        $this->dispatch('filter-changed');
    }

    public function updatedPriceTo($value)
    {
        \Illuminate\Support\Facades\Log::info('Метод updatedPriceTo вызван', [
            'value' => $value,
            'currentPriceFrom' => $this->priceFrom,
            'currentPriceTo' => $this->priceTo
        ]);
        
        // Ensure priceTo isn't less than priceFrom
        if ($value < $this->priceFrom) {
            $this->priceTo = $this->priceFrom;
            \Illuminate\Support\Facades\Log::info('Скорректирована верхняя граница цены', [
                'newPriceTo' => $this->priceTo,
                'priceFrom' => $this->priceFrom
            ]);
        }
        $this->dispatch('filter-changed');
    }

    public function getProductsProperty()
    {
        try {
            \Illuminate\Support\Facades\Log::info('Метод getProductsProperty вызван', [
                'category' => $this->category,
                'product_ids_count' => count($this->product_ids ?? []),
                'selectedVariations' => $this->selectedVariations,
                'selectedParams' => $this->selectedParams,
                'priceFrom' => $this->priceFrom,
                'priceTo' => $this->priceTo,
                'selectedBrands' => $this->selectedBrands,
                'selectedSort' => $this->selectedSort
            ]);
            
            // Убедимся, что все параметры имеют правильный тип
            $this->selectedVariations = array_map('intval', (array) $this->selectedVariations);
            $this->priceFrom = is_numeric($this->priceFrom) ? (float) $this->priceFrom : null;
            $this->priceTo = is_numeric($this->priceTo) ? (float) $this->priceTo : null;
            $this->selectedBrands = array_map('intval', (array) $this->selectedBrands);
            
            // Получаем информацию о выбранных параметрах
            $selectedParamItems = [];
            if (!empty($this->selectedVariations)) {
                $selectedParamItems = \App\Models\ProductParamItem::whereIn('id', $this->selectedVariations)
                    ->get()
                    ->keyBy('id');
                
                \Illuminate\Support\Facades\Log::info('Получена информация о выбранных параметрах', [
                    'count' => $selectedParamItems->count(),
                    'items' => $selectedParamItems->map(function($item) {
                        return [
                            'id' => $item->id,
                            'title' => $item->title,
                            'value' => $item->value
                        ];
                    })
                ]);
            }
            
            if ($this->category) {
                $query = $this->category->products();
                \Illuminate\Support\Facades\Log::info('Запрос по категории');
            } elseif ($this->product_ids) {
                $query = \App\Models\Product::whereIn('id', $this->product_ids);
                \Illuminate\Support\Facades\Log::info('Запрос по ID товаров');
            } else {
                $query = \App\Models\Product::query();
                \Illuminate\Support\Facades\Log::info('Запрос по всем товарам');
            }
            
            if (!empty($this->selectedBrands)) {
                $query->whereIn('brand_id', $this->selectedBrands);
                \Illuminate\Support\Facades\Log::info('Применен фильтр по брендам', [
                    'selectedBrands' => $this->selectedBrands
                ]);
            }

            // Получаем товары с их вариациями и параметрами
            $baseQuery = clone $query;
            $products = $baseQuery->with([
                'variants', 
                'variants.paramItems.productParam',
                'variants.parametrs.productParam',
                'variants.img'
            ])->select('products.*')->get();
            
            \Illuminate\Support\Facades\Log::info('Получены товары', [
                'products_count' => $products->count(),
                'products_with_variants' => $products->filter(function($product) {
                    return $product->variants && $product->variants->count() > 0;
                })->count(),
                'total_variants' => $products->sum(function($product) {
                    return $product->variants ? $product->variants->count() : 0;
                })
            ]);
            
            // Собираем все вариации товаров
            $variants = collect();
            
            foreach ($products as $product) {
                // Если у товара есть вариации
                if ($product->variants && $product->variants->count() > 0) {
                    \Illuminate\Support\Facades\Log::info('Обработка товара с вариациями', [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'variants_count' => $product->variants->count()
                    ]);
                    
                    // Проверяем каждую вариацию на соответствие фильтрам
                    foreach ($product->variants as $variant) {
                        $variantMatches = true;
                        
                        // Проверяем соответствие параметрам фильтра
                        if (!empty($this->selectedVariations)) {
                            // Получаем параметры вариации из обеих связей
                            $variantParamIds = collect();
                            
                            if ($variant->paramItems) {
                                $variantParamIds = $variantParamIds->merge($variant->paramItems->pluck('id'));
                            }
                            
                            if ($variant->parametrs) {
                                $variantParamIds = $variantParamIds->merge($variant->parametrs->pluck('id'));
                            }
                            
                            $variantParamIds = $variantParamIds->unique()->toArray();
                            
                            \Illuminate\Support\Facades\Log::info('Параметры вариации', [
                                'variant_id' => $variant->id,
                                'variant_name' => $variant->name,
                                'variantParamIds' => $variantParamIds,
                                'selectedVariations' => $this->selectedVariations
                            ]);
                            
                            // Проверяем, содержит ли вариация все выбранные параметры
                            foreach ($this->selectedVariations as $selectedParamId) {
                                if (!in_array($selectedParamId, $variantParamIds)) {
                                    $variantMatches = false;
                                    \Illuminate\Support\Facades\Log::info('Вариация не соответствует параметру', [
                                        'variant_id' => $variant->id,
                                        'variant_name' => $variant->name,
                                        'selectedParamId' => $selectedParamId
                                    ]);
                                    break;
                                }
                            }
                        }
                        
                        // Проверяем соответствие ценовому диапазону
                        if ($variantMatches && (!empty($this->priceFrom) || !empty($this->priceTo))) {
                            $variantPrice = $variant->new_price > 0 ? $variant->new_price : $variant->price;
                            
                            \Illuminate\Support\Facades\Log::info('Проверка цены вариации', [
                                'variant_id' => $variant->id,
                                'variant_name' => $variant->name,
                                'variantPrice' => $variantPrice,
                                'priceFrom' => $this->priceFrom,
                                'priceTo' => $this->priceTo
                            ]);
                            
                            if (!empty($this->priceFrom) && $variantPrice < $this->priceFrom) {
                                $variantMatches = false;
                                \Illuminate\Support\Facades\Log::info('Вариация не соответствует минимальной цене', [
                                    'variant_id' => $variant->id,
                                    'variant_name' => $variant->name,
                                    'variantPrice' => $variantPrice,
                                    'priceFrom' => $this->priceFrom
                                ]);
                            }
                            
                            if (!empty($this->priceTo) && $variantPrice > $this->priceTo) {
                                $variantMatches = false;
                                \Illuminate\Support\Facades\Log::info('Вариация не соответствует максимальной цене', [
                                    'variant_id' => $variant->id,
                                    'variant_name' => $variant->name,
                                    'variantPrice' => $variantPrice,
                                    'priceTo' => $this->priceTo
                                ]);
                            }
                        }
                        
                        // Если вариация соответствует всем фильтрам, добавляем ее в результаты
                        if ($variantMatches) {
                            \Illuminate\Support\Facades\Log::info('Вариация добавлена в результаты', [
                                'variant_id' => $variant->id,
                                'variant_name' => $variant->name
                            ]);
                            $variants->push($variant);
                        }
                    }
                }
            }
            
            // Применяем сортировку к коллекции вариаций
        switch ($this->selectedSort) {
            case 'price_asc':
                    $variants = $variants->sortBy(function ($variant) {
                        return $variant->new_price > 0 ? $variant->new_price : $variant->price;
                    })->values();
                break;
            case 'price_desc':
                    $variants = $variants->sortByDesc(function ($variant) {
                        return $variant->new_price > 0 ? $variant->new_price : $variant->price;
                    })->values();
                break;
            case 'name_asc':
                    $variants = $variants->sortBy('name')->values();
                break;
            case 'name_desc':
                    $variants = $variants->sortByDesc('name')->values();
                break;
            default:
                    // По умолчанию сортировка по популярности (id)
                    $variants = $variants->sortBy('id')->values();
                break;
        }

            // Применяем пагинацию к коллекции вариаций
            $perPage = 18;
            $page = request()->get('page', 1);
            $offset = ($page - 1) * $perPage;
            
            $paginatedVariants = new \Illuminate\Pagination\LengthAwarePaginator(
                $variants->slice($offset, $perPage),
                $variants->count(),
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );
            
            return $paginatedVariants;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Ошибка в методе getProductsProperty', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Отправляем событие об ошибке
            $this->dispatch('filter-error', 'Произошла ошибка при получении товаров: ' . $e->getMessage());
            
            return null;
        }
    }

    public function getAvailableBrandsProperty()
    {
        try {
            // Получаем все бренды из базы данных
            $allBrands = \App\Models\Brand::all();
            $activeBrandIds = collect();
            
            // Получаем вариации товаров
            $variants = $this->products;
            
            if (!$variants || $variants->isEmpty()) {
                // Если нет вариаций, возвращаем все бренды как неактивные
                return $allBrands->map(function($brand) {
                    $brand->would_have_results = false;
                    $brand->selected = in_array($brand->id, $this->selectedBrands);
                    return $brand;
                });
            }
            
            // Собираем ID брендов из вариаций
            foreach ($variants as $variant) {
                if ($variant->product && $variant->product->brand_id) {
                    $activeBrandIds->push($variant->product->brand_id);
                }
            }
            
            // Делаем уникальными
            $activeBrandIds = $activeBrandIds->unique();
            
            // Отмечаем активные и неактивные бренды
            return $allBrands->map(function($brand) use ($activeBrandIds) {
                $brand->would_have_results = $activeBrandIds->contains($brand->id);
                $brand->selected = in_array($brand->id, $this->selectedBrands);
                return $brand;
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Ошибка в методе getAvailableBrandsProperty', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return collect();
        }
    }

    public function getPriceRangeProperty()
    {
        try {
            \Illuminate\Support\Facades\Log::info('Начало метода getPriceRangeProperty');
            
            // Получаем все вариации товаров без учета ценовых фильтров
            $variants = collect();
            
            if ($this->category) {
                // Получаем все товары в категории
                $query = $this->category->products();
            } elseif ($this->product_ids) {
                $query = \App\Models\Product::whereIn('id', $this->product_ids);
               
            } else {
                $query = \App\Models\Product::query();
            }
            
            // Применяем фильтр по брендам, если он выбран
            if (!empty($this->selectedBrands)) {
                $query->whereIn('brand_id', $this->selectedBrands);
            }
            
            // Применяем фильтр по параметрам, если он выбран
            if (!empty($this->selectedVariations)) {
                $products = $query->with(['variants.paramItems'])->get();
                
                
                foreach ($products as $product) {
                    if (!$product->variants) continue;
                    
                    foreach ($product->variants as $variant) {
                        $variantMatches = true;
                        
                        if (!$variant->paramItems) {
                            $variantMatches = false;
                            continue;
                        }
                        
                        $variantParamIds = $variant->paramItems->pluck('id')->toArray();
                        
                        // Проверяем, содержит ли вариация все выбранные параметры
                        foreach ($this->selectedVariations as $selectedParamId) {
                            if (!in_array($selectedParamId, $variantParamIds)) {
                                $variantMatches = false;
                                break;
                            }
                        }
                        
                        if ($variantMatches) {
                            $variants->push($variant);
                        }
                    }
                }
            } else {
                // Если параметры не выбраны, получаем все вариации
                $products = $query->with('variants')->get();
                
                foreach ($products as $product) {
                    if (!$product->variants) continue;
                    
                    foreach ($product->variants as $variant) {
                        $variants->push($variant);
                    }
                }
            }
            
            // Если нет вариаций, возвращаем значения по умолчанию
            if ($variants->isEmpty()) {
                \Illuminate\Support\Facades\Log::info('Нет вариаций для расчета диапазона цен, используем значения по умолчанию');
                return (object) [
                    'min_price' => 0,
                    'max_price' => 100000
                ];
            }
            
            // Рассчитываем минимальную и максимальную цену
            $prices = $variants->map(function($variant) {
                // Если есть цена со скидкой и она больше 0, используем ее, иначе используем обычную цену
                return $variant->new_price > 0 ? $variant->new_price : $variant->price;
            });
            
            $minPrice = $prices->min();
            $maxPrice = $prices->max();
            
            // Если цены не определены, используем значения по умолчанию
            if ($minPrice === null) $minPrice = 0;
            if ($maxPrice === null) $maxPrice = 100000;
            
            \Illuminate\Support\Facades\Log::info('Рассчитан диапазон цен', [
                'variants_count' => $variants->count(),
                'min_price' => $minPrice,
                'max_price' => $maxPrice
            ]);
            
            return (object) [
                'min_price' => $minPrice,
                'max_price' => $maxPrice
            ];
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Ошибка в методе getPriceRangeProperty', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return (object) [
                'min_price' => 0,
                'max_price' => 100000
            ];
        }
    }

    public function getAvailableParamItemsProperty()
    {
        try {
            \Illuminate\Support\Facades\Log::info('Начало метода getAvailableParamItemsProperty');
            
            $paramGroups = [];
            
            // Получаем все вариации товаров, соответствующие текущим фильтрам
            $variants = $this->products;
            
            if (!$variants || $variants->isEmpty()) {
                \Illuminate\Support\Facades\Log::warning('Нет вариаций для получения параметров');
                return collect();
            }
            
            \Illuminate\Support\Facades\Log::info('Получены вариации для параметров', [
                'variants_count' => $variants->count(),
                'variants_ids' => $variants->pluck('id')->toArray()
            ]);
            
            // Получаем все параметры для текущей категории (без учета фильтров)
            $allCategoryParamItems = collect();
            
            if ($this->category) {
                // Получаем все товары в категории без фильтров
                $allCategoryProducts = $this->category->products()->with([
                    'variants.paramItems.productParam',
                    'variants.parametrs.productParam'
                ])->get();
                
                // Собираем все параметры из всех вариаций в категории
                foreach ($allCategoryProducts as $product) {
                    if (!$product->variants) continue;
                    
                    foreach ($product->variants as $variant) {
                        // Обрабатываем paramItems
                        if ($variant->paramItems) {
                            foreach ($variant->paramItems as $paramItem) {
                                if (!$paramItem->productParam) continue;
                                
                                $allCategoryParamItems->push([
                                    'id' => $paramItem->id,
                                    'param_id' => $paramItem->productParam->id,
                                    'param_name' => $paramItem->productParam->name,
                                    'title' => $paramItem->title,
                                    'value' => $paramItem->value
                                ]);
                            }
                        }
                        
                        // Обрабатываем parametrs
                        if ($variant->parametrs) {
                            foreach ($variant->parametrs as $paramItem) {
                                if (!$paramItem->productParam) continue;
                                
                                $allCategoryParamItems->push([
                                    'id' => $paramItem->id,
                                    'param_id' => $paramItem->productParam->id,
                                    'param_name' => $paramItem->productParam->name,
                                    'title' => $paramItem->title,
                                    'value' => $paramItem->value
                                ]);
                            }
                        }
                    }
                }
            }
            
            // Сначала собираем все ID параметров и их элементов из вариаций после фильтрации
            $activeParamItemIds = collect();
            $activeParamIds = collect();
            
            // Обрабатываем каждую вариацию для сбора активных параметров
            foreach ($variants as $variant) {
                if (!$variant) {
                    \Illuminate\Support\Facades\Log::warning('Обнаружена null вариация');
                    continue;
                }
                
                \Illuminate\Support\Facades\Log::info('Обработка вариации для параметров', [
                    'variant_id' => $variant->id,
                    'variant_name' => $variant->name,
                    'has_param_items' => $variant->paramItems && $variant->paramItems->count() > 0,
                    'param_items_count' => $variant->paramItems ? $variant->paramItems->count() : 0
                ]);
                
                // Добавляем параметры вариации
                if (!$variant->paramItems && !$variant->parametrs) {
                    \Illuminate\Support\Facades\Log::warning('Вариация без параметров', [
                        'variant_id' => $variant->id,
                        'variant_name' => $variant->name
                    ]);
                    continue;
                }
                
                // Загружаем отношения, если они не загружены
                if (!$variant->relationLoaded('paramItems')) {
                    $variant->load('paramItems.productParam');
                }
                if (!$variant->relationLoaded('parametrs')) {
                    $variant->load('parametrs.productParam');
                }
                
                // Обрабатываем paramItems
                if ($variant->paramItems) {
                    foreach ($variant->paramItems as $paramItem) {
                        if (!$paramItem) continue;
                        
                        // Получаем родительский параметр
                        $param = $paramItem->productParam;
                        
                        if (!$param) continue;
                        
                        // Добавляем ID параметра в список активных
                        $activeParamIds->push($param->id);
                        // Добавляем ID элемента параметра в список активных
                        $activeParamItemIds->push($paramItem->id);
                        
                        \Illuminate\Support\Facades\Log::info('Добавление параметра вариации (paramItems)', [
                            'param_id' => $param->id,
                            'param_name' => $param->name,
                            'param_item_id' => $paramItem->id,
                            'param_item_title' => $paramItem->title,
                            'param_item_value' => $paramItem->value
                        ]);
                        
                        // Добавляем группу параметров, если ее еще нет
                        if (!isset($paramGroups[$param->id])) {
                            $paramGroups[$param->id] = [
                                'id' => $param->id,
                                'name' => $param->name,
                                'items' => []
                            ];
                        }
                        
                        // Добавляем элемент параметра, если его еще нет
                        if (!isset($paramGroups[$param->id]['items'][$paramItem->id])) {
                            $paramGroups[$param->id]['items'][$paramItem->id] = [
                                'id' => $paramItem->id,
                                'title' => $paramItem->title,
                                'value' => $paramItem->value,
                                'selected' => in_array($paramItem->id, $this->selectedVariations),
                                'would_have_results' => true
                            ];
                        }
                    }
                }
                
                // Обрабатываем parametrs
                if ($variant->parametrs) {
                    foreach ($variant->parametrs as $paramItem) {
                        if (!$paramItem) continue;
                        
                        // Получаем родительский параметр
                        $param = $paramItem->productParam;
                        
                        if (!$param) continue;
                        
                        // Добавляем ID параметра в список активных
                        $activeParamIds->push($param->id);
                        // Добавляем ID элемента параметра в список активных
                        $activeParamItemIds->push($paramItem->id);
                        
                        \Illuminate\Support\Facades\Log::info('Добавление параметра вариации (parametrs)', [
                            'param_id' => $param->id,
                            'param_name' => $param->name,
                            'param_item_id' => $paramItem->id,
                            'param_item_title' => $paramItem->title,
                            'param_item_value' => $paramItem->value
                        ]);
                        
                        // Добавляем группу параметров, если ее еще нет
                        if (!isset($paramGroups[$param->id])) {
                            $paramGroups[$param->id] = [
                                'id' => $param->id,
                                'name' => $param->name,
                                'items' => []
                            ];
                        }
                        
                        // Добавляем элемент параметра, если его еще нет
                        if (!isset($paramGroups[$param->id]['items'][$paramItem->id])) {
                            $paramGroups[$param->id]['items'][$paramItem->id] = [
                                'id' => $paramItem->id,
                                'title' => $paramItem->title,
                                'value' => $paramItem->value,
                                'selected' => in_array($paramItem->id, $this->selectedVariations),
                                'would_have_results' => true
                            ];
                        }
                    }
                }
            }
            
            // Делаем уникальными ID параметров и элементов
            $activeParamIds = $activeParamIds->unique();
            $activeParamItemIds = $activeParamItemIds->unique();
            
            \Illuminate\Support\Facades\Log::info('Собраны активные параметры', [
                'active_param_ids_count' => $activeParamIds->count(),
                'active_param_ids' => $activeParamIds->toArray(),
                'active_param_item_ids_count' => $activeParamItemIds->count(),
                'active_param_item_ids' => $activeParamItemIds->toArray()
            ]);
            
            // Добавляем все параметры из категории, которых нет в активных, как неактивные
            if ($allCategoryParamItems->isNotEmpty()) {
                $allCategoryParamItems = $allCategoryParamItems->unique(function ($item) {
                    return $item['param_id'] . '-' . $item['id'];
                });
                
                foreach ($allCategoryParamItems as $item) {
                    // Добавляем группу параметров, если ее еще нет
                    if (!isset($paramGroups[$item['param_id']])) {
                        $paramGroups[$item['param_id']] = [
                            'id' => $item['param_id'],
                            'name' => $item['param_name'],
                            'items' => []
                        ];
                    }
                    
                    // Добавляем элемент параметра, если его еще нет
                    if (!isset($paramGroups[$item['param_id']]['items'][$item['id']])) {
                        $paramGroups[$item['param_id']]['items'][$item['id']] = [
                            'id' => $item['id'],
                            'title' => $item['title'],
                            'value' => $item['value'],
                            'selected' => in_array($item['id'], $this->selectedVariations),
                            'would_have_results' => $activeParamItemIds->contains($item['id'])
                        ];
                    }
                }
            }
            
            // Добавляем выбранные параметры, даже если они не активны
            if (!empty($this->selectedVariations)) {
                $selectedItems = \App\Models\ProductParamItem::whereIn('id', $this->selectedVariations)
                    ->with('productParam')
                    ->get();
                
                foreach ($selectedItems as $item) {
                    if (!$item->productParam) continue;
                    
                    $param = $item->productParam;
                    
                    // Добавляем группу параметров, если ее еще нет
                    if (!isset($paramGroups[$param->id])) {
                        $paramGroups[$param->id] = [
                            'id' => $param->id,
                            'name' => $param->name,
                            'items' => []
                        ];
                    }
                    
                    // Добавляем элемент параметра, если его еще нет
                    if (!isset($paramGroups[$param->id]['items'][$item->id])) {
                        $paramGroups[$param->id]['items'][$item->id] = [
                            'id' => $item->id,
                            'title' => $item->title,
                            'value' => $item->value,
                            'selected' => true,
                            'would_have_results' => $activeParamItemIds->contains($item->id)
                        ];
                    } else {
                        // Если элемент уже есть, обновляем его статус
                        $paramGroups[$param->id]['items'][$item->id]['selected'] = true;
                    }
                }
            }
            
            // Преобразуем массив групп параметров в коллекцию
            $paramItems = collect();
            foreach ($paramGroups as $paramGroup) {
                $paramGroup['items'] = collect($paramGroup['items'])->values();
                $paramItems->push($paramGroup);
            }
            
            \Illuminate\Support\Facades\Log::info('Результат getAvailableParamItemsProperty', [
                'param_groups_count' => $paramItems->count(),
                'param_groups' => $paramItems->pluck('name')->toArray(),
                'param_items_counts' => $paramItems->map(function($group) {
                    return [
                        'group' => $group['name'],
                        'items_count' => $group['items']->count(),
                        'active_items_count' => $group['items']->where('would_have_results', true)->count(),
                        'items' => $group['items']->pluck('title')->toArray()
                    ];
                })->toArray()
            ]);
            
            return $paramItems;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Ошибка в методе getAvailableParamItemsProperty', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->dispatch('error', [
                'message' => 'Произошла ошибка при получении доступных параметров: ' . $e->getMessage()
            ]);
            
            return collect();
        }
    }

    public function getAvailableFiltersProperty()
    {
        try {
            \Illuminate\Support\Facades\Log::info('Начало метода getAvailableFiltersProperty');
            
            $filters = collect();
            
            // Получаем доступные параметры
            $paramItems = $this->availableParamItems;
            
            \Illuminate\Support\Facades\Log::info('Получены доступные параметры для фильтров', [
                'param_groups_count' => $paramItems->count(),
                'param_groups' => $paramItems->pluck('name')->toArray()
            ]);
            
            // Добавляем параметры в фильтры
            foreach ($paramItems as $paramGroup) {
                \Illuminate\Support\Facades\Log::info('Обработка группы параметров для фильтров', [
                    'group_id' => $paramGroup['id'],
                    'group_name' => $paramGroup['name'],
                    'items_count' => $paramGroup['items']->count()
                ]);
                
                $filters->push([
                    'id' => $paramGroup['id'],
                    'name' => $paramGroup['name'],
                    'type' => 'param',
                    'items' => $paramGroup['items']
                ]);
            }
            
            // Добавляем фильтр по брендам
            $brands = $this->availableBrands;
            
            \Illuminate\Support\Facades\Log::info('Получены доступные бренды для фильтров', [
                'brands_count' => $brands->count(),
                'brands' => $brands->pluck('name')->toArray()
            ]);
            
            if ($brands->count() > 0) {
                $filters->push([
                    'id' => 'brand',
                    'name' => 'Бренд',
                    'type' => 'brand',
                    'items' => $brands->map(function ($brand) {
                        return [
                            'id' => $brand->id,
                            'title' => $brand->name,
                            'selected' => $brand->selected,
                            'would_have_results' => $brand->would_have_results
                        ];
                    })
                ]);
            }
            
            \Illuminate\Support\Facades\Log::info('Результат getAvailableFiltersProperty', [
                'filters_count' => $filters->count(),
                'filters' => $filters->map(function($filter) {
                    return [
                        'id' => $filter['id'],
                        'name' => $filter['name'],
                        'type' => $filter['type'],
                        'items_count' => count($filter['items'])
                    ];
                })->toArray()
            ]);
            
            return $filters;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Ошибка в методе getAvailableFiltersProperty', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->dispatch('error', [
                'message' => 'Произошла ошибка при получении доступных фильтров: ' . $e->getMessage()
            ]);
            
            return collect();
        }
    }

    public function getSortOptions()
    {
        return [
            'default' => [
                'label' => 'По умолчанию',
                'icon' => 'M3 4.5h14.25M3 9h9.75M3 13.5h9.75m4.5-4.5v12m0 0l-3.75-3.75M17.25 21L21 17.25'
            ],
            'price_asc' => [
                'label' => 'Сначала дешевые',
                'icon' => 'M3 4.5h14.25M3 9h9.75M3 13.5h5.25m5.25-.75L17.25 9m0 0L21 12.75M17.25 9v12'
            ],
            'price_desc' => [
                'label' => 'Сначала дорогие',
                'icon' => 'M3 4.5h14.25M3 9h9.75M3 13.5h5.25m5.25-.75L17.25 9m0 0L21 12.75M17.25 9v12'
            ],
            'name_asc' => [
                'label' => 'По названию А-Я',
                'icon' => 'M3 4.5h14.25M3 9h9.75M3 13.5h9.75m4.5-4.5v12m0 0l-3.75-3.75M17.25 21L21 17.25'
            ],
            'name_desc' => [
                'label' => 'По названию Я-А',
                'icon' => 'M3 4.5h14.25M3 9h9.75M3 13.5h9.75m4.5-4.5v12m0 0l-3.75-3.75M17.25 21L21 17.25'
            ],
        ];
    }

    public function updateSort($value)
    {
        \Illuminate\Support\Facades\Log::info('Метод updateSort вызван', [
            'value' => $value,
            'currentSort' => $this->selectedSort
        ]);
        
        $this->selectedSort = $value;
        $this->closeSorting();
        
        \Illuminate\Support\Facades\Log::info('Сортировка обновлена', [
            'newSort' => $this->selectedSort
        ]);
        
        $this->dispatch('filter-changed');
    }

    public function render()
    {
        try {
            \Illuminate\Support\Facades\Log::info('Начало метода render', [
                'category' => $this->category ? $this->category->name : null,
                'product_ids_count' => count($this->product_ids ?? []),
                'selectedVariations' => $this->selectedVariations,
                'selectedParams' => $this->selectedParams,
                'priceFrom' => $this->priceFrom,
                'priceTo' => $this->priceTo,
                'selectedBrands' => $this->selectedBrands,
                'selectedSort' => $this->selectedSort,
                'showFilters' => $this->showFilters,
                'showSorting' => $this->showSorting,
                'display_filter' => $this->display_filter
            ]);
            
            try {
                // Получаем доступные фильтры
                $filters = $this->availableFilters;
                
                if (!$filters) {
                    $filters = collect();
                    \Illuminate\Support\Facades\Log::warning('Фильтры не найдены, используем пустую коллекцию');
                }
                
                \Illuminate\Support\Facades\Log::info('Получены фильтры для отображения', [
                    'filters_count' => $filters->count(),
                    'filters' => $filters->map(function($filter) {
                        return [
                            'id' => $filter['id'] ?? $filter->id ?? 'unknown',
                            'name' => $filter['name'] ?? $filter->name ?? 'unknown',
                            'type' => $filter['type'] ?? 'unknown',
                            'items_count' => isset($filter['items']) ? count($filter['items']) : 
                                            (isset($filter->params) ? $filter->params->count() : 0)
                        ];
                    })->toArray()
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Ошибка при получении фильтров', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $filters = collect();
            }
            
            try {
                // Получаем товары для отображения
                $products = $this->products;
                
                if (!$products) {
                    $products = collect();
                    \Illuminate\Support\Facades\Log::warning('Товары не найдены, используем пустую коллекцию');
                }
                
                \Illuminate\Support\Facades\Log::info('Получены товары для отображения', [
                    'products_count' => $products->count(),
                    'current_page' => $products->currentPage(),
                    'total_pages' => $products->lastPage()
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Ошибка при получении товаров', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $products = collect();
            }
            
            try {
                $priceRange = $this->priceRange;
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Ошибка при получении диапазона цен', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $priceRange = (object) ['min_price' => 0, 'max_price' => 100000];
            }
            
            try {
                $sortOptions = $this->getSortOptions();
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Ошибка при получении опций сортировки', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $sortOptions = [
                    'price_asc' => ['label' => 'По возрастанию цены', 'icon' => 'M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12'],
                    'price_desc' => ['label' => 'По убыванию цены', 'icon' => 'M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4']
                ];
            }
            
            return view('livewire.catalog.items', [
                'products' => $products,
                'filters' => $filters,
                'priceRange' => $priceRange,
                'sortOptions' => $sortOptions,
                'showFilters' => $this->showFilters,
                'showSorting' => $this->showSorting,
                'display_filter' => $this->display_filter
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Ошибка в методе render', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->dispatch('error', [
                'message' => 'Произошла ошибка при отображении страницы: ' . $e->getMessage()
            ]);
            
            return view('livewire.catalog.items', [
                'products' => collect(),
                'filters' => collect(),
                'priceRange' => (object) ['min_price' => 0, 'max_price' => 100000],
                'sortOptions' => [
                    'price_asc' => ['label' => 'По возрастанию цены', 'icon' => 'M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12'],
                    'price_desc' => ['label' => 'По убыванию цены', 'icon' => 'M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4']
                ],
                'showFilters' => $this->showFilters,
                'showSorting' => $this->showSorting,
                'display_filter' => $this->display_filter,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
        $this->showSorting = false;
    }

    public function closeFilters()
    {
        $this->showFilters = false;
    }

    public function toggleSorting()
    {
        $this->showSorting = !$this->showSorting;
        $this->showFilters = false;
    }

    public function closeSorting()
    {
        $this->showSorting = false;
    }

    public function applyPriceRangeFilter($minPrice, $maxPrice)
    {
        $this->priceFrom = $minPrice;
        $this->priceTo = $maxPrice;
    }

    public function resetPriceRangeFilter()
    {
        $priceRange = $this->getPriceRangeProperty();
        $this->priceFrom = $priceRange->min_price;
        $this->priceTo = $priceRange->max_price;
    }

    public function resetFilters()
    {
        \Illuminate\Support\Facades\Log::info('Метод resetFilters вызван', [
            'currentSelectedVariations' => $this->selectedVariations,
            'currentSelectedParams' => $this->selectedParams,
            'currentPriceFrom' => $this->priceFrom,
            'currentPriceTo' => $this->priceTo,
            'currentSelectedBrands' => $this->selectedBrands,
            'currentSelectedSort' => $this->selectedSort
        ]);
        
        $this->selectedVariations = [];
        $this->selectedParams = [];
        $this->selectedVariationNames = [];
        $this->priceFrom = $this->priceRange->min_price;
        $this->priceTo = $this->priceRange->max_price;
        $this->selectedBrands = [];
        $this->selectedSort = 'default';
        $this->resetPage();
        
        \Illuminate\Support\Facades\Log::info('Фильтры сброшены', [
            'newPriceFrom' => $this->priceFrom,
            'newPriceTo' => $this->priceTo
        ]);
        
        $this->dispatch('filter-reset');
    }

    public function dehydrate()
    {
        try {
            // Убедимся, что все свойства корректно сериализуются
            $this->selectedVariations = array_map('intval', array_values(array_filter((array) $this->selectedVariations)));
            
            // Преобразуем selectedParams в массив, чтобы избежать проблем с сериализацией
            $params = [];
            foreach ((array) $this->selectedParams as $key => $value) {
                $params[$key] = array_map('intval', array_values(array_filter((array) $value)));
            }
            $this->selectedParams = $params;
            
            // Убедимся, что числовые значения корректно сериализуются
            $this->priceFrom = is_numeric($this->priceFrom) ? (float) $this->priceFrom : null;
            $this->priceTo = is_numeric($this->priceTo) ? (float) $this->priceTo : null;
            
            // Убедимся, что массивы корректно сериализуются
            $this->selectedBrands = array_map('intval', array_values(array_filter((array) $this->selectedBrands)));
            
            // Получаем информацию о выбранных параметрах для логирования
            $selectedParamInfo = [];
            if (!empty($this->selectedVariations)) {
                $selectedParamItems = \App\Models\ProductParamItem::whereIn('id', $this->selectedVariations)->get();
                foreach ($selectedParamItems as $item) {
                    $selectedParamInfo[$item->id] = [
                        'title' => $item->title,
                        'value' => $item->value
                    ];
                }
            }
            
            \Illuminate\Support\Facades\Log::info('Компонент сериализован', [
                'selectedVariations' => $this->selectedVariations,
                'selectedParams' => $this->selectedParams,
                'selectedParamInfo' => $selectedParamInfo,
                'priceFrom' => $this->priceFrom,
                'priceTo' => $this->priceTo,
                'selectedBrands' => $this->selectedBrands
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Ошибка в методе dehydrate', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Отправляем событие об ошибке
            $this->dispatch('filter-error', 'Произошла ошибка при сериализации компонента: ' . $e->getMessage());
            
            // Сбрасываем состояние в случае ошибки
            $this->selectedVariations = [];
            $this->selectedParams = [];
        }
    }

    #[On('filter-reset')]
    public function handleFilterReset()
    {
        // Этот метод будет вызван при событии filter-reset
        // Здесь можно добавить дополнительную логику, если нужно
    }

    public function hydrate()
    {
        try {
            // Убедимся, что все свойства корректно десериализуются
            $this->selectedVariations = array_map('intval', array_values(array_filter((array) $this->selectedVariations)));
            
            // Преобразуем selectedParams в массив, чтобы избежать проблем с десериализацией
            $params = [];
            foreach ((array) $this->selectedParams as $key => $value) {
                $params[$key] = array_map('intval', array_values(array_filter((array) $value)));
            }
            $this->selectedParams = $params;
            
            // Убедимся, что числовые значения корректно десериализуются
            $this->priceFrom = is_numeric($this->priceFrom) ? (float) $this->priceFrom : null;
            $this->priceTo = is_numeric($this->priceTo) ? (float) $this->priceTo : null;
            
            // Убедимся, что массивы корректно десериализуются
            $this->selectedBrands = array_map('intval', array_values(array_filter((array) $this->selectedBrands)));
            
            // Получаем информацию о выбранных параметрах для логирования
            $selectedParamInfo = [];
            if (!empty($this->selectedVariations)) {
                $selectedParamItems = \App\Models\ProductParamItem::whereIn('id', $this->selectedVariations)->get();
                foreach ($selectedParamItems as $item) {
                    $selectedParamInfo[$item->id] = [
                        'title' => $item->title,
                        'value' => $item->value
                    ];
                }
            }
            
            \Illuminate\Support\Facades\Log::info('Компонент десериализован', [
                'selectedVariations' => $this->selectedVariations,
                'selectedParams' => $this->selectedParams,
                'selectedParamInfo' => $selectedParamInfo,
                'priceFrom' => $this->priceFrom,
                'priceTo' => $this->priceTo,
                'selectedBrands' => $this->selectedBrands
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Ошибка в методе hydrate', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Отправляем событие об ошибке
            $this->dispatch('filter-error', 'Произошла ошибка при десериализации компонента: ' . $e->getMessage());
            
            // Сбрасываем состояние в случае ошибки
            $this->selectedVariations = [];
            $this->selectedParams = [];
        }
    }
}
