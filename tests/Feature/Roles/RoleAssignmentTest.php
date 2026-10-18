<?php

use App\Models\Tenant;
use App\Models\User;
use App\Tenancy\TenancyViolation;
use App\Tenancy\TenantContext;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    $this->schoolA = Tenant::factory()->create();
    $this->schoolB = Tenant::factory()->create();

    $this->teacher = TenantContext::run($this->schoolA, function () {
        $user = User::factory()->create();
        $user->assignRole('teacher');

        return $user;
    });

    // A new instance each time, so a cached roles relation never hides a leak.
    $this->freshTeacher = fn () => TenantContext::runAsSystem(fn () => User::find($this->teacher->id));
});

it('lets a user have a role inside their own school', function () {
    $has = TenantContext::run($this->schoolA, fn () => ($this->freshTeacher)()->hasRole('teacher'));

    expect($has)->toBeTrue();
});

it('does not carry a role over to another school', function () {
    $has = TenantContext::run($this->schoolB, fn () => ($this->freshTeacher)()->hasRole('teacher'));

    expect($has)->toBeFalse();
});

it('does not give a role outside any school', function () {
    expect(($this->freshTeacher)()->hasRole('teacher'))->toBeFalse();
});

it('assigns a role object of the current school', function () {
    $role = Role::where('tenant_id', $this->schoolA->id)->where('name', 'accountant')->firstOrFail();

    $has = TenantContext::run($this->schoolA, function () use ($role) {
        $user = ($this->freshTeacher)();
        $user->assignRole($role);

        return ($this->freshTeacher)()->hasRole('accountant');
    });

    expect($has)->toBeTrue();
});

it('refuses a role object that belongs to another school', function () {
    $roleOfB = Role::where('tenant_id', $this->schoolB->id)->where('name', 'owner')->firstOrFail();

    TenantContext::run($this->schoolA, fn () => ($this->freshTeacher)()->assignRole($roleOfB));
})->throws(TenancyViolation::class, 'other than the current one');

it('refuses a role object that is mixed into a list with a role of another school', function () {
    $roleOfB = Role::where('tenant_id', $this->schoolB->id)->where('name', 'owner')->firstOrFail();

    TenantContext::run($this->schoolA, fn () => ($this->freshTeacher)()->assignRole('accountant', [$roleOfB]));
})->throws(TenancyViolation::class);

it('cannot assign a role by name outside any school', function () {
    ($this->freshTeacher)()->assignRole('owner');
})->throws(RoleDoesNotExist::class);

it('takes the permissions team from the current tenant', function () {
    $registrar = app(PermissionRegistrar::class);

    expect($registrar->getPermissionsTeamId())->toBeNull();

    TenantContext::run($this->schoolA, function () use ($registrar) {
        expect($registrar->getPermissionsTeamId())->toBe($this->schoolA->id);
    });
});

it('refuses to set the permissions team to anything but the current tenant', function () {
    TenantContext::run($this->schoolA, function () {
        setPermissionsTeamId($this->schoolB->id);
    });
})->throws(LogicException::class, 'cannot be set directly');

it('accepts setting the permissions team to the current tenant', function () {
    TenantContext::run($this->schoolA, function () {
        setPermissionsTeamId($this->schoolA);

        expect(getPermissionsTeamId())->toBe($this->schoolA->id);
    });
});
