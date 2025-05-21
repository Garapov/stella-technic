<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Outerweb\ImageLibrary\Models\Image;
use Spatie\Sluggable\HasSlug;
use Illuminate\Support\Str;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Support\Facades\Log;
use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use Filament\Support\Assets\Asset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductVariant extends Model
{
    /** @use HasFactory<\Database\Factories\ProductVariantFactory> */
    use HasFactory, SoftDeletes, HasSlug, Filterable, Sortable;


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
        "links",
        "auth_price",
        "seo",
        "is_constructable",
        "constructor_type",
        'rows',
        'files',
        'show_category_files'
    ];

    protected $casts = [
        "gallery" => "array",
        "is_default" => "boolean",
        "is_popular" => "boolean",
        "seo" => "array",
        "rows" => "array",
        "files" => "array"
    ];

    protected $dates = ["deleted_at"];

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
            $variant->is_default = false;

            if (!$variant->sku) {
                $variant->sku = (string) Str::random(10);
            }
        });

        static::deleted(function ($model) {
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

    public function getActualPrice()
    {
        $price = $this->price;

        if (Auth::id() && $this->auth_price) $price = $this->auth_price;

        return $price;
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    /**
     * @param int $bytes Number of bytes (eg. 25907)
     * @param int $precision [optional] Number of digits after the decimal point (eg. 1)
     * @return string Value converted with unit (eg. 25.3KB)
     */
    public function formatBytes($bytes, $precision = 2) {
        $unit = ["b", "kb", "mb", "gb"];
        $exp = floor(log($bytes, 1024)) | 0;
        return round($bytes / (pow(1024, $exp)), $precision).$unit[$exp];
    }

}
