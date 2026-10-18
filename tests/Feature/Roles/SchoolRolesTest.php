<?php

use App\Domain\Settings\SyncTenantRoles;
use App\Models\Tenant;
use App\Tenancy\TenantContext;
use Illuminate\Support\Facades\Lang;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

function roleNamesOf(Tenant $school): array
{
    return Role::where('tenant_id', $school->id)->pluck('name')->sort()->values()->all();
}

function configuredRoleNames(): array
{
    return collect(config('roles.roles'))->sort()->values()->all();
}

it('gives a new school every configured role', function () {
    $school = Tenant::factory()->create();

    expect(roleNamesOf($school))->toBe(configuredRoleNames());
});

it('keeps the roles of different schools separate', function () {
    $a = Tenant::factory()->create();
    $b = Tenant::factory()->create();

    expect(roleNamesOf($a))->toBe(configuredRoleNames())
        ->and(roleNamesOf($b))->toBe(configuredRoleNames())
        ->and(Role::count())->toBe(2 * count(config('roles.roles')))
        ->and(Role::where('tenant_id', $a->id)->pluck('id')->intersect(
            Role::where('tenant_id', $b->id)->pluck('id'),
        )->all())->toBe([]);
});

it('adds nothing when it syncs a school that is already up to date', function () {
    $school = Tenant::factory()->create();
    $before = Role::count();

    app(SyncTenantRoles::class)->handle($school);
    app(SyncTenantRoles::class)->handle($school);

    expect(Role::count())->toBe($before);
});

it('has an English and a Swahili label for every role', function (string $locale) {
    foreach (config('roles.roles') as $role) {
        expect(Lang::hasForLocale("roles.{$role}", $locale))->toBeTrue("{$role} has no {$locale} label");
    }
})->with(['en', 'sw']);

it('grants the configured permissions to the configured roles only', function () {
    config()->set('roles.permissions', ['students.view' => ['owner', 'teacher']]);

    $school = Tenant::factory()->create();

    TenantContext::run($school, function () {
        expect(Role::findByName('owner', 'web')->hasPermissionTo('students.view'))->toBeTrue()
            ->and(Role::findByName('teacher', 'web')->hasPermissionTo('students.view'))->toBeTrue()
            ->and(Role::findByName('accountant', 'web')->hasPermissionTo('students.view'))->toBeFalse();
    });
});

it('gives an existing school a permission added to the config later', function () {
    $school = Tenant::factory()->create();

    config()->set('roles.permissions', ['fees.collect' => ['accountant']]);
    app(SyncTenantRoles::class)->handle($school);

    TenantContext::run($school, function () {
        expect(Role::findByName('accountant', 'web')->hasPermissionTo('fees.collect'))->toBeTrue();
    });
});

it('never revokes a permission when the config no longer lists it', function () {
    config()->set('roles.permissions', ['students.view' => ['owner']]);
    $school = Tenant::factory()->create();

    config()->set('roles.permissions', []);
    app(SyncTenantRoles::class)->handle($school);

    TenantContext::run($school, function () {
        expect(Role::findByName('owner', 'web')->hasPermissionTo('students.view'))->toBeTrue();
    });
});

it('shares one permission row between schools but grants it per school', function () {
    config()->set('roles.permissions', ['students.view' => ['owner']]);

    $a = Tenant::factory()->create();
    $b = Tenant::factory()->create();

    expect(Permission::where('name', 'students.view')->count())->toBe(1);

    TenantContext::run($a, function () {
        Role::findByName('owner', 'web')->revokePermissionTo('students.view');
    });

    TenantContext::run($b, function () {
        expect(Role::findByName('owner', 'web')->hasPermissionTo('students.view'))->toBeTrue();
    });
});

it('refuses a permission that refers to an unknown role', function () {
    $school = Tenant::factory()->create();

    config()->set('roles.permissions', ['students.view' => ['nobody']]);

    app(SyncTenantRoles::class)->handle($school);
})->throws(InvalidArgumentException::class, 'unknown role [nobody]');
