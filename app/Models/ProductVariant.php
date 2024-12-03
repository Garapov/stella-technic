<?php

namespace App\Models;

use App\Forms\Components\ImagePicker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Outerweb\ImageLibrary\Models\Image;

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
        'is_default'
    ];

    protected $casts = [
        'is_default' => 'boolean'
    ];

    protected $with = [
        'img'
    ];

    protected static function booted()
    {
        static::creating(function ($variant) {
            // Check if this is the first variant for the product
            if (!$variant->product->variants()->exists()) {
                $variant->is_default = true;
            }
        });
    }

    public function img(): BelongsTo
    {
        return $this->belongsTo(Image::class, 'image');
    }

    public function param(): BelongsTo
    {
        return $this->belongsTo(ProductParamItem::class, 'product_param_item_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
