<?php

use App\Models\Tenant;

beforeEach(function () {
    useTestHosts();
});

it('builds https asset links behind a proxy that ends TLS', function () {
    $this->get('http://somaplus.test/login', ['X-Forwarded-Proto' => 'https'])
        ->assertOk()
        ->assertSee('https://somaplus.test/assets/css/style.css', false)
        ->assertSee('https://somaplus.test/assets/css/somaplus.css', false)
        ->assertDontSee('http://somaplus.test/assets/', false);
});

it('keeps http links when no proxy says the request was https', function () {
    $this->get('http://somaplus.test/login')
        ->assertOk()
        ->assertSee('http://somaplus.test/assets/css/style.css', false);
});

it('does not let X-Forwarded-Host choose the school', function () {
    Tenant::factory()->create(['slug' => 'acme', 'name' => 'Acme Secondary']);

    $this->get('http://somaplus.test/login', [
        'X-Forwarded-Proto' => 'https',
        'X-Forwarded-Host' => 'acme.somaplus.test',
    ])
        ->assertOk()
        ->assertSee(__('auth.subtitle_platform'))
        ->assertDontSee('Acme Secondary');
});
