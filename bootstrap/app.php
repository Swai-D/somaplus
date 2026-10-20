<?php

use App\Http\Middleware\EnsurePlanAllows;
use App\Http\Middleware\EnsureTenantWritable;
use App\Http\Middleware\ResolveTenant;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Railway ends TLS at its proxy, so PHP sees plain http. Believe the proxy about the scheme
        // (and client address, port) so asset() and route() build https:// links; otherwise the
        // browser blocks the http:// stylesheets as mixed content. X-Forwarded-Host is deliberately
        // NOT trusted: ResolveTenant picks the school from the host, and no header may override that.
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO,
        );

        $middleware->web(prepend: [
            ResolveTenant::class,
        ]);

        // After the session has started, so the chosen language can be read.
        $middleware->web(append: [
            SetLocale::class,
        ]);

        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo(fn () => route('dashboard'));

        $middleware->alias([
            'plan' => EnsurePlanAllows::class,
            'tenant.writable' => EnsureTenantWritable::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
