<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Builder;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\ArticleFactory> */
    use HasFactory, HasSlug;

    protected $fillable = [
        "title",
        "content",
        "image",
        "is_popular",
        "short_content",
        "created_at"
    ];

    protected $casts = [
        "content" => "array",
    ];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom("title")
            ->saveSlugsTo("slug")
            ->doNotGenerateSlugsOnUpdate();
    }

    protected static function booted(): void
    {
        static::addGlobalScope('orderByCreatedAt', function (Builder $query) {
            $query->orderByDesc('created_at'); // DESC — от новых к старым
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return "slug";
    }
}
