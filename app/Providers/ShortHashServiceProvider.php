<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ShortHashService;

class ShortHashServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('short-hash', fn() => new ShortHashService());
    }
}
