<?php

use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsAuth;
use App\Http\Middleware\IsEmployee;
use App\Http\Middleware\IsGuest;
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
            'is.auth' => IsAuth::class,
            'is.guest' => IsGuest::class,
            'is.admin' => IsAdmin::class,
            'is.employee' => IsEmployee::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
