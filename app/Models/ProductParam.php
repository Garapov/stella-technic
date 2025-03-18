<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductParam extends Model
{
    /** @use HasFactory<\Database\Factories\ProductParamFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'allow_filtering',
        'show_on_preview',
        'show_on_table'
    ];

    public function params(): HasMany
    {
        return $this->hasMany(ProductParamItem::class);
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($param) {
            foreach($param->params as $item) {
                $item->delete();
            }
        });
    }
}
