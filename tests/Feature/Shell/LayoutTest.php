<?php

use App\Models\Tenant;
use App\Models\User;
use App\Tenancy\TenantContext;

beforeEach(function () {
    useTestHosts();

    $this->school = Tenant::factory()->create(['slug' => 'acme', 'name' => 'Acme Secondary']);
    $this->teacher = makeSchoolUser($this->school, ['name' => 'Joseph Kileo'], 'teacher');
    $this->fresh = fn () => TenantContext::runAsSystem(fn () => User::findOrFail($this->teacher->id));
});

it('builds the page from the template assets', function () {
    $this->actingAs(($this->fresh)())
        ->get('http://acme.somaplus.test/')
        ->assertOk()
        ->assertSee('assets/css/style.css')
        ->assertSee('assets/css/somaplus.css')
        ->assertSee('assets/js/script.js')
        ->assertSee('id="sidebar-menu"', false);
});

it('switches language on the server, not with the template script', function () {
    $this->actingAs(($this->fresh)())
        ->get('http://acme.somaplus.test/')
        ->assertDontSee('somaplus-i18n.js')
        ->assertDontSee('somaplus-crud.js')
        ->assertSee('/locale"', false);
});

it('shows the signed-in person and a sign-out form instead of the template sample data', function () {
    $this->actingAs(($this->fresh)())
        ->get('http://acme.somaplus.test/')
        ->assertSee('Joseph Kileo')
        ->assertSee(__('roles.teacher'))
        ->assertSee('/logout"', false)
        ->assertDontSee('School Admin')
        ->assertDontSee('Amina Juma');
});

it('carries a csrf token on its forms', function () {
    $this->actingAs(($this->fresh)())
        ->get('http://acme.somaplus.test/')
        ->assertSee('name="_token"', false);
});

it('keeps the template stylesheet in public/assets', function () {
    expect(file_exists(public_path('assets/css/somaplus.css')))->toBeTrue();
});

it('shows the logo of the application, whose name always fits', function () {
    $this->actingAs(($this->fresh)())
        ->get('http://acme.somaplus.test/')
        ->assertOk()
        ->assertSee('img/somaplus-logo.svg', false)
        ->assertSee('img/somaplus-logo-white.svg', false)
        ->assertSee('css/somaplus-app.css', false)
        // The wordmark of the template's own logo is cut off when the browser picks a
        // wider font, so the application uses a logo of its own.
        ->assertDontSee('assets/img/logo.svg', false);
});
