<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductParam;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Product;

class ProductParamItem extends Model
{
    /** @use HasFactory<\Database\Factories\ProductParamItemFactory> */
    use HasFactory, Filterable;

    protected $fillable = [
        'product_param_id',
        'title',
        'value',
    ];

    protected $with = [
        'productParam'
    ];

    protected $filterFields = [
        'id',
        'title',
        'value'
      ];


    public function productParam()
    {
        return $this->belongsTo(ProductParam::class, 'product_param_id', 'id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_product_param_item');
    }
}
