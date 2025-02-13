<?php

namespace App\Models;


use Stephenjude\FilamentBlog\Models\Post as ModelsPost;
use Stephenjude\FilamentBlog\Models\Category as ModelsCategory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Datlechin\FilamentMenuBuilder\Concerns\HasMenuPanel;
use Datlechin\FilamentMenuBuilder\Contracts\MenuPanelable;


class Post extends ModelsPost implements MenuPanelable
{
    use HasMenuPanel;

    public function getMenuPanelTitleColumn(): string
    {
        return 'title';
    }

    public function getMenuPanelUrlUsing(): callable
    {
        return fn (self $model) => route('client.posts.show', [
            'category_slug' => $model->category->slug,
            'slug' => $model->slug,
        ]);
    }

    public function getMenuPanelName(): string
    {
        return "Посты блога";
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ModelsCategory::class, 'blog_category_id', 'id');
    }
}