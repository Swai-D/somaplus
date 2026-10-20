<?php

namespace Database\Seeders;

use App\Enums\SubscriptionStatus;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use App\Tenancy\TenantContext;
use Illuminate\Database\Seeder;

/**
 * Creates the platform super-admin and one demo school with a user for every
 * school role (owner, head_teacher, teacher, accountant, parent_student).
 *
 * Every user uses the same password: `password`. Emails are unique per school,
 * so the same address can exist at two different schools.
 */
class SystemUsersSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Platform super-admin: no school, created outside any tenant context.
        TenantContext::runAsSystem(function (): void {
            User::firstOrCreate(
                ['email' => 'admin@somaplus.test'],
                [
                    'name' => 'System Administrator',
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                    'is_super_admin' => true,
                ],
            );
        });

        // 2. Demo school: Acme Secondary on the basic plan.
        $plan = SubscriptionPlan::where('key', 'basic')->first();
        $acme = Tenant::firstOrCreate(
            ['slug' => 'acme'],
            [
                'name' => 'Acme Secondary',
                'domain' => null,
                'subscription_plan_id' => $plan->id,
                'subscription_status' => SubscriptionStatus::Active,
            ],
        );

        // 3. One user per school role, created inside the school's tenant context.
        $roles = ['owner', 'head_teacher', 'teacher', 'accountant', 'parent_student'];

        $users = [
            'owner' => ['name' => 'Musa Mwakapi',    'email' => 'owner@acme.test'],
            'head_teacher' => ['name' => 'Asha Mwakapi',    'email' => 'asha@acme.test'],
            'teacher' => ['name' => 'John Kileo',      'email' => 'teacher@acme.test'],
            'accountant' => ['name' => 'Grace Salim',     'email' => 'accountant@acme.test'],
            'parent_student' => ['name' => 'Faith Mwakapi',   'email' => 'parent@acme.test'],
        ];

        foreach ($roles as $role) {
            $data = $users[$role];

            TenantContext::run($acme, function () use ($role, $data): void {
                $user = User::firstOrCreate(
                    ['email' => $data['email']],
                    [
                        'name' => $data['name'],
                        'password' => bcrypt('password'),
                        'email_verified_at' => now(),
                    ],
                );

                if (! $user->hasRole($role)) {
                    $user->assignRole($role);
                }
            });
        }
    }
}
