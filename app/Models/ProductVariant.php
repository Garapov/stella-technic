<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    ];

    public function param(): BelongsTo
    {
        return $this->belongsTo(ProductParamItem::class, 'product_param_item_id');
    }
}
