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

    public function mount($products = new Collection())
    {
        $this->products = $products;
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