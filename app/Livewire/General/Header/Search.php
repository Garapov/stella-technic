<?php

namespace App\Livewire\General\Header;

use App\Models\Product;
use App\Models\User;
use Livewire\Component;
use Spatie\Searchable\Search as SearchableSearch;

class Search extends Component
{
    public $searchString = 'Введите что то для поиска';

    public function render()
    {
        return view('livewire.general.header.search', [
            'results' => (new SearchableSearch())
                // ->registerModel(User::class, 'name')
                ->registerModel(Product::class, 'name')
                ->search($this->searchString)
        ]);
    }
}
