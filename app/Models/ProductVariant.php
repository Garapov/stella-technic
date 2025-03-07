<?php

namespace App\Models;

use App\Forms\Components\ImagePicker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Outerweb\ImageLibrary\Models\Image;
use Illuminate\Support\Str;

class ProductVariant extends Model
{
    /** @use HasFactory<\Database\Factories\ProductVariantFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'product_param_item_id',
        'name',
        'sku',
        'price',
        'new_price',
        'image',
        'sku',
        'is_default'
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    protected $with = [
        'img'
    ];

    protected static function booted()
    {
        static::creating(function ($variant) {
            if (!$variant->product->variants()->where('is_default', true)->exists()) {
                $variant->is_default = true;
            }

            if (!$variant->sku) {
                $variant->sku = (string) Str::random(10);
            }
        });
    }

    public function img(): BelongsTo
    {
        return $this->belongsTo(Image::class, 'image');
    }

    public function paramItems(): BelongsToMany
    {
        return $this->belongsToMany(ProductParamItem::class, 'product_variant_product_param_item')
            ->withTimestamps();
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
