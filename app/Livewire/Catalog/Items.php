<?php

namespace App\Livewire\Catalog;

// use App\Livewire\Cart\Components\Product;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductParam;
use App\Models\ProductParamItem;
use Livewire\Component;
use Livewire\WithPagination;

class Items extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $isFiltersOpened = false;
    public $isSortingOpened = false;
    public $selectedVariations = [];
    public $priceFrom = null;
    public $priceTo = null;
    public $selectedVariationNames = [];
    public $selectedSort = 'default';
    public $items = null;

    public $product_ids = [];

    public ?ProductCategory $category = null;

    protected $queryString = [
        'selectedVariationNames' => ['as' => 'variations', 'except' => []],
        'priceFrom' => ['as' => 'price_from', 'except' => null],
        'priceTo' => ['as' => 'price_to', 'except' => null],
        'selectedSort' => ['as' => 'sort', 'except' => 'default']
    ];

    public function mount($slug = null, $products = null)
    {
        
        if ($slug) {
            $this->category = ProductCategory::where('slug', $slug)->first();
        }
        if ($products) {
            $this->product_ids = $products;
        }
        // Convert variation titles to IDs if they exist in URL
        if (!empty($this->selectedVariationNames)) {
            $this->selectedVariations = ProductParamItem::whereIn('title', $this->selectedVariationNames)
                ->pluck('id')
                ->toArray();
        }

        // Set default price range if not provided in URL
        if ($this->priceFrom === null && $this->priceTo === null) {
            $priceRange = $this->getPriceRangeProperty();
            $this->priceFrom = $priceRange->min_price;
            $this->priceTo = $priceRange->max_price;
        }
    }

    public function updatedSelectedVariations($value)
    {
        // Update variation titles when IDs change
        $this->selectedVariationNames = ProductParamItem::whereIn('id', $this->selectedVariations)
            ->pluck('title')
            ->toArray();
        $this->resetPage();
    }

    public function updatedPriceFrom($value)
    {
        // Ensure priceFrom doesn't exceed priceTo
        if ($value > $this->priceTo) {
            $this->priceFrom = $this->priceTo;
        }
        $this->resetPage();
    }

    public function updatedPriceTo($value)
    {
        // Ensure priceTo isn't less than priceFrom
        if ($value < $this->priceFrom) {
            $this->priceTo = $this->priceFrom;
        }
        $this->resetPage();
    }

    public function getProductsProperty()
    {
        if ($this->category) {
            $query = $this->category->products();
        }
        if ($this->product_ids) {
            $query = Product::whereIn('id', $this->product_ids);
        }

        
        // Apply parameter filters only if there are selected variations
        if (!empty($this->selectedVariations)) {
            $query->whereHas('paramItems', function ($query) {
                $query->whereIn('product_param_items.id', $this->selectedVariations);
            }, '=', count($this->selectedVariations));
        }

        // Apply price range filter if either min or max price is set and not empty
        if (!empty($this->priceFrom) || !empty($this->priceTo)) {
            $query->where(function ($query) {
                // Handle products with new_price
                $query->where(function ($q) {
                    $q->where('new_price', '>', 0);
                    
                    if (!empty($this->priceFrom)) {
                        $q->where('new_price', '>=', $this->priceFrom);
                    }
                    if (!empty($this->priceTo)) {
                        $q->where('new_price', '<=', $this->priceTo);
                    }
                })
                // Handle products with regular price
                ->orWhere(function ($q) {
                    $q->where(function ($sq) {
                        $sq->where('new_price', 0)->orWhereNull('new_price');
                    });
                    
                    if (!empty($this->priceFrom)) {
                        $q->where('price', '>=', $this->priceFrom);
                    }
                    if (!empty($this->priceTo)) {
                        $q->where('price', '<=', $this->priceTo);
                    }
                });
            });
        }

        // Apply sorting
        switch ($this->selectedSort) {
            case 'price_asc':
                $query->orderByRaw('CASE WHEN new_price > 0 THEN new_price ELSE price END ASC');
                break;
            case 'price_desc':
                $query->orderByRaw('CASE WHEN new_price > 0 THEN new_price ELSE price END DESC');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            default:
                // Default sorting logic here if needed
                break;
        }


        if ($this->category) {
            return $query->paginate(18);
        }
        if ($this->product_ids) {
            return $query->paginate(18);
        }

        
    }

    public function getPriceRangeProperty()
    {
        if (!$this->category && !$this->product_ids) {
            return (object)[
                'min_price' => 0,
                'max_price' => 0
            ];
        }

        if ($this->category) {
        return $this->category->products()
            ->selectRaw('MIN(CASE WHEN new_price > 0 THEN new_price ELSE price END) as min_price, 
                        MAX(CASE WHEN new_price > 0 THEN new_price ELSE price END) as max_price')
            ->first() ?? (object)[
                'min_price' => 0,
                'max_price' => 0
            ];
        }

        if ($this->product_ids) {

            
            return Product::whereIn('id', $this->product_ids)
                ->selectRaw('MIN(CASE WHEN new_price > 0 THEN new_price ELSE price END) as min_price, 
                            MAX(CASE WHEN new_price > 0 THEN new_price ELSE price END) as max_price')
                ->first() ?? (object)[
                    'min_price' => 0,
                    'max_price' => 0
                ];
        }
    }

    public function getAvailableParamItemsProperty()
    {
        if ($this->category) {
            $query = $this->category->products();
        }
        if ($this->product_ids) {
            $query = Product::whereIn('id', $this->product_ids);
            
        }

        // Apply existing filters except price
        if (!empty($this->selectedVariations)) {
            $query->whereHas('paramItems', function ($query) {
                $query->whereIn('product_param_items.id', $this->selectedVariations);
            }, '=', count($this->selectedVariations));
        }

        // Apply price filter if set
        if ($this->priceFrom !== null || $this->priceTo !== null) {
            $query->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('new_price', '>', 0);
                    if ($this->priceFrom !== null) {
                        $q->where('new_price', '>=', $this->priceFrom);
                    }
                    if ($this->priceTo !== null) {
                        $q->where('new_price', '<=', $this->priceTo);
                    }
                })->orWhere(function ($q) {
                    $q->where(function ($sq) {
                        $sq->where('new_price', 0)->orWhereNull('new_price');
                    });
                    if ($this->priceFrom !== null) {
                        $q->where('price', '>=', $this->priceFrom);
                    }
                    if ($this->priceTo !== null) {
                        $q->where('price', '<=', $this->priceTo);
                    }
                });
            });
        }

        // Get all parameter items that are associated with the filtered products
        return ProductParamItem::whereHas('products', function ($q) use ($query) {
            $q->whereIn('products.id', $query->pluck('products.id'));
        })->pluck('id')->toArray();
    }

    public function getAvailableFiltersProperty()
    {
        if ($this->category) {
            $categoryProductIds = $this->category->products->pluck('id');
        }
        if ($this->product_ids) {
            $categoryProductIds = $this->product_ids;
        }
        $availableParamItems = $this->availableParamItems;

        return ProductParam::where('allow_filtering', true)
            ->whereHas('params.products', function ($query) use ($categoryProductIds) {
                $query->whereIn('products.id', $categoryProductIds);
            })
            ->with(['params' => function ($query) use ($categoryProductIds, $availableParamItems) {
                $query->whereHas('products', function ($q) use ($categoryProductIds) {
                    $q->whereIn('products.id', $categoryProductIds);
                });
                // Add a flag to indicate if the parameter item would lead to empty results
                $query->withCount(['products as would_have_results' => function ($q) use ($availableParamItems) {
                    $q->whereIn('product_param_items.id', $availableParamItems);
                }]);
            }])
            ->get();
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
        $this->selectedSort = $value;
        $this->isSortingOpened = false;
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.catalog.items');
    }

    public function toggleFilters()
    {
        $this->isFiltersOpened = !$this->isFiltersOpened;
    }

    public function closeFilters()
    {
        $this->isFiltersOpened = false;
    }

    public function toggleSorting()
    {
        $this->isSortingOpened = !$this->isSortingOpened;
    }

    public function closeSorting()
    {
        $this->isSortingOpened = false;
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
        $this->selectedVariations = [];
        $this->selectedVariationNames = [];
        $priceRange = $this->getPriceRangeProperty();
        $this->priceFrom = $priceRange->min_price;
        $this->priceTo = $priceRange->max_price;
        $this->selectedSort = 'default';
        $this->dispatch('filter-reset');
        $this->resetPage();
    }
}
