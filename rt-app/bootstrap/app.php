<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Global security middleware (applied to all requests)
        $middleware->web(append: [
            \App\Http\Middleware\AdvancedSecurityMiddleware::class,
            \App\Http\Middleware\DDoSProtection::class,
            \App\Http\Middleware\SqlInjectionProtection::class,
            \App\Http\Middleware\XSSProtection::class,
            \App\Http\Middleware\SecurityHeaders::class,
        ]);

        // Authentication rate limiting
        $middleware->alias([
            'auth.rate_limit' => \App\Http\Middleware\RateLimitAuth::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
