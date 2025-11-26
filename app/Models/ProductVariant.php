<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class ProductVariant extends Model
{
    /** @use HasFactory<\Database\Factories\ProductVariantFactory> */
    use Filterable, HasFactory, HasSlug, Searchable, SoftDeletes, Sortable;

    protected $fillable = [
        'product_id',
        'product_param_item_id',
        'name',
        'sku',
        'price',
        'new_price',
        'image',
        'is_default',
        'slug',
        'short_description',
        'description',
        'is_popular',
        'count',
        'synonims',
        'gallery',
        'links',
        'auth_price',
        'seo',
        'is_constructable',
        'constructor_type',
        'rows',
        'files',
        'show_category_files',
        'selected_width',
        'selected_height',
        'selected_desk_type',
        'selected_position',
        'uuid',
        'is_pre_order',
        'deleted_at',
        'is_hidden',
        'h1',
        'videos',
        'is_rebate',
    ];

    protected $casts = [
        'gallery' => 'array',
        'videos' => 'array',
        'is_default' => 'boolean',
        'is_popular' => 'boolean',
        'is_hidden' => 'boolean',
        'seo' => 'array',
        'rows' => 'array',
        'files' => 'array',
    ];

    protected $dates = ['deleted_at'];

    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
            'sku' => $this->sku,
            'synonims' => $this->synonims,
            'is_hidden' => (bool) $this->is_hidden,
            'product_is_hidden' => (bool) optional($this->product)->is_hidden,
        ];
    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function urlChain()
    {
        return Cache::rememberForever('variations:urls:'.$this->slug, function () {
            $urlChain = [];

            $currentCategory = ProductCategory::find($this->product->category_id && $this->product->category_id != 0 ? $this->product->category_id : $this->product->categories->last()->id);

            array_unshift($urlChain, $currentCategory->slug);

            while ($currentCategory->parent_id && $currentCategory->parent_id != '-1') {
                $parentCategory = ProductCategory::find($currentCategory->parent_id);
                $currentCategory = $parentCategory;
                array_unshift($urlChain, $currentCategory->slug);
            }

            $urlChain[] = $this->slug;

            return implode('/', $urlChain);
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($variant) {
            $variant->is_default = false;

            if (! $variant->sku) {
                $variant->sku = (string) Str::random(10);
            }
        });

        static::deleted(function ($model) {
            // Collect the ids of $model->paramItems
            $paramItemIds = $model->paramItems->pluck('id')->toArray();

            // Find the $model->product->links object with a 'row' array of ids equal to the ids of $model->paramItems
            if ($product = $model->product) {
                // Ensure links is an array
                if (! is_array($product->links)) {
                    $product->links = [];
                }

                // Remove the 'row' that matches the paramItemIds
                $product->links = array_filter($product->links, function (
                    $link
                ) use ($paramItemIds) {
                    return ! isset($link['row']) ||
                        array_intersect($paramItemIds, $link['row']) !==
                            $paramItemIds;
                });

                // Save the remaining data
                $product->save();
            }
        });
    }

    public function paramItems(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductParamItem::class,
            'product_variant_product_param_item'
        )->withTimestamps();
    }

    public function parametrs(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductParamItem::class,
            'variation_product_param_item'
        )->withTimestamps();
    }

    public function getActualPrice()
    {
        $price = $this->price;

        if (Auth::id() && $this->auth_price) {
            $price = $this->auth_price;
        }

        return $price;
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function getStatus()
    {
        $status = 'available';

        if ($this->count == 0 && $this->uuid) {
            $status = 'unavailable';
        }

        return $status;
    }

    // Cross-sells
    public function crossSells(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductVariant::class,
            'cross_sells',
            'product_variant_id',
            'related_variant_id'
        );
    }

    // Upsells
    public function upSells(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductVariant::class,
            'up_sells',
            'product_variant_id',
            'related_variant_id'
        );
    }

    /**
     * @param  int  $bytes  Number of bytes (eg. 25907)
     * @param  int  $precision  [optional] Number of digits after the decimal point (eg. 1)
     * @return string Value converted with unit (eg. 25.3KB)
     */
    public function formatBytes($bytes, $precision = 2)
    {
        $unit = ['b', 'kb', 'mb', 'gb'];
        $exp = floor(log($bytes, 1024)) | 0;

        return round($bytes / pow(1024, $exp), $precision).$unit[$exp];
    }
}
