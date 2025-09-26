<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
// use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Outerweb\ImageLibrary\Models\Image;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;
use Illuminate\Support\Str;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory, HasSlug, SoftDeletes;


    protected $fillable = [
        "name",
        "slug",
        "image",
        "price",
        "new_price",
        "count",
        "gallery",
        "short_description",
        "description",
        "synonims",
        "is_popular",
        "uuid",
        "links",
        "sku",
        "is_hidden",
        "category_id"
    ];

    protected $casts = [
        "gallery" => "array",
        "links" => "array",
    ];

    protected $with = ["paramItems", "categories", "variants"];


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

    public function paramItems(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductParamItem::class,
            "product_product_param_item"
        )->withTimestamps();
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductCategory::class,
            "product_product_category"
        );
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
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

        static::deleted(function ($model) {
            if ($model->variants->isNotEmpty()) {
                // dd($model->variants);
                $model->variants->each(function ($variant) {
                    $variant->forceDelete();
                });
            }
            // Удаление файлов изображений из галереи
            if (!empty($model->gallery) && is_array($model->gallery)) {
                foreach ($model->gallery as $imagePath) {
                    $fullPath = public_path($imagePath);
                    if (file_exists($fullPath)) {
                        try {
                            unlink($fullPath);
                        } catch (\Exception $e) {
                            Log::error(
                                "Ошибка при удалении файла изображения",
                                [
                                    "path" => $fullPath,
                                    "error" => $e->getMessage(),
                                    "product_id" => $model->id,
                                ]
                            );
                        }
                    }
                }
            }
        });
    }

    public function makeProductVariations()
    {
        $existingVariationsIds = ProductVariant::where("product_id", $this->id)
            ->pluck("id")
            ->toArray();
        $createdVariationsIds = [];

        foreach ($this->links as $link) {
            $name = "";
            $name .= $this->name;
            $links = "";

            // Собираем параметры для привязки к вариации
            $paramIds = [];

            foreach ($link["row"] as $param) {
                $parametr = ProductParamItem::where("id", intval($param))->first();

                if (!$parametr) {
                    continue;
                }

                // Добавляем параметр в список для привязки
                $paramIds[] = $param;
                $links .= $param;

                $name .= " {$parametr->title}";
            }

            $findedVariant = ProductVariant::withTrashed()->firstOrCreate(
                [
                    "product_id" => $this->id,
                    "links" => $links,
                ],
                [
                    "price" => $this->price,
                    "new_price" => $this->new_price,
                    "image" => $this->image,
                    "gallery" => $this->gallery,
                    "short_description" => $this->short_description,
                    "description" => $this->description,
                    "is_popular" => $this->is_popular,
                    "count" => $this->count,
                    "synonims" => $this->synonims,
                    "name" => $name,
                ]
            );

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
            ProductVariant::whereIn(
                "id",
                array_diff($existingVariationsIds, $createdVariationsIds)
            )->delete();
        }
    }
}
