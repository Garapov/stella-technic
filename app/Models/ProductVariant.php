<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Outerweb\ImageLibrary\Models\Image;
use Spatie\Sluggable\HasSlug;
use Illuminate\Support\Str;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Support\Facades\Log;

class ProductVariant extends Model
{
    /** @use HasFactory<\Database\Factories\ProductVariantFactory> */
    use HasFactory, SoftDeletes, HasSlug;

    protected $fillable = [
        "product_id",
        "product_param_item_id",
        "name",
        "sku",
        "price",
        "new_price",
        "image",
        "is_default",
        "slug",
        "short_description",
        "description",
        "is_popular",
        "count",
        "synonims",
        "gallery",
    ];

    protected $casts = [
        "gallery" => "array",
        "is_default" => "boolean",
        "is_popular" => "boolean",
    ];

    protected $dates = ["deleted_at"];

    protected $with = ["img"];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom("name")
            ->saveSlugsTo("slug")
            ->doNotGenerateSlugsOnUpdate();
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return "slug";
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($variant) {
            if (
                !$variant->product
                    ->variants()
                    ->where("is_default", true)
                    ->exists()
            ) {
                $variant->is_default = true;
            }

            if (!$variant->sku) {
                $variant->sku = (string) Str::random(10);
            }
            Log::info("Creating product variant", ["variant" => $variant]);
        });
    }

    public function img(): BelongsTo
    {
        return $this->belongsTo(Image::class, "image");
    }

    public function paramItems(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductParamItem::class,
            "product_variant_product_param_item"
        )->withTimestamps();
    }

    public function parametrs(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductParamItem::class,
            "variation_product_param_item"
        )->withTimestamps();
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
