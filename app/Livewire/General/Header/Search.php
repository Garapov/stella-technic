<?php

namespace App\Livewire\General\Header;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Page;
use Livewire\Component;
use Spatie\Searchable\Search as SearchableSearch;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class Search extends Component
{
    #[Url]
    public $q = '';

    public function render()
    {
        $results = [
            'products' => new Collection(),
            'categories' => new Collection(),
            // 'pages' => new Collection(),
        ];

        if (Str::length($this->q) > 3) {
            $results['products'] = Product::where('name', 'like', "%{$this->q}%")->orWhere('synonims', 'like', "%{$this->q}%")->get();
            $results['categories'] = ProductCategory::where('title', 'like', "%{$this->q}%")->get();
            // $results['pages'] = Page::where('title', 'like', "%{$this->q}%")->get();
        }

        return view('livewire.general.header.search', [
            'results' => $results
        ]);
    }
}
