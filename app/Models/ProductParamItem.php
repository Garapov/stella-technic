<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductParamItem extends Model
{
    /** @use HasFactory<\Database\Factories\ProductParamItemFactory> */
    use HasFactory;

    protected $fillable = [
        'product_param_id',
        'title',
        'value',
    ];
}
