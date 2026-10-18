<?php

use App\Http\Middleware\EnsurePlanAllows;
use App\Http\Middleware\EnsureTenantWritable;
use App\Http\Middleware\ResolveTenant;
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
        $middleware->web(prepend: [
            ResolveTenant::class,
        ]);

        $middleware->alias([
            'plan' => EnsurePlanAllows::class,
            'tenant.writable' => EnsureTenantWritable::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
