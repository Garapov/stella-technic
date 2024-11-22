<?php

namespace App\Filament\Resources;

use Datlechin\FilamentMenuBuilder\Resources\MenuResource as ResourcesMenuResource;

class MenuResource extends ResourcesMenuResource
{
    protected static ?string $navigationGroup = 'Настройки сайта';
    protected static ?string $navigationLabel = 'Меню';
}
