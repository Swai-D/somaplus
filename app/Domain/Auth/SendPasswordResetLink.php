<?php

namespace App\Domain\Auth;

use Illuminate\Support\Facades\Password;

/**
 * Sends a reset link to a person of the current school (or, on the central
 * address, to a platform administrator). The person is looked up through the
 * global scope on User, so an address that belongs to another school is simply
 * not found here. The caller must give the same answer whether or not it was.
 */
final class SendPasswordResetLink
{
    public function handle(string $email): void
    {
        Password::sendResetLink(['email' => $email]);
    }
}
