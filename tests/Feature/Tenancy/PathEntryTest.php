<?php

use App\Models\Student;
use App\Models\Tenant;
use App\Tenancy\TenantContext;

beforeEach(function () {
    useTestHosts();

    $this->school = Tenant::factory()->create(['slug' => 'acme', 'name' => 'Acme Secondary']);
    $this->other = Tenant::factory()->create(['slug' => 'beta', 'name' => 'Beta Academy']);
    $this->owner = makeSchoolUser($this->school, ['email' => 'owner@acme.test'], 'owner');
    $this->central = 'http://somaplus.test';
});

it('ignores a remembered school when the path entry is off', function () {
    config()->set('tenancy.allow_path_entry', false);

    $this->withSession(['tenant_slug' => 'acme'])->get("{$this->central}/login")->assertOk()->assertDontSee('Acme Secondary');
    $this->withSession(['tenant_slug' => 'acme'])->post("{$this->central}/login", ['email' => 'owner@acme.test', 'password' => 'password'])->assertSessionHasErrors();

    $this->assertGuest();
});

it('shows the remembered school on the central sign-in page when the path entry is on', function () {
    config()->set('tenancy.allow_path_entry', true);

    $this->withSession(['tenant_slug' => 'acme'])->get("{$this->central}/login")->assertOk()->assertSee('Acme Secondary');
});

it('lets that school sign in on the central address and then use pages that load tenant data', function () {
    config()->set('tenancy.allow_path_entry', true);
    $student = TenantContext::run($this->school, fn () => Student::factory()->create(['first_name' => 'Pathkid']));

    $this->withSession(['tenant_slug' => 'acme'])
        ->post("{$this->central}/login", ['email' => 'owner@acme.test', 'password' => 'password'])
        ->assertSessionHasNoErrors();
    $this->assertAuthenticatedAs($this->owner);

    // The signed-in page needs the school before authentication and route model binding.
    $this->withSession(['tenant_slug' => 'acme'])->actingAs($this->owner)->get("{$this->central}/")->assertOk();
    $this->withSession(['tenant_slug' => 'acme'])->actingAs($this->owner)->get("{$this->central}/students/{$student->id}")->assertOk()->assertSee('Pathkid');
});

it('does not let another school user sign in through a remembered school', function () {
    config()->set('tenancy.allow_path_entry', true);
    makeSchoolUser($this->other, ['email' => 'owner@beta.test'], 'owner');

    $this->withSession(['tenant_slug' => 'acme'])
        ->post("{$this->central}/login", ['email' => 'owner@beta.test', 'password' => 'password'])
        ->assertSessionHasErrors();

    $this->assertGuest();
});

it('never overrides a school own address', function () {
    config()->set('tenancy.allow_path_entry', true);

    $this->withSession(['tenant_slug' => 'beta'])->get('http://acme.somaplus.test/login')->assertOk()->assertSee('Acme Secondary')->assertDontSee('Beta Academy');
});

it('ignores an unknown remembered school', function () {
    config()->set('tenancy.allow_path_entry', true);

    $this->withSession(['tenant_slug' => 'nope'])->get("{$this->central}/login")->assertOk()->assertSee(__('auth.subtitle_platform'));
});

it('keeps the chosen school after signing out', function () {
    config()->set('tenancy.allow_path_entry', true);

    $this->withSession(['tenant_slug' => 'acme'])->actingAs($this->owner)->post("{$this->central}/logout")->assertRedirect();

    expect(session('tenant_slug'))->toBe('acme');
});
