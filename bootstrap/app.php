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
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin.auth' => \App\Http\Middleware\AdminAuth::class,
            'user.auth' => \App\Http\Middleware\UserAuth::class,
            'rate.limit' => \App\Http\Middleware\RateLimitMiddleware::class,
            'security.headers' => \App\Http\Middleware\SecurityHeadersMiddleware::class,
            'input.validation' => \App\Http\Middleware\InputValidationMiddleware::class,
            'cloudflare.bypass' => \App\Http\Middleware\CloudflareBypass::class,
            'login.csp' => \App\Http\Middleware\LoginCSPMiddleware::class,
            'dev.csp' => \App\Http\Middleware\DevelopmentCSPMiddleware::class,
        ]);
        
        // Apply security middleware globally
        $middleware->append(\App\Http\Middleware\SecurityHeadersMiddleware::class);
        $middleware->append(\App\Http\Middleware\InputValidationMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
