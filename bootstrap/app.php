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
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to interact this url.'
                ], 403);
            }
        });
    })
    ->create();
