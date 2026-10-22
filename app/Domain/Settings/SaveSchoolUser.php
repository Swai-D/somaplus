<?php

namespace App\Domain\Settings;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Adds a person to the current school or changes their name, email, password or role. A person holds
 * exactly one role here, and the school can never be left without an owner. Only the role id of the
 * change is written to the audit log, never the password.
 */
final class SaveSchoolUser
{
    public function __construct(private readonly RecordAudit $audit) {}

    /**
     * @param  array{name: string, email: string, role: string, password?: string|null}  $data
     */
    public function handle(?User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data): User {
            $created = $user === null;

            if ($user !== null && $user->hasRole('owner') && $data['role'] !== 'owner' && $this->isLastOwner($user)) {
                throw ValidationException::withMessages(['role' => __('custom_roles.users.last_owner')]);
            }

            $before = $user?->getRoleNames()->first();

            $user ??= new User;
            $user->fill(['name' => $data['name'], 'email' => $data['email']]);

            if (($data['password'] ?? '') !== '') {
                $user->password = $data['password'];
            }

            if ($created) {
                $user->forceFill(['email_verified_at' => now()]);
            }

            $user->save();
            $user->syncRoles([$data['role']]);

            if ($created) {
                $this->audit->handle('user.created', $user);
            } elseif ($before !== $data['role']) {
                $this->audit->handle('user.role_changed', $user);
            }

            return $user;
        });
    }

    private function isLastOwner(User $user): bool
    {
        return User::query()->role('owner')->whereKeyNot($user->getKey())->doesntExist();
    }
}
