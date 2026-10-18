<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Uses the language the person chose (kept in the session), or the default.
 * The language is switched on the server, so every page, validation message
 * and email is translated the same way.
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $available = (array) config('app.available_locales', []);
        $chosen = $request->hasSession() ? $request->session()->get('locale') : null;

        if (is_string($chosen) && in_array($chosen, $available, true)) {
            app()->setLocale($chosen);
        }

        return $next($request);
    }
}
