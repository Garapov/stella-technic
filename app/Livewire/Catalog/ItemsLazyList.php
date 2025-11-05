<?php

namespace App\Livewire\Catalog;

use App\Livewire\Cart\Components\Product;
use App\Models\ProductVariant;
use App\Services\ProductSelector;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Lazy()]
class ItemsLazyList extends Component
{
    use WithPagination;
    public $category = null;

    #[Url()]
    public $filters = [];

    #[Url]
    public $sort = "count:asc";

    #[Url]
    public $displayMode = "block";

    public $showSorting = false;

    protected ?ProductSelector $selector = null;

    public function boot(ProductSelector $selector)
    {
        $this->selector = $selector;
    }

    public function mount($category = null)
    {
        $this->category = $category;

        if ($this->category) {
            $this->displayMode = $this->category->viewtype;
        }
    }

    #[On("filters-changed")]
    public function updateFilters($filters)
    {
        $this->filters = $filters;

        $this->resetPage();
    }
    
    public function render()
    {
        // sleep(4);
        return view('livewire.catalog.items-lazy-list');
    }
    
    
    public function variationsBuilder()
    {
        if (!$this->selector || !$this->category) {
            return collect();
        }
        if ($this->category) {
            return ProductVariant::whereIn('id', 
                Cache::remember('catalog:all_products:' . $this->category->slug, 60, function () {
                    return $this->selector->fromCategory($this->category)->where('is_hidden', false)->pluck('id')->toArray();
                })
            )->filter($this->filters)->sort([$this->sort])->with('parametrs');
        }
        return collect();
    }

    #[Computed()]
    public function variations()
    {
        return $this->variationsBuilder()->paginate(40, pageName: 'page');
    }

    #[Computed()]
    public function batches()
    {
        $batches = $this->variationsBuilder()->whereHas('batch')->get()->groupBy('batch.name');

        // $batches->map(function ($batch) {
        //     dd($batch);
        // });

        return $batches;
    }
    

    #[Computed()]
    public function sortOptions()
    {
        return [
            "count:asc" => [
                "label" => "По умолчанию",
                "icon" => "M3 4.5h14.25M3 9h9.75M3 13.5h9.75m4.5-4.5v12m0 0l-3.75-3.75M17.25 21L21 17.25",
            ],
            "price:asc" => [
                "label" => "Подешевле",
                "icon" => "M3 4.5h14.25M3 9h9.75M3 13.5h5.25m5.25-.75L17.25 9m0 0L21 12.75M17.25 9v12",
            ],
            "price:desc" => [
                "label" => "Подороже",
                "icon" => "M3 4.5h14.25M3 9h9.75M3 13.5h5.25m5.25-.75L17.25 9m0 0L21 12.75M17.25 9v12",
            ],
            "name:asc" => [
                "label" => "По названию А-Я",
                "icon" => "M3 4.5h14.25M3 9h9.75M3 13.5h9.75m4.5-4.5v12m0 0l-3.75-3.75M17.25 21L21 17.25",
            ],
            "name:desc" => [
                "label" => "По названию Я-А",
                "icon" => "M3 4.5h14.25M3 9h9.75M3 13.5h9.75m4.5-4.5v12m0 0l-3.75-3.75M17.25 21L21 17.25",
            ],
        ];
    }

    public function updateSort($sort)
    {
        $this->sort = $sort;
    }

    public function changeDisplayMode($mode)
    {
        $this->resetPage();
        $this->displayMode = $mode;
    }

    public function placeholder()
    {
        // sleep(4);
        return view('placeholders.catalog.items-lazy-list');
    }
}
