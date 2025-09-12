<?php

namespace App\Livewire\General\Header;

use App\Models\ProductCategory;
use App\Models\ProductVariant;
use Livewire\Component;
use Livewire\Attributes\Url;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class Search extends Component
{
    #[Url]
    public $q = '';
    public $results;

    public function mount()
    {
        $this->results = [
            'products' => new Collection(),
            'categories' => new Collection(),
        ];
        $this->getQueryResult();
    }

    public function updatedQ()
    {
        if (Str::length($this->q) >= 3) {
            $this->getQueryResult();
        } else {
            $this->results = [
                'products' => new Collection(),
                'categories' => new Collection(),
            ];
        }
    }

    public function getQueryResult()
    {
        if ($this->q == '') return;
        $this->results = [
            'products' => ProductVariant::search($this->q)
                ->where('is_hidden', false)
                ->where('product_is_hidden', false)
                ->take(10)
                ->get(),
            'categories' => ProductCategory::where('title', 'like', "%{$this->q}%")->get()
        ];
        $this->dispatch('queryUpdated', query: $this->q);
        // $results['pages'] = Page::where('title', 'like', "%{$this->q}%")->get();
    }

    public function render()
    {
        return view('livewire.general.header.search', [
            'results' => $this->results
        ]);
    }
}
