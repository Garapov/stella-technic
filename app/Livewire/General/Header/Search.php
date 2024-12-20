<?php

namespace App\Livewire\General\Header;

use App\Models\Product;
use App\Models\ProductCategory;
use Livewire\Component;
use Spatie\Searchable\Search as SearchableSearch;

class Search extends Component
{
    public $searchString = '';

    public function render()
    {
        return view('livewire.general.header.search', [
            'results' => (new SearchableSearch())
                // ->registerModel(User::class, 'name')
                ->registerModel(Product::class, ['name', 'synonims'])
                ->registerModel(ProductCategory::class, ['title', 'description'])
                ->search($this->searchString)
        ]);
    }
}
