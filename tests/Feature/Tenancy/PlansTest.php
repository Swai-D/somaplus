<?php

use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Support\Plans;
use App\Tenancy\TenantContext;
use Database\Seeders\PlanSeeder;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    config()->set('tenancy.base_domain', 'somaplus.test');
    config()->set('tenancy.central_domains', ['somaplus.test']);

    Route::middleware(['web', 'plan:reports'])->get('/_reports', fn () => 'ok');
    Route::middleware(['web', 'plan:reports,exports'])->get('/_both', fn () => 'ok');
});

function schoolOnPlan(array $features, string $slug = 'acme'): Tenant
{
    $plan = SubscriptionPlan::factory()->withFeatures($features)->create();

    return Tenant::factory()->onPlan($plan)->create(['slug' => $slug]);
}

it('seeds the configured plans', function () {
    $this->seed(PlanSeeder::class);

    expect(SubscriptionPlan::orderBy('sort_order')->pluck('key')->all())
        ->toBe(['basic', 'medium', 'enterprise']);
});

it('can be seeded twice without duplicates', function () {
    $this->seed(PlanSeeder::class);
    $this->seed(PlanSeeder::class);

    expect(SubscriptionPlan::count())->toBe(3);
});

it('never overwrites a plan that already exists', function () {
    SubscriptionPlan::factory()->withFeatures(['reports'])->create(['key' => 'basic', 'name' => 'Custom']);

    $this->seed(PlanSeeder::class);

    $basic = SubscriptionPlan::where('key', 'basic')->firstOrFail();

    expect($basic->name)->toBe('Custom')
        ->and($basic->features)->toBe(['reports'])
        ->and(SubscriptionPlan::count())->toBe(3);
});

it('reports exactly the configured features on every seeded tier', function (string $key) {
    $this->seed(PlanSeeder::class);

    $plan = SubscriptionPlan::where('key', $key)->firstOrFail();
    $school = Tenant::factory()->onPlan($plan)->create();
    $configured = config("plans.plans.{$key}.features");

    TenantContext::run($school, function () use ($key, $configured) {
        expect(Plans::current()->key())->toBe($key);

        foreach ($configured as $feature) {
            expect(Plans::current()->allows($feature))->toBeTrue();
        }

        expect(Plans::current()->allows('feature-that-does-not-exist'))->toBeFalse();
    });
})->with(['basic', 'medium', 'enterprise']);

it('allows only the features of the current tenant\'s plan', function () {
    $school = schoolOnPlan(['reports']);

    TenantContext::run($school, function () {
        expect(Plans::current()->allows('reports'))->toBeTrue()
            ->and(Plans::current()->allows('exports'))->toBeFalse();
    });
});

it('allows nothing when there is no tenant', function () {
    expect(Plans::current()->allows('reports'))->toBeFalse()
        ->and(Plans::current()->key())->toBeNull();
});

it('lets a request through when the plan includes the feature', function () {
    schoolOnPlan(['reports']);

    $this->get('http://acme.somaplus.test/_reports')->assertOk();
});

it('answers 403 when the plan does not include the feature', function () {
    schoolOnPlan(['exports']);

    $this->get('http://acme.somaplus.test/_reports')->assertForbidden();
});

it('answers 403 on a central domain, where there is no plan', function () {
    $this->get('http://somaplus.test/_reports')->assertForbidden();
});

it('requires every listed feature', function () {
    schoolOnPlan(['reports'], 'only-reports');
    schoolOnPlan(['reports', 'exports'], 'both');

    $this->get('http://only-reports.somaplus.test/_both')->assertForbidden();
    $this->get('http://both.somaplus.test/_both')->assertOk();
});
