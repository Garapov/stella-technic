<?php

namespace App\Livewire\Catalog;

use App\Models\Category;
use Livewire\Component;

class Items extends Component
{
    public $isFiltersOpened = false;
    public $isSortingOpened = false;

    public ?Category $category = null;

    public function mount($slug)
    {
        $this->category = Category::where('slug', $slug)->first();
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
}
