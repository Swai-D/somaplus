<?php

use App\Domain\Settings\RoleCatalog;
use App\Domain\Settings\SaveCustomRole;
use App\Enums\SubscriptionStatus;
use App\Models\AuditLog;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use App\Tenancy\TenantContext;
use Illuminate\Support\Facades\Lang;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    useTestHosts();

    $enterprise = SubscriptionPlan::factory()->withFeatures(['custom_roles'])->create();
    $this->school = Tenant::factory()->onPlan($enterprise)->create(['slug' => 'acme']);
    $this->other = Tenant::factory()->onPlan($enterprise)->create(['slug' => 'beta']);
    $this->url = 'http://acme.somaplus.test';
    $this->owner = userWithRole($this->school, 'owner');

    $this->inSchool = fn (Closure $work, ?Tenant $school = null) => TenantContext::run($school ?? $this->school, $work);

    $this->makeRole = fn (string $name, array $permissions, ?Tenant $school = null): Role => ($this->inSchool)(fn () => app(SaveCustomRole::class)->handle(null, ['name' => $name, 'permissions' => $permissions]), $school);

    $this->personWith = function (Role $role, ?Tenant $school = null): User {
        $user = makeSchoolUser($school ?? $this->school, [], $role->name);

        return TenantContext::runAsSystem(fn () => User::findOrFail($user->id));
    };
});

it('lists the five default roles as locked and offers custom roles to an Enterprise owner', function () {
    $this->actingAs($this->owner)->get("{$this->url}/settings/roles")->assertOk()
        ->assertSee('Head teacher')->assertSee('Parent / student')->assertSee('Default roles cannot be changed or deleted.')
        ->assertDontSee('/settings/roles/1/edit', false);

    $this->actingAs($this->owner)->get($this->url)->assertSee('/settings/roles', false);
});

it('is a feature of the Enterprise plan: other plans get 403 and no menu entry', function () {
    $basic = SubscriptionPlan::factory()->withFeatures([])->create();
    $school = Tenant::factory()->onPlan($basic)->create(['slug' => 'basic']);
    $owner = userWithRole($school, 'owner');

    $this->actingAs($owner)->get('http://basic.somaplus.test/settings/roles')->assertForbidden();
    $this->actingAs($owner)->post('http://basic.somaplus.test/settings/roles', ['name' => 'Bursar', 'permissions' => ['fees.view']])->assertForbidden();
    $this->actingAs($owner)->get('http://basic.somaplus.test')->assertDontSee('/settings/roles', false);

    expect(TenantContext::run($school, fn () => Role::where('name', 'Bursar')->count()))->toBe(0);
});

it('makes a custom role with its permissions, adds the ones they need, and audits it', function () {
    $this->actingAs($this->owner)->post("{$this->url}/settings/roles", ['name' => 'Registrar', 'permissions' => ['students.manage', 'attendance.manage']])
        ->assertSessionHasNoErrors()->assertRedirect(route('settings.roles.index', [], false));

    ($this->inSchool)(function () {
        $role = Role::where('name', 'Registrar')->where('tenant_id', $this->school->id)->firstOrFail();

        expect($role->permissions->pluck('name')->sort()->values()->all())->toBe(['attendance.manage', 'students.manage', 'students.view'])
            ->and(AuditLog::where('action', 'role.created')->where('subject_id', $role->id)->where('user_id', $this->owner->id)->exists())->toBeTrue();
    });
});

it('never gives a custom role the permissions that stay with the owner', function () {
    $this->actingAs($this->owner)->post("{$this->url}/settings/roles", ['name' => 'Deputy', 'permissions' => ['students.view', 'settings.manage']])->assertSessionHasErrors('permissions.1');

    expect(($this->inSchool)(fn () => Role::where('name', 'Deputy')->count()))->toBe(0);
});

it('refuses bad role names and empty or unknown permissions', function (array $input, string $field) {
    $this->actingAs($this->owner)->post("{$this->url}/settings/roles", $input + ['name' => 'Valid Name', 'permissions' => ['students.view']])->assertSessionHasErrors($field);
})->with([
    'no name' => [['name' => ''], 'name'],
    'too long' => [['name' => str_repeat('a', 61)], 'name'],
    'symbols' => [['name' => '<script>'], 'name'],
    'a default role key' => [['name' => 'teacher'], 'name'],
    'a default role label' => [['name' => 'Head Teacher'], 'name'],
    'no permissions' => [['permissions' => []], 'permissions'],
    'unknown permission' => [['permissions' => ['everything.do']], 'permissions.0'],
]);

it('refuses the Swahili label of a default role too', function () {
    $this->actingAs($this->owner)->post("{$this->url}/settings/roles", ['name' => 'mwalimu', 'permissions' => ['students.view']])->assertSessionHasErrors('name');
});

it('keeps role names unique inside a school but lets another school use the same name', function () {
    ($this->makeRole)('Registrar', ['students.view']);

    $this->actingAs($this->owner)->post("{$this->url}/settings/roles", ['name' => 'registrar', 'permissions' => ['students.view']])->assertSessionHasErrors('name');

    $ownerB = userWithRole($this->other, 'owner');
    $this->actingAs($ownerB)->post('http://beta.somaplus.test/settings/roles', ['name' => 'Registrar', 'permissions' => ['students.view']])->assertSessionHasNoErrors();
    expect(Role::where('name', 'Registrar')->count())->toBe(2);
});

it('gives a person with a custom role exactly what the role allows', function () {
    $registrar = ($this->personWith)(($this->makeRole)('Registrar', ['students.manage']));

    $this->actingAs($registrar)->get("{$this->url}/students")->assertOk();
    $this->actingAs($registrar)->get("{$this->url}/students/create")->assertRedirect();
    $this->actingAs($registrar)->get("{$this->url}/fees/invoices")->assertForbidden();
    $this->actingAs($registrar)->get("{$this->url}/settings/roles")->assertForbidden();
    $this->actingAs($registrar)->get("{$this->url}/settings/users")->assertForbidden();
    $this->actingAs($registrar)->get("{$this->url}/settings/admission-numbers")->assertForbidden();

    $this->actingAs($registrar)->get($this->url)->assertOk()->assertSee('Registrar')->assertSee('/students', false)->assertDontSee('/fees/invoices', false)->assertDontSee('/settings/roles', false);
});

it('applies a change of permissions at once', function () {
    $role = ($this->makeRole)('Clerk', ['students.view', 'fees.view']);
    $clerk = ($this->personWith)($role);

    $this->actingAs($clerk)->get("{$this->url}/fees/invoices")->assertOk();

    $this->actingAs($this->owner)->put("{$this->url}/settings/roles/{$role->id}", ['name' => 'Front desk', 'permissions' => ['students.view']])->assertSessionHasNoErrors();

    $this->actingAs(TenantContext::runAsSystem(fn () => User::findOrFail($clerk->id)))->get("{$this->url}/fees/invoices")->assertForbidden();
    ($this->inSchool)(function () use ($role) {
        expect(Role::findOrFail($role->id)->name)->toBe('Front desk')
            ->and(AuditLog::where('action', 'role.updated')->where('subject_id', $role->id)->exists())->toBeTrue();
    });
});

it('opens the edit page with the permissions ticked', function () {
    $role = ($this->makeRole)('Clerk', ['fees.manage']);

    $this->actingAs($this->owner)->get("{$this->url}/settings/roles/{$role->id}/edit")->assertOk()->assertSee('Clerk')->assertSee('value="fees.manage"', false);
    $this->actingAs($this->owner)->get("{$this->url}/settings/roles")->assertOk()->assertSee('Start from the permissions of');
});

it('deletes an unused custom role and refuses one that people hold', function () {
    $unused = ($this->makeRole)('Unused', ['students.view']);
    $used = ($this->makeRole)('Used', ['students.view']);
    ($this->personWith)($used);

    $this->actingAs($this->owner)->delete("{$this->url}/settings/roles/{$used->id}")->assertSessionHasErrors('delete');
    $this->actingAs($this->owner)->delete("{$this->url}/settings/roles/{$unused->id}")->assertSessionHasNoErrors();

    ($this->inSchool)(fn () => expect(Role::whereKey($used->id)->exists())->toBeTrue()->and(Role::whereKey($unused->id)->exists())->toBeFalse()
        ->and(AuditLog::where('action', 'role.deleted')->where('subject_id', $unused->id)->exists())->toBeTrue());
});

it('never changes or deletes a default role', function () {
    $teacher = ($this->inSchool)(fn () => Role::where('name', 'teacher')->firstOrFail());
    $before = $teacher->permissions->pluck('name')->sort()->values()->all();

    $this->actingAs($this->owner)->get("{$this->url}/settings/roles/{$teacher->id}/edit")->assertNotFound();
    $this->actingAs($this->owner)->put("{$this->url}/settings/roles/{$teacher->id}", ['name' => 'Renamed', 'permissions' => ['students.view']])->assertNotFound();
    $this->actingAs($this->owner)->delete("{$this->url}/settings/roles/{$teacher->id}")->assertNotFound();

    ($this->inSchool)(fn () => expect(Role::findOrFail($teacher->id)->only('name'))->toBe(['name' => 'teacher'])->and(Role::findOrFail($teacher->id)->permissions->pluck('name')->sort()->values()->all())->toBe($before));
});

it('keeps schools apart: a role of another school is not found, not listed and not assignable', function () {
    $foreign = ($this->makeRole)('Foreign Role', ['students.view'], $this->other);

    $this->actingAs($this->owner)->get("{$this->url}/settings/roles")->assertOk()->assertDontSee('Foreign Role');
    $this->actingAs($this->owner)->get("{$this->url}/settings/roles/{$foreign->id}/edit")->assertNotFound();
    $this->actingAs($this->owner)->put("{$this->url}/settings/roles/{$foreign->id}", ['name' => 'Taken', 'permissions' => ['students.view']])->assertNotFound();
    $this->actingAs($this->owner)->delete("{$this->url}/settings/roles/{$foreign->id}")->assertNotFound();
    $this->actingAs($this->owner)->post("{$this->url}/settings/users", ['name' => 'X', 'email' => 'x@example.com', 'password' => 'password123', 'role' => 'Foreign Role'])->assertSessionHasErrors('role');

    expect(Role::findOrFail($foreign->id)->name)->toBe('Foreign Role');
});

it('lets only the owner see or change roles', function (string $role) {
    $user = userWithRole($this->school, $role);

    $this->actingAs($user)->get("{$this->url}/settings/roles")->assertForbidden();
    $this->actingAs($user)->post("{$this->url}/settings/roles", ['name' => 'Sneaky', 'permissions' => ['students.view']])->assertForbidden();

    expect(($this->inSchool)(fn () => Role::where('name', 'Sneaky')->count()))->toBe(0);
})->with(['head_teacher', 'teacher', 'accountant', 'parent_student']);

it('can be read but not changed in a read-only school', function () {
    $this->school->update(['subscription_status' => SubscriptionStatus::ReadOnly]);
    $owner = userWithRole($this->school, 'owner');

    $this->actingAs($owner)->get("{$this->url}/settings/roles")->assertOk();
    $this->actingAs($owner)->post("{$this->url}/settings/roles", ['name' => 'Late', 'permissions' => ['students.view']])->assertForbidden();
});

it('keeps people who hold a custom role working after the plan drops the feature', function () {
    $registrar = ($this->personWith)(($this->makeRole)('Registrar', ['students.view']));
    $this->school->forceFill(['subscription_plan_id' => SubscriptionPlan::factory()->withFeatures([])->create()->id])->save();

    $this->actingAs($registrar)->get("{$this->url}/students")->assertOk();
    $this->actingAs(userWithRole($this->school, 'owner'))->get("{$this->url}/settings/roles")->assertForbidden();
});

it('adds the feature to an Enterprise plan that is already in the table, once', function () {
    $plan = SubscriptionPlan::query()->updateOrCreate(['key' => 'enterprise'], ['name' => 'Enterprise', 'features' => ['x'], 'sort_order' => 3]);
    $migration = require database_path('migrations/2026_10_23_000001_add_custom_roles_to_enterprise_plan.php');

    $migration->up();
    $migration->up();

    expect($plan->fresh()?->features)->toBe(['x', 'custom_roles']);
});

describe('the catalog', function () {
    it('keeps only assignable permissions, adds what they need and keeps config order', function () {
        expect(RoleCatalog::withImplied(['payments.record', 'settings.manage', 'nonsense', 'students.manage']))
            ->toBe(['students.view', 'students.manage', 'fees.view', 'payments.record']);
    });

    it('labels every assignable permission and area in English and Swahili', function () {
        foreach (['en', 'sw'] as $locale) {
            foreach (RoleCatalog::groups() as $area => $permissions) {
                expect(Lang::has("custom_roles.area.{$area}", $locale))->toBeTrue("area {$area} in {$locale}");
                foreach ($permissions as $permission) {
                    expect(Lang::has('custom_roles.perm.'.$permission, $locale))->toBeTrue("{$permission} in {$locale}");
                }
            }
        }
    });

    it('only lets a permission imply permissions that exist', function () {
        $all = array_keys(config('roles.permissions'));

        foreach (config('roles.implies') as $permission => $needed) {
            expect($all)->toContain($permission)->and(array_diff($needed, $all))->toBe([]);
        }
    });

    it('shows a default role translated and a custom role as typed', function () {
        expect(RoleCatalog::label('head_teacher'))->toBe('Head teacher')->and(RoleCatalog::label('Bursar'))->toBe('Bursar');
    });
});
