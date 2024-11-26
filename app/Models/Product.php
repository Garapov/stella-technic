<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'price',
        'new_price',
        'count',
        'gallery'
    ];

    protected $casts = [
        'gallery' => 'array'
    ];

    protected $with = [
        'paramItems',
        'categories'
    ];

    public function paramItems(): BelongsToMany
    {
        return $this->belongsToMany(ProductParamItem::class, 'product_product_param_item')
            ->withTimestamps();
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ProductCategory::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    // public static function boot(): void
    // {
    //     parent::boot();

    //     static::saved(function (Product $model) {
    //         // This event fires after all relationships are synced
    //         $paramItems = $model->paramItems;
    //         dd($paramItems); // Now you'll see the paramItems
    //     });
    // }
}
