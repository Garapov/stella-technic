<?php

namespace App\Livewire\Search;

use App\Models\Product;
use App\Models\ProductCategory;
use Livewire\Component;
use App\Models\Page;
use Spatie\Searchable\Search as SearchableSearch;
use Livewire\Attributes\Url;
class Results extends Component
{
    #[Url]
    public $q = '';

    public function render()
    {
        return view('livewire.search.results', [
            'results' => (new SearchableSearch())
                // ->registerModel(User::class, 'name')
                ->registerModel(Product::class, ['name', 'synonims'])
                ->registerModel(ProductCategory::class, ['title', 'description'])
                ->registerModel(Page::class, ['title'])
                ->search($this->q)
        ]);
    }
}
