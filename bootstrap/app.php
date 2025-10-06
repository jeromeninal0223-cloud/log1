<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'check.vendor.status' => \App\Http\Middleware\CheckVendorStatus::class,
            'role' => \App\Http\Middleware\CheckUserRole::class,
            'require.2fa' => \App\Http\Middleware\Require2FA::class,
            'audit.log' => \App\Http\Middleware\AuditLogMiddleware::class,
        ]);
        
        // Add audit logging to web middleware group
        $middleware->web(append: [
            \App\Http\Middleware\AuditLogMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
