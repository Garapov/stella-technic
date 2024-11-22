<?php
namespace App\Filament\Plugins;

use App\Filament\Resources\MenuResource as ResourcesMenuResource;
use Datlechin\FilamentMenuBuilder\FilamentMenuBuilderPlugin;
// use Datlechin\FilamentMenuBuilder\Resources\MenuResource;

class MenuBuilderPlugin extends FilamentMenuBuilderPlugin
{
    protected string $resource = ResourcesMenuResource::class;
}