<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . "/../routes/web.php",
        api: __DIR__ . "/../routes/api.php",
        commands: __DIR__ . "/../routes/console.php",
        health: "/up"
    )
    ->withMiddleware(function (Middleware $middleware) {
        // $middleware->append(CheckAllowedHostNames::class);
        $middleware->web(
            append: [
                \App\Http\Middleware\AddLastModified::class,
                \Spatie\ResponseCache\Middlewares\CacheResponse::class,
                // \App\Http\Middleware\AddGlobalPages::class,
            ]
        );
        $middleware->alias([
            "doNotCacheResponse" =>
                \Spatie\ResponseCache\Middlewares\DoNotCacheResponse::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
