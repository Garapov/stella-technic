<?php

namespace App\Filament\Plugins;

use App\Filament\Resources\BlogAuthorResource;
use App\Filament\Resources\BlogCategoryResource;
use App\Filament\Resources\BlogPostResource;
use Override;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Stephenjude\FilamentBlog\BlogPlugin as FilamentBlogBlogPlugin;
// use Stephenjude\FilamentBlog\Resources\AuthorResource;
// use Stephenjude\FilamentBlog\Resources\CategoryResource;
// use Stephenjude\FilamentBlog\Resources\PostResource;

class BlogPlugin extends FilamentBlogBlogPlugin implements Plugin
{
    #[Override]
    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                BlogAuthorResource::class,
                BlogCategoryResource::class,
                BlogPostResource::class,
            ]);
    }
}