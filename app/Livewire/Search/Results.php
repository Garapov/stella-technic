<?php

namespace App\Livewire\Search;

use App\Models\ProductCategory;
use Livewire\Component;
use App\Models\ProductVariant;
use Livewire\Attributes\Url;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class Results extends Component
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

        $results['products'] = ProductVariant::search($this->q)->get();
        $results['categories'] = ProductCategory::where('title', 'like', "%{$this->q}%")->get();
        // $results['pages'] = Page::where('title', 'like', "%{$this->q}%")->get();

        return view('livewire.search.results', [
            'results' => $results
        ]);
    }
}
