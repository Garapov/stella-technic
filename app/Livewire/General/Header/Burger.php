<?php

namespace App\Livewire\General\Header;

use App\Models\ProductCategory;

use Livewire\Component;

class Burger extends Component
{
    public $categories;
    public $allCategoryIds;
    public $variationCounts;
    public $minPrices;
    public function mount($categories = [], $allCategoryIds = [], $variationCounts = [], $minPrices = [])
    {
        $this->categories = $categories;
        $this->allCategoryIds = $allCategoryIds;
        $this->variationCounts = $variationCounts;
        $this->minPrices = $minPrices;
    }
    public function render()
    {
        return view('livewire.general.header.burger', [
            "categories" => $this->categories,
        ]);
    }
}
