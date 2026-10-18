<?php

use App\Models\Tenant;
use App\Tenancy\TenantContext;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    config()->set('tenancy.base_domain', 'somaplus.test');
    config()->set('tenancy.central_domains', ['somaplus.test', 'localhost']);

    Route::middleware('web')->get('/_whoami', fn () => response()->json([
        'tenant' => app(TenantContext::class)->currentId(),
    ]));
});

it('resolves the tenant from its subdomain', function () {
    $school = Tenant::factory()->create(['slug' => 'acme']);

    $this->getJson('http://acme.somaplus.test/_whoami')
        ->assertOk()
        ->assertExactJson(['tenant' => $school->id]);
});

it('resolves the tenant from its own domain', function () {
    $school = Tenant::factory()->create(['domain' => 'school.example.org']);

    $this->getJson('http://school.example.org/_whoami')
        ->assertOk()
        ->assertExactJson(['tenant' => $school->id]);
});

it('has no tenant on a central domain', function () {
    $this->getJson('http://somaplus.test/_whoami')
        ->assertOk()
        ->assertExactJson(['tenant' => null]);
});

it('returns 404 for an unknown school', function (string $url) {
    Tenant::factory()->create(['slug' => 'acme']);

    $this->getJson($url)->assertNotFound();
})->with([
    'unknown subdomain' => 'http://nobody.somaplus.test/_whoami',
    'nested subdomain' => 'http://x.acme.somaplus.test/_whoami',
    'unrelated host' => 'http://evil.example.com/_whoami',
]);

it('ignores any attempt to choose the tenant with a header', function () {
    $school = Tenant::factory()->create(['slug' => 'acme']);

    $this->withHeaders(['X-Tenant' => $school->slug, 'X-Tenant-Id' => $school->id])
        ->getJson('http://somaplus.test/_whoami')
        ->assertExactJson(['tenant' => null]);
});

it('does not carry a tenant over to the next request', function () {
    $school = Tenant::factory()->create(['slug' => 'acme']);

    $this->getJson('http://acme.somaplus.test/_whoami')->assertExactJson(['tenant' => $school->id]);
    $this->getJson('http://somaplus.test/_whoami')->assertExactJson(['tenant' => null]);
});

it('does not match a host that only resembles a tenant domain', function () {
    Tenant::factory()->create(['domain' => 'school.example.org']);

    $this->getJson('http://notschool.example.org/_whoami')->assertNotFound();
});
