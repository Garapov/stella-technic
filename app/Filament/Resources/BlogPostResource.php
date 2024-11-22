<?php

namespace App\Filament\Resources;

use Stephenjude\FilamentBlog\Resources\PostResource;

class BlogPostResource extends PostResource
{
    protected static ?string $navigationGroup = 'Блог';
    protected static ?string $navigationLabel = 'Записи';
}