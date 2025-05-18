<?php

use App\Console\Commands\ScheduledExecutions;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        using: function () {
            Route::middleware('api')
                ->prefix('oauth')
                ->group(__DIR__ . '/../routes/oauth.php');
            Route::middleware('api')
                ->prefix('api')
                ->group(__DIR__ . '/../routes/api.php');
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo('login');
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);
    })
    ->withCommands([
        ScheduledExecutions::class,
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
