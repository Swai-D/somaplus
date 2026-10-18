<?php

use App\Enums\SubscriptionStatus;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\PlanSeeder;

beforeEach(function () {
    useTestHosts();

    $this->school = Tenant::factory()->create(['slug' => 'acme']);
    $this->url = 'http://acme.somaplus.test';
});

it('lets every school role except parent / student look at the academic structure', function (string $role, bool $allowed) {
    $user = userWithRole($this->school, $role);

    foreach (['years', 'classes', 'subjects'] as $page) {
        $response = $this->actingAs($user)->get("{$this->url}/academics/{$page}");

        $allowed ? $response->assertOk() : $response->assertForbidden();
    }
})->with([
    'owner' => ['owner', true],
    'head teacher' => ['head_teacher', true],
    'teacher' => ['teacher', true],
    'accountant' => ['accountant', true],
    'parent / student' => ['parent_student', false],
]);

it('lets only the owner and the head teacher change it', function (string $role, bool $allowed) {
    $user = userWithRole($this->school, $role);

    $response = $this->actingAs($user)->post("{$this->url}/academics/subjects", ['name' => 'Mathematics']);

    $allowed ? $response->assertRedirect() : $response->assertForbidden();
})->with([
    'owner' => ['owner', true],
    'head teacher' => ['head_teacher', true],
    'teacher' => ['teacher', false],
    'accountant' => ['accountant', false],
    'parent / student' => ['parent_student', false],
]);

it('does not show forms or buttons to people who can only look', function () {
    $this->actingAs(userWithRole($this->school, 'teacher'))
        ->get("{$this->url}/academics/subjects")
        ->assertOk()
        ->assertDontSee(__('academics.subjects.add'));

    $this->actingAs(userWithRole($this->school, 'owner'))
        ->get("{$this->url}/academics/subjects")
        ->assertSee(__('academics.subjects.add'));
});

it('works on every plan', function (string $planKey) {
    $this->seed(PlanSeeder::class);
    $plan = SubscriptionPlan::where('key', $planKey)->firstOrFail();
    $school = Tenant::factory()->onPlan($plan)->create(['slug' => "on-{$planKey}"]);
    $owner = userWithRole($school, 'owner');

    $this->actingAs($owner)->get("http://on-{$planKey}.somaplus.test/academics/years")->assertOk();
    $this->actingAs($owner)
        ->post("http://on-{$planKey}.somaplus.test/academics/subjects", ['name' => 'Mathematics'])
        ->assertRedirect();
})->with(['basic', 'medium', 'enterprise']);

it('lets a read-only school look but not change', function () {
    $this->school->update(['subscription_status' => SubscriptionStatus::ReadOnly]);
    $owner = userWithRole($this->school, 'owner');

    $this->actingAs($owner)->get("{$this->url}/academics/years")->assertOk();
    $this->actingAs($owner)->post("{$this->url}/academics/subjects", ['name' => 'Mathematics'])->assertForbidden();
});

it('sends a guest to sign in', function () {
    $this->get("{$this->url}/academics/years")->assertRedirect("{$this->url}/login");
});

it('does not give the central address an academic structure', function () {
    $admin = User::factory()->superAdmin()->create();

    $this->actingAs($admin)->get('http://somaplus.test/academics/years')->assertForbidden();
});

it('shows the Academics entries in the sidebar to people who may look, and only to them', function () {
    $this->actingAs(userWithRole($this->school, 'teacher'))
        ->get($this->url)
        ->assertSee('/academics/years"', false)
        ->assertSee('/academics/classes"', false)
        ->assertSee('/academics/subjects"', false);

    $this->actingAs(userWithRole($this->school, 'parent_student'))
        ->get($this->url)
        ->assertDontSee('/academics/years"', false);
});

it('gives a school created later the permissions too', function () {
    $later = Tenant::factory()->create(['slug' => 'later']);

    $this->actingAs(userWithRole($later, 'head_teacher'))
        ->get('http://later.somaplus.test/academics/years')
        ->assertOk();
});

it('shows the pages in Swahili when that language is chosen', function () {
    $this->actingAs(userWithRole($this->school, 'owner'))
        ->withSession(['locale' => 'sw'])
        ->get("{$this->url}/academics/subjects")
        ->assertOk()
        ->assertSee(__('academics.subjects.add', [], 'sw'))
        ->assertDontSee(__('academics.subjects.add', [], 'en'));
});
