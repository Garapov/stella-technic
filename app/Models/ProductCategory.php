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

class ProductCategory extends Model
{
    /** @use HasFactory<\Database\Factories\ProductCategoryFactory> */
    use HasFactory, ModelTree, HasSlug;

    protected $fillable = [
        'icon',
        'title',
        'slug',
        'description',
        'is_visible',
        "parent_id",
        "order"
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

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function categories(): HasMany
    {
        return $this->hasMany(ProductCategory::class, 'parent_id', 'id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}
