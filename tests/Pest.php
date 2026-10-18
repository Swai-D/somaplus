<?php

use App\Models\Tenant;
use App\Models\User;
use App\Tenancy\TenantContext;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

pest()->extend(TestCase::class)->use(RefreshDatabase::class)->in('Feature');

/**
 * A throwaway table for Tests\Support\TenantNote, a model that uses
 * BelongsToTenant, so tenancy behaviour can be tested before any real
 * tenant-owned table exists.
 */
function createTenantNotesTable(): void
{
    Schema::dropIfExists('tenant_notes');

    Schema::create('tenant_notes', function (Blueprint $table) {
        $table->id();
        $table->foreignUlid('tenant_id')->constrained('tenants');
        $table->string('body');
        $table->timestamps();
    });
}

/**
 * Hosts used by tests that go through ResolveTenant: the central address is
 * http://somaplus.test and a school is reached at http://<slug>.somaplus.test.
 */
function useTestHosts(): void
{
    config()->set('tenancy.base_domain', 'somaplus.test');
    config()->set('tenancy.central_domains', ['somaplus.test']);
}

/**
 * A user of the given school, optionally with a role, created inside that school.
 *
 * @param  array<string, mixed>  $attributes
 */
function makeSchoolUser(Tenant $school, array $attributes = [], ?string $role = null): User
{
    return TenantContext::run($school, function () use ($attributes, $role) {
        $user = User::factory()->create($attributes);

        if ($role !== null) {
            $user->assignRole($role);
        }

        return $user;
    });
}

/**
 * A user of the given school with the given role, loaded fresh the way a request
 * would load it (so no relation cached while the test set things up).
 */
function userWithRole(Tenant $school, string $role): User
{
    $user = makeSchoolUser($school, [], $role);

    return TenantContext::runAsSystem(fn () => User::findOrFail($user->id));
}
