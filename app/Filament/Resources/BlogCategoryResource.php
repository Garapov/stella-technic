<?php

namespace App\Filament\Resources;

use Stephenjude\FilamentBlog\Resources\CategoryResource;

class BlogCategoryResource extends CategoryResource
{
    protected static ?string $navigationGroup = 'Блог';
    protected static ?string $navigationLabel = 'Категории блога';
}