<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use SolutionForest\FilamentTree\Concern\ModelTree;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;
use Datlechin\FilamentMenuBuilder\Concerns\HasMenuPanel;
use Datlechin\FilamentMenuBuilder\Contracts\MenuPanelable;
use Illuminate\Support\Number;

class ProductCategory extends Model implements Searchable,MenuPanelable
{
    /** @use HasFactory<\Database\Factories\ProductCategoryFactory> */
    use HasFactory, ModelTree, HasSlug, HasMenuPanel;

    public $searchableType = 'Категории';

    protected $fillable = [
        'icon',
        'title',
        'slug',
        'description',
        'is_visible',
        "parent_id",
        "order",
        "image"
    ];

    protected $casts = [
        'parent_id' => 'int'
    ];
 
    protected $table = 'product_categories';

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function getSearchResult(): SearchResult
     {
        $url = route('client.product_detail', $this->slug);

        // dd($this);
        $searchResult = new \Spatie\Searchable\SearchResult(
            $this,
            $this->title,
            $url
        );

        // dd($searchResult);
     
        return $searchResult;
     }
    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getMenuPanelTitleColumn(): string
    {
        return 'title';
    }

    public function getMenuPanelUrlUsing(): callable
    {
        return fn (self $model) => route('client.catalog', $model->slug);
    }

    public function getMenuPanelName(): string
    {
        return "Категории товаров";
    }

    public function categories(): HasMany
    {
        return $this->hasMany(ProductCategory::class, 'parent_id', 'id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_product_category');
    }

    public function minProductPrice()
    {
        return Number::format($this->products()->where('price', '>', 0)->min('price') ?? 0, 0);
    }

    public function variationsCount()
    {
        $count = 0;

        foreach ($this->products as $product) {
            $count += $product->variants->count();
        }
        return $count;
    }

    
}
