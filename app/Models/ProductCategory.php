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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;
use Illuminate\Support\Facades\Log;

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
        "image",
        "seo",
        "files"
    ];

    protected $casts = [
        'parent_id' => 'int',
        'seo' => 'array',
        'files' => 'array',
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

    public function urlChain()
    {
        $urlChain = [$this->slug];

        $currentCategory = $this;

        while ($currentCategory->parent_id && $currentCategory->parent_id != '-1') {
            
            $parentCategory = ProductCategory::find($currentCategory->parent_id);
            // Log::info(['parentCategory', $parentCategory]);
            $currentCategory = $parentCategory;
            array_unshift($urlChain, $currentCategory->slug);
        }


        return join('/', $urlChain);
    }

    public function paramItems(): BelongsToMany
    {
        return $this->belongsToMany(ProductParamItem::class, 'product_category_product_param_item')
            ->withTimestamps();
    }

    public function getSearchResult(): SearchResult
     {
        $url = route('client.catalog', $this->urlChain());

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
        return fn (self $model) => route('client.catalog', $model->urlChain());
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
        $category = $this;
        
        $minPrice = ProductVariant::query()
        ->whereHas('product', function ($query) use ($category) {
            $query->whereHas('categories', function ($q) use ($category) {
                $q->where('product_categories.id', $category->id);
            });
        })
        ->min(DB::raw('COALESCE(new_price, price)'));
        return $minPrice;
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
