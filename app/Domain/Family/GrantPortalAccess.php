<?php

namespace App\Domain\Family;

use App\Domain\Settings\RecordAudit;
use App\Models\Guardian;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Gives a guardian an account for the family pages: a user with the parent role, linked to the guardian, using the
 * guardian's email. The account gets a random password nobody sees and the guardian is sent a link to choose their
 * own. An email that already belongs to another account of the school is never taken over.
 */
final class GrantPortalAccess
{
    public function __construct(private readonly RecordAudit $audit) {}

    /** @return bool whether the email with the link was sent */
    public function handle(Guardian $guardian): bool
    {
        $email = trim((string) $guardian->email);

        if ($guardian->user_id !== null) {
            throw ValidationException::withMessages(['portal' => __('family.errors.already')]);
        }
        if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw ValidationException::withMessages(['portal' => __('family.errors.no_email')]);
        }
        if (User::query()->where('email', $email)->exists()) {
            throw ValidationException::withMessages(['portal' => __('family.errors.email_taken')]);
        }

        DB::transaction(function () use ($guardian, $email): void {
            $user = new User(['name' => $guardian->name, 'email' => $email, 'password' => bcrypt(Str::random(40))]);
            $user->forceFill(['email_verified_at' => now()])->save();
            $user->assignRole('parent_student');

            $guardian->forceFill(['user_id' => $user->id])->save();
            $this->audit->handle('portal.granted', $guardian);
        });

        return Password::sendResetLink(['email' => $email]) === Password::RESET_LINK_SENT;
    }
}
