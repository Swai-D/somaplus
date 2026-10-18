<?php

use App\Enums\SubscriptionStatus;
use App\Models\Tenant;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    config()->set('tenancy.base_domain', 'somaplus.test');
    config()->set('tenancy.central_domains', ['somaplus.test']);

    Route::middleware(['web', 'tenant.writable'])
        ->match(['get', 'post', 'put', 'delete'], '/_write', fn () => 'ok');
});

it('lets an active school read and write', function () {
    Tenant::factory()->create(['slug' => 'acme']);

    $this->get('http://acme.somaplus.test/_write')->assertOk();
    $this->post('http://acme.somaplus.test/_write')->assertOk();
});

it('still lets a school in its grace period write', function () {
    Tenant::factory()->status(SubscriptionStatus::Grace)->create(['slug' => 'acme']);

    $this->post('http://acme.somaplus.test/_write')->assertOk();
});

it('lets a read-only school read but not change anything', function () {
    Tenant::factory()->status(SubscriptionStatus::ReadOnly)->create(['slug' => 'acme']);

    $this->get('http://acme.somaplus.test/_write')->assertOk();
    $this->post('http://acme.somaplus.test/_write')->assertForbidden();
    $this->put('http://acme.somaplus.test/_write')->assertForbidden();
    $this->delete('http://acme.somaplus.test/_write')->assertForbidden();
});

it('does not block the central domain', function () {
    $this->post('http://somaplus.test/_write')->assertOk();
});

it('keeps a lapsed school\'s record: read-only is a status, not a deletion', function () {
    $school = Tenant::factory()->status(SubscriptionStatus::ReadOnly)->create();

    expect($school->isReadOnly())->toBeTrue()
        ->and(Tenant::find($school->id))->not->toBeNull();
});
