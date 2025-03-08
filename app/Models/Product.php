<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
// use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Outerweb\ImageLibrary\Models\Image;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;
use Illuminate\Support\Str;


class Product extends Model implements Searchable
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory, HasSlug, SoftDeletes;

    public $searchableType = 'Товары';

    protected $fillable = [
        'name',
        'slug',
        'image',
        'price',
        'new_price',
        'count',
        'gallery',
        'short_description',
        'description',
        'synonims',
        'is_popular',
        'uuid',
        'links',
        'sku'
    ];

    protected $casts = [
        'gallery' => 'array',
        'links' => 'array',
    ];

    protected $with = [
        'paramItems',
        'categories',
        'variants',
        'img',
    ];

    public function getSearchResult(): SearchResult
     {
        $url = route('client.product_detail', $this->slug);

        // dd($this);
        $searchResult = new \Spatie\Searchable\SearchResult(
            $this,
            $this->name,
            $url
        );

     
        return $searchResult;
     }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }



    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function paramItems(): BelongsToMany
    {
        return $this->belongsToMany(ProductParamItem::class, 'product_product_param_item')
            ->withTimestamps();
    }

    public function img(): BelongsTo
    {
        return $this->belongsTo(Image::class, 'image');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ProductCategory::class, 'product_product_category');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }
    
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->uuid) {
                $model->uuid = (string) Str::uuid();
            }

            if (!$model->sku) {
                $model->sku = (string) Str::random(10);
            }
        });

        static::deleting(function ($model) {
            if ($model->variants->isNotEmpty()) {
                // dd($model->variants);
                $model->variants->each(function ($variant) {
                    $variant->forceDelete();
                });
            }
        });
    }

    public function makeProductVariations()
    {
        $existingVariationsIds = ProductVariant::where('product_id', $this->id)->pluck('id')->toArray();
        $createdVariationsIds = [];

        foreach ($this->links as $link) {
            $name = "";
            $name .= $this->name;
            
            // Собираем параметры для привязки к вариации
            $paramIds = [];

            foreach ($link['row'] as $param) {
                $parametr = ProductParamItem::where('id', $param)->first();

                if (!$parametr) continue;

                // Добавляем параметр в список для привязки
                $paramIds[] = $param;

                $name .= " {$parametr->title}";
            }

            $findedVariant = ProductVariant::withTrashed()->firstOrCreate([
                'product_id' => $this->id,
                'name' => $name
            ], [
                'price' => $this->price,
                'new_price' => $this->new_price,
                'image' => $this->image
            ]);

            $createdVariationsIds[] = $findedVariant->id;
            
            if ($findedVariant->trashed()) {
                $findedVariant->restore();
            }
            
            // Привязываем параметры к вариации
            if (!empty($paramIds)) {
                // Синхронизируем параметры, чтобы избежать дублирования
                $findedVariant->paramItems()->sync($paramIds);
            }
        }
        
        if (!empty(array_diff($existingVariationsIds, $createdVariationsIds))) {
            ProductVariant::whereIn('id', array_diff($existingVariationsIds, $createdVariationsIds))->delete();
        }
    }
}