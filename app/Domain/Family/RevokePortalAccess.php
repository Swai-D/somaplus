<?php

namespace App\Domain\Family;

use App\Domain\Settings\RecordAudit;
use App\Models\Guardian;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Takes a guardian's access to the family pages away: the account loses its role and its password, any sign-in on
 * another device is ended, and the guardian is no longer linked to it. The account and the guardian record stay
 * (nothing is deleted); giving access again makes a new account.
 */
final class RevokePortalAccess
{
    public function __construct(private readonly RecordAudit $audit) {}

    public function handle(Guardian $guardian): void
    {
        $user = $guardian->user;

        DB::transaction(function () use ($guardian, $user): void {
            if ($user !== null) {
                $user->syncRoles([]);
                $user->forceFill(['password' => bcrypt(Str::random(40)), 'remember_token' => null])->save();

                if (config('session.driver') === 'database') {
                    DB::table(config('session.table', 'sessions'))->where('user_id', $user->id)->delete();
                }
            }

            $guardian->forceFill(['user_id' => null])->save();
            $this->audit->handle('portal.revoked', $guardian);
        });
    }
}
