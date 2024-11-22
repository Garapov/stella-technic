<?php

namespace App\Providers;

use Datlechin\FilamentMenuBuilder\Resources\MenuResource;
use Illuminate\Support\ServiceProvider;
use Z3d0X\FilamentFabricator\Resources\PageResource;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // PageResource::navigationGroup('Настройки сайта');
        // MenuResource::navigationGroup('Настройки сайта');
        // PageResource::navigationLabel('Страницы');
        // MenuResource::navigationLabel('Меню');
    }
}
