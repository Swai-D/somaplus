<?php

use App\Models\Tenant;
use App\Tenancy\RestoreTenantContext;
use App\Tenancy\TenancyViolation;
use App\Tenancy\TenantContext;
use Tests\Support\FakeTenantJob;
use Tests\Support\TenantNote;

it('runs a job inside the tenant it was dispatched for', function () {
    $school = Tenant::factory()->create();

    $job = TenantContext::run($school, fn () => (new FakeTenantJob)->forCurrentTenant());

    $seen = (new RestoreTenantContext)->handle($job, fn () => TenantContext::instance()->currentId());

    expect($seen)->toBe($school->id)
        ->and(TenantContext::instance()->current())->toBeNull();
});

it('scopes the work a job does to its tenant', function () {
    createTenantNotesTable();

    $school = Tenant::factory()->create();
    $job = TenantContext::run($school, fn () => (new FakeTenantJob)->forCurrentTenant());

    $note = (new RestoreTenantContext)->handle($job, fn () => TenantNote::create(['body' => 'from a job']));

    expect($note->tenant_id)->toBe($school->id);
});

it('carries only the tenant id in the serialized payload', function () {
    $school = Tenant::factory()->create();

    $job = TenantContext::run($school, fn () => (new FakeTenantJob)->forCurrentTenant());
    $payload = serialize($job);

    expect(unserialize($payload)->tenantId)->toBe($school->id)
        ->and($payload)->not->toContain($school->name)
        ->and($payload)->not->toContain($school->slug);
});

it('attaches the restore middleware to the job', function () {
    expect((new FakeTenantJob)->middleware())
        ->toHaveCount(1)
        ->and((new FakeTenantJob)->middleware()[0])->toBeInstanceOf(RestoreTenantContext::class);
});

it('refuses to run a job that has no tenant id', function () {
    (new RestoreTenantContext)->handle(new FakeTenantJob, fn () => null);
})->throws(TenancyViolation::class, 'carries no tenant id');

it('refuses to run a job for a tenant that no longer exists', function () {
    $job = new FakeTenantJob;
    $job->tenantId = '01ARZ3NDEKTSV4RRFFQ69G5FAV';

    (new RestoreTenantContext)->handle($job, fn () => null);
})->throws(TenancyViolation::class, 'does not exist');

it('refuses to prepare a job when there is no current tenant', function () {
    (new FakeTenantJob)->forCurrentTenant();
})->throws(TenancyViolation::class, 'requires a current tenant');
