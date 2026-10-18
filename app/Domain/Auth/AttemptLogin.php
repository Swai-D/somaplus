<?php

namespace App\Domain\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Signs a person in. Who may sign in where is decided by the global scope on
 * User, not here: on a school's address only that school's users are found, on
 * the central address only platform administrators.
 */
final class AttemptLogin
{
    private const MAX_ATTEMPTS = 5;

    public function handle(Request $request, string $email, string $password, bool $remember): void
    {
        $key = $this->throttleKey($request, $email);

        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            event(new Lockout($request));

            $seconds = RateLimiter::availableIn($key);

            throw ValidationException::withMessages([
                'email' => __('auth.throttle', ['seconds' => $seconds, 'minutes' => (int) ceil($seconds / 60)]),
            ]);
        }

        if (! Auth::attempt(['email' => $email, 'password' => $password], $remember)) {
            RateLimiter::hit($key);

            throw ValidationException::withMessages(['email' => __('auth.failed')]);
        }

        RateLimiter::clear($key);

        $request->session()->regenerate();
    }

    private function throttleKey(Request $request, string $email): string
    {
        // The host is part of the key so a school's attempts are counted apart from another school's.
        return Str::transliterate(Str::lower($email).'|'.$request->ip().'|'.$request->getHost());
    }
}
