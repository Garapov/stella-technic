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

        
        $rawResults = ProductVariant::search($this->q, function ($meilisearch, $query, $options) {
                    $options['showRankingScore'] = true;
                    return $meilisearch->search($query, $options);
                })->raw();

                
        $results = [
            'products' => ProductVariant::search($this->q)
                ->where('is_hidden', false)
                ->where('product_is_hidden', false)
                ->take(10)
                ->get()
                ->map(function ($productVariant) use ($rawResults) {
                    $productVariant->score = collect($rawResults['hits'])->where('sku', $productVariant->sku)->first()['_rankingScore'] ?? 0;
                    return $productVariant;
                })
                ->filter(fn($productVariant) => $productVariant->score > 0.7),
            'categories' => ProductCategory::search($this->q)->get()
        ];

        // $results['pages'] = Page::where('title', 'like', "%{$this->q}%")->get();

        return view('livewire.search.results', [
            'results' => $results
        ]);
    }
}
