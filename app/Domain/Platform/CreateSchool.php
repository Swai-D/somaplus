<?php

namespace App\Domain\Platform;

use App\Models\Tenant;
use App\Models\User;
use App\Support\SchoolAddress;
use App\Tenancy\TenantContext;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

/**
 * Opens a new school: the school itself (its five roles are added when it is created), and its first owner. The
 * owner gets a random password nobody sees and is sent a link to choose their own, so no password is ever shown
 * or stored in plain text. Runs on the central address, by a platform administrator.
 */
final class CreateSchool
{
    public function __construct(private readonly SchoolAddress $address) {}

    /**
     * @param  array{name: string, slug: string, domain?: string|null, subscription_plan_id: int, currency: string, owner_name: string, owner_email: string}  $data
     * @return array{school: Tenant, link_sent: bool}
     */
    public function handle(array $data): array
    {
        $school = DB::transaction(function () use ($data): Tenant {
            $school = new Tenant([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'domain' => ($data['domain'] ?? null) ?: null,
                'subscription_plan_id' => $data['subscription_plan_id'],
                'subscription_status' => 'active',
            ]);
            $school->currency = strtoupper($data['currency']);
            $school->save();

            TenantContext::run($school, function () use ($data): void {
                $owner = new User([
                    'name' => $data['owner_name'],
                    'email' => $data['owner_email'],
                    'password' => bcrypt(Str::random(40)),
                ]);
                $owner->email_verified_at = now();
                $owner->save();
                $owner->assignRole('owner');
            });

            return $school;
        });

        // Without an address of its own the school cannot be reached from a link (Railway path entry): no link is sent.
        $sent = $this->address->isReachable($school)
            && TenantContext::run($school, fn (): bool => Password::sendResetLink(['email' => $data['owner_email']]) === Password::RESET_LINK_SENT);

        return ['school' => $school, 'link_sent' => $sent];
    }
}
