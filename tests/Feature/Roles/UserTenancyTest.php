<?php

use App\Models\Tenant;
use App\Models\User;
use App\Tenancy\TenancyViolation;
use App\Tenancy\TenantContext;
use Illuminate\Database\QueryException;

beforeEach(function () {
    $this->schoolA = Tenant::factory()->create();
    $this->schoolB = Tenant::factory()->create();

    $this->userA = TenantContext::run($this->schoolA, fn () => User::factory()->create(['name' => 'In A']));
    $this->userB = TenantContext::run($this->schoolB, fn () => User::factory()->create(['name' => 'In B']));
    $this->admin = User::factory()->superAdmin()->create(['name' => 'Platform admin']);
});

it('gives a new user the current school', function () {
    expect($this->userA->tenant_id)->toBe($this->schoolA->id)
        ->and($this->userA->isSuperAdmin())->toBeFalse();
});

it('shows a school only its own users', function () {
    $names = TenantContext::run($this->schoolA, fn () => User::pluck('name')->all());

    expect($names)->toBe(['In A']);
});

it('cannot find a user of another school by id', function () {
    $found = TenantContext::run($this->schoolA, fn () => User::find($this->userB->id));

    expect($found)->toBeNull();
});

it('shows only super-admins on the central domain', function () {
    expect(User::pluck('name')->all())->toBe(['Platform admin']);
});

it('shows every user only inside runAsSystem', function () {
    $names = TenantContext::runAsSystem(fn () => User::pluck('name')->sort()->values()->all());

    expect($names)->toBe(['In A', 'In B', 'Platform admin']);
});

it('has no school for a super-admin', function () {
    expect($this->admin->tenant_id)->toBeNull()
        ->and($this->admin->isSuperAdmin())->toBeTrue();
});

it('refuses a normal user without a school', function () {
    User::factory()->create();
})->throws(TenancyViolation::class, 'requires a current tenant');

it('refuses a super-admin created inside a school', function () {
    TenantContext::run($this->schoolA, fn () => User::factory()->superAdmin()->create());
})->throws(TenancyViolation::class, 'cannot be created for, or inside, a school');

it('refuses a super-admin that has a school', function () {
    User::factory()->superAdmin()->create(['tenant_id' => $this->schoolA->id]);
})->throws(TenancyViolation::class, 'cannot be created for, or inside, a school');

it('refuses to create a user for another school', function () {
    TenantContext::run($this->schoolA, fn () => User::factory()->create(['tenant_id' => $this->schoolB->id]));
})->throws(TenancyViolation::class, 'other than the current one');

it('refuses to move a user to another school', function () {
    TenantContext::run($this->schoolA, function () {
        $user = User::findOrFail($this->userA->id);
        $user->tenant_id = $this->schoolB->id;
        $user->save();
    });
})->throws(TenancyViolation::class, 'cannot be moved');

it('refuses to promote a user to super-admin', function () {
    TenantContext::run($this->schoolA, function () {
        $user = User::findOrFail($this->userA->id);
        $user->is_super_admin = true;
        $user->save();
    });
})->throws(TenancyViolation::class, 'cannot be promoted');

it('ignores a school or super-admin flag passed through mass assignment', function () {
    $user = TenantContext::run($this->schoolA, fn () => User::create([
        'name' => 'Sneaky',
        'email' => 'sneaky@example.test',
        'password' => 'secret-password',
        'tenant_id' => $this->schoolB->id,
        'is_super_admin' => true,
    ]));

    expect($user->tenant_id)->toBe($this->schoolA->id)
        ->and($user->isSuperAdmin())->toBeFalse();
});

it('allows the same email address at two different schools', function () {
    TenantContext::run($this->schoolA, fn () => User::factory()->create(['email' => 'same@example.test']));
    TenantContext::run($this->schoolB, fn () => User::factory()->create(['email' => 'same@example.test']));

    $count = TenantContext::runAsSystem(fn () => User::where('email', 'same@example.test')->count());

    expect($count)->toBe(2);
});

it('refuses the same email address twice at one school', function () {
    TenantContext::run($this->schoolA, function () {
        User::factory()->create(['email' => 'dup@example.test']);
        User::factory()->create(['email' => 'dup@example.test']);
    });
})->throws(QueryException::class);

it('does not delete a school that still has data', function () {
    $this->schoolA->delete();
})->throws(QueryException::class);
