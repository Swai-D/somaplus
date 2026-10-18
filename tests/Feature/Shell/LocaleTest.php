<?php

use App\Models\Tenant;

beforeEach(function () {
    useTestHosts();
});

it('uses English by default', function () {
    $this->get('http://somaplus.test/login')->assertOk()->assertSee(__('auth.title', [], 'en'));
});

it('remembers the language a person chooses', function () {
    $this->from('http://somaplus.test/login')
        ->post('http://somaplus.test/locale', ['locale' => 'sw'])
        ->assertRedirect('http://somaplus.test/login')
        ->assertSessionHas('locale', 'sw');

    $this->get('http://somaplus.test/login')
        ->assertOk()
        ->assertSee(__('auth.subtitle_platform', [], 'sw'))
        ->assertDontSee(__('auth.subtitle_platform', [], 'en'));
});

it('translates validation messages into the chosen language', function () {
    $this->withSession(['locale' => 'sw'])
        ->post('http://somaplus.test/login', [])
        ->assertSessionHasErrors(['email' => __('validation.required', ['attribute' => __('validation.attributes.email', [], 'sw')], 'sw')]);
});

it('refuses a language the application does not have', function () {
    $this->post('http://somaplus.test/locale', ['locale' => 'fr'])
        ->assertSessionHasErrors('locale')
        ->assertSessionMissing('locale');
});

it('switches language on a school address too', function () {
    Tenant::factory()->create(['slug' => 'acme']);

    $this->post('http://acme.somaplus.test/locale', ['locale' => 'sw'])->assertSessionHas('locale', 'sw');
});

it('ignores a language stored in the session that is no longer offered', function () {
    $this->withSession(['locale' => 'fr'])
        ->get('http://somaplus.test/login')
        ->assertOk()
        ->assertSee(__('auth.subtitle_platform', [], 'en'));
});
