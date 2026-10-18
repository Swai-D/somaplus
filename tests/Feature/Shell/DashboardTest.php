<?php

use App\Enums\SubscriptionStatus;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use App\Tenancy\TenantContext;

beforeEach(function () {
    useTestHosts();

    $plan = SubscriptionPlan::factory()->create(['key' => 'medium', 'name' => 'Medium Plan']);
    $this->school = Tenant::factory()->onPlan($plan)->create(['slug' => 'acme', 'name' => 'Acme Secondary']);
    $this->head = makeSchoolUser($this->school, ['name' => 'Asha Mwinyi'], 'head_teacher');
    $this->freshHead = fn () => TenantContext::runAsSystem(fn () => User::findOrFail($this->head->id));
});

it('shows a school its own name, plan, subscription and role', function () {
    $this->actingAs(($this->freshHead)())
        ->get('http://acme.somaplus.test/')
        ->assertOk()
        ->assertSee('Acme Secondary')
        ->assertSee('Medium Plan')
        ->assertSee(__('dashboard.status.active'))
        ->assertSee(__('roles.head_teacher'))
        ->assertSee(__('dashboard.welcome', ['name' => 'Asha Mwinyi']));
});

it('shows the end of the grace period', function () {
    $this->school->update([
        'subscription_status' => SubscriptionStatus::Grace,
        'grace_ends_at' => now()->addDays(10),
    ]);

    $this->actingAs(($this->freshHead)())
        ->get('http://acme.somaplus.test/')
        ->assertOk()
        ->assertSee(__('dashboard.status.grace'))
        ->assertSee(__('dashboard.grace_ends', ['date' => now()->addDays(10)->toFormattedDateString()]));
});

it('warns a read-only school and still lets it read', function () {
    $this->school->update(['subscription_status' => SubscriptionStatus::ReadOnly]);

    $this->actingAs(($this->freshHead)())
        ->get('http://acme.somaplus.test/')
        ->assertOk()
        ->assertSee(__('tenancy.read_only'))
        ->assertSee(__('dashboard.status.read_only'));
});

it('does not warn a school that is active', function () {
    $this->actingAs(($this->freshHead)())
        ->get('http://acme.somaplus.test/')
        ->assertDontSee(__('tenancy.read_only'));
});

it('shows the dashboard in Swahili when that language is chosen', function () {
    $this->actingAs(($this->freshHead)())
        ->withSession(['locale' => 'sw'])
        ->get('http://acme.somaplus.test/')
        ->assertOk()
        ->assertSee(__('dashboard.status.active', [], 'sw'))
        ->assertSee(__('roles.head_teacher', [], 'sw'))
        ->assertSee(__('menu.dashboard', [], 'sw'));
});

it('does not show one school anything of another school', function () {
    Tenant::factory()->create(['slug' => 'beta', 'name' => 'Beta Academy']);

    $this->actingAs(($this->freshHead)())
        ->get('http://acme.somaplus.test/')
        ->assertDontSee('Beta Academy');
});

it('shows the platform administrator the number of schools', function () {
    Tenant::factory()->count(2)->create();
    $admin = User::factory()->superAdmin()->create(['name' => 'Platform Root']);

    $this->actingAs($admin)
        ->get('http://somaplus.test/')
        ->assertOk()
        ->assertSee(__('dashboard.platform'))
        ->assertSee(__('dashboard.schools'))
        ->assertSee(__('layout.platform_admin'))
        ->assertSeeInOrder(['<h3 class="mb-0">', '3', '</h3>'], false);
});
