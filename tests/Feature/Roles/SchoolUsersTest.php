<?php

use App\Domain\Settings\SaveCustomRole;
use App\Enums\SubscriptionStatus;
use App\Models\AuditLog;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use App\Tenancy\TenantContext;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    useTestHosts();

    $this->school = Tenant::factory()->create(['slug' => 'acme']);
    $this->other = Tenant::factory()->create(['slug' => 'beta']);
    $this->url = 'http://acme.somaplus.test';
    $this->owner = userWithRole($this->school, 'owner');

    $this->inSchool = fn (Closure $work, ?Tenant $school = null) => TenantContext::run($school ?? $this->school, $work);
    $this->valid = fn (array $override = []) => $override + ['name' => 'Neema Kimaro', 'email' => 'neema@example.com', 'password' => 'password123', 'role' => 'teacher'];
    $this->find = fn (string $email, ?Tenant $school = null) => ($this->inSchool)(fn () => User::where('email', $email)->first(), $school);
});

it('lists the people of the school with their role, and nobody from another school', function () {
    makeSchoolUser($this->other, ['name' => 'Foreign Person'], 'teacher');
    makeSchoolUser($this->school, ['name' => 'Local Person'], 'accountant');

    $this->actingAs($this->owner)->get("{$this->url}/settings/users")->assertOk()->assertSee('Local Person')->assertSee('Accountant')->assertDontSee('Foreign Person');
});

it('adds a person to the school with a role and a password that works', function () {
    $this->actingAs($this->owner)->post("{$this->url}/settings/users", ($this->valid)())->assertSessionHasNoErrors()->assertRedirect(route('settings.users.index', [], false));

    $user = ($this->find)('neema@example.com');
    expect($user->tenant_id)->toBe($this->school->id)
        ->and($user->is_super_admin)->toBeFalse()
        ->and(Hash::check('password123', $user->password))->toBeTrue()
        ->and($user->password)->not->toBe('password123')
        ->and($user->email_verified_at)->not->toBeNull()
        ->and(TenantContext::run($this->school, fn () => $user->hasRole('teacher')))->toBeTrue()
        ->and(($this->inSchool)(fn () => AuditLog::where('action', 'user.created')->where('subject_id', $user->id)->where('user_id', $this->owner->id)->exists()))->toBeTrue();
});

it('gives a new person a custom role', function () {
    $enterprise = SubscriptionPlan::factory()->withFeatures(['custom_roles'])->create();
    $this->school->forceFill(['subscription_plan_id' => $enterprise->id])->save();
    ($this->inSchool)(fn () => app(SaveCustomRole::class)->handle(null, ['name' => 'Bursar', 'permissions' => ['fees.manage']]));

    $this->actingAs($this->owner)->post("{$this->url}/settings/users", ($this->valid)(['role' => 'Bursar']))->assertSessionHasNoErrors();

    expect(TenantContext::run($this->school, fn () => ($this->find)('neema@example.com')->hasRole('Bursar')))->toBeTrue();
});

it('refuses bad input', function (array $input, string $field) {
    $this->actingAs($this->owner)->post("{$this->url}/settings/users", ($this->valid)($input))->assertSessionHasErrors($field);

    expect(($this->inSchool)(fn () => User::where('email', 'neema@example.com')->count()))->toBe(0);
})->with([
    'no name' => [['name' => ''], 'name'],
    'bad email' => [['email' => 'not-an-email'], 'email'],
    'short password' => [['password' => 'short'], 'password'],
    'no password' => [['password' => ''], 'password'],
    'unknown role' => [['role' => 'superuser'], 'role'],
]);

it('keeps an email unique inside a school but allows it at another school', function () {
    makeSchoolUser($this->school, ['email' => 'neema@example.com'], 'teacher');

    $this->actingAs($this->owner)->post("{$this->url}/settings/users", ($this->valid)())->assertSessionHasErrors('email');

    $ownerB = userWithRole($this->other, 'owner');
    $this->actingAs($ownerB)->post('http://beta.somaplus.test/settings/users', ($this->valid)())->assertSessionHasNoErrors();
});

it('changes name, email and role, keeps the password when none is given, and audits a role change', function () {
    $person = makeSchoolUser($this->school, ['name' => 'Old Name', 'email' => 'old@example.com'], 'teacher');
    $hash = $person->password;

    $this->actingAs($this->owner)->get("{$this->url}/settings/users/{$person->id}/edit")->assertOk()->assertSee('Old Name');
    $this->actingAs($this->owner)->put("{$this->url}/settings/users/{$person->id}", ($this->valid)(['name' => 'New Name', 'email' => 'new@example.com', 'password' => '', 'role' => 'accountant']))->assertSessionHasNoErrors();

    $fresh = ($this->find)('new@example.com');
    expect($fresh->name)->toBe('New Name')->and($fresh->password)->toBe($hash)
        ->and(TenantContext::run($this->school, fn () => $fresh->hasRole('accountant') && ! $fresh->hasRole('teacher')))->toBeTrue()
        ->and(($this->inSchool)(fn () => AuditLog::where('action', 'user.role_changed')->where('subject_id', $person->id)->exists()))->toBeTrue();

    $this->actingAs($this->owner)->put("{$this->url}/settings/users/{$person->id}", ($this->valid)(['name' => 'New Name', 'email' => 'new@example.com', 'password' => 'another-pass-1', 'role' => 'accountant']));
    expect(Hash::check('another-pass-1', ($this->find)('new@example.com')->password))->toBeTrue();
});

it('never leaves the school without an owner', function () {
    $this->actingAs($this->owner)->put("{$this->url}/settings/users/{$this->owner->id}", ($this->valid)(['email' => $this->owner->email, 'role' => 'teacher']))->assertSessionHasErrors('role');
    expect(TenantContext::run($this->school, fn () => User::find($this->owner->id)->hasRole('owner')))->toBeTrue();

    makeSchoolUser($this->school, ['email' => 'second@example.com'], 'owner');
    $this->actingAs($this->owner)->put("{$this->url}/settings/users/{$this->owner->id}", ($this->valid)(['email' => $this->owner->email, 'role' => 'teacher']))->assertSessionHasNoErrors();
});

it('does not find a person of another school', function () {
    $foreign = makeSchoolUser($this->other, [], 'teacher');

    $this->actingAs($this->owner)->get("{$this->url}/settings/users/{$foreign->id}/edit")->assertNotFound();
    $this->actingAs($this->owner)->put("{$this->url}/settings/users/{$foreign->id}", ($this->valid)(['role' => 'owner']))->assertNotFound();
});

it('lets only the owner manage users', function (string $role) {
    $user = userWithRole($this->school, $role);

    $this->actingAs($user)->get("{$this->url}/settings/users")->assertForbidden();
    $this->actingAs($user)->get("{$this->url}/settings/users/create")->assertForbidden();
    $this->actingAs($user)->post("{$this->url}/settings/users", ($this->valid)())->assertForbidden();
    $this->actingAs($user)->put("{$this->url}/settings/users/{$this->owner->id}", ($this->valid)(['role' => 'teacher']))->assertForbidden();

    expect(($this->find)('neema@example.com'))->toBeNull();
})->with(['head_teacher', 'teacher', 'accountant', 'parent_student']);

it('is available on every plan, can be read when read-only, and shows in the owner menu in Swahili too', function () {
    $this->actingAs($this->owner)->get($this->url)->assertSee('/settings/users', false);
    $this->actingAs($this->owner)->withSession(['locale' => 'sw'])->get("{$this->url}/settings/users")->assertOk()->assertSee('Watumiaji');

    $this->school->update(['subscription_status' => SubscriptionStatus::ReadOnly]);
    $owner = userWithRole($this->school, 'owner');
    $this->actingAs($owner)->get("{$this->url}/settings/users")->assertOk();
    $this->actingAs($owner)->post("{$this->url}/settings/users", ($this->valid)())->assertForbidden();
});
