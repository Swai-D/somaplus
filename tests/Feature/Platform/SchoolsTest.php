<?php

use App\Enums\SubscriptionStatus;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use App\Tenancy\TenantContext;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    useTestHosts();

    $this->central = 'http://somaplus.test';
    $this->admin = User::factory()->superAdmin()->create();
    $this->plan = SubscriptionPlan::factory()->create(['key' => 'medium', 'name' => 'Zxqmedium']);
    $this->acme = Tenant::factory()->create(['slug' => 'acme', 'name' => 'Zxqacme School']);

    $this->data = fn (array $over = []) => $over + [
        'name' => 'Zxqnew School', 'slug' => 'zxqnew', 'domain' => '', 'subscription_plan_id' => $this->plan->id, 'currency' => 'KES',
        'owner_name' => 'Zxqowner', 'owner_email' => 'owner@zxqnew.test',
    ];
    $this->create = fn (array $over = []) => $this->actingAs($this->admin)->post($this->central.'/platform/schools', ($this->data)($over));
    $this->school = fn (string $slug = 'zxqnew') => Tenant::query()->where('slug', $slug)->first();
});

it('lists the schools for a platform administrator', function () {
    $this->actingAs($this->admin)->get($this->central.'/platform/schools')->assertOk()->assertSee('Zxqacme School')->assertSee('acme.somaplus.test');
});

it('is hidden from everyone who is not a platform administrator', function () {
    $owner = userWithRole($this->acme, 'owner');

    $this->actingAs($owner)->get('http://acme.somaplus.test/platform/schools')->assertNotFound();
    $this->app['auth']->forgetGuards();
    $this->actingAs($owner)->post('http://acme.somaplus.test/platform/schools', ($this->data)())->assertNotFound();
    $this->app['auth']->forgetGuards();
    $this->actingAs($this->admin)->get('http://acme.somaplus.test/platform/schools')->assertNotFound();
    $this->app['auth']->forgetGuards();
    $this->get($this->central.'/platform/schools')->assertRedirect();

    expect(Tenant::query()->count())->toBe(1);
});

it('opens a school with its roles, plan, currency and first owner, and sends the owner a link on the school address', function () {
    Notification::fake();

    ($this->create)()->assertSessionHasNoErrors()->assertRedirect();

    $school = ($this->school)();
    expect($school->subscription_plan_id)->toBe($this->plan->id)->and($school->currency)->toBe('KES')
        ->and($school->subscription_status)->toBe(SubscriptionStatus::Active)->and($school->domain)->toBeNull();

    TenantContext::run($school, function () use ($school) {
        $owner = User::query()->where('email', 'owner@zxqnew.test')->sole();
        expect($owner->hasRole('owner'))->toBeTrue()->and(Role::where('tenant_id', $school->id)->count())->toBe(count(config('roles.roles')));
        Notification::assertSentTo($owner, ResetPasswordNotification::class, function ($notification) use ($owner) {
            $url = (string) collect($notification->toMail($owner)->introLines)->first(fn ($line) => str_starts_with($line, 'http'));

            return str_starts_with($url, 'http://zxqnew.somaplus.test/reset-password/');
        });
    });
});

it('uses the school own domain for the link when it has one', function () {
    Notification::fake();

    ($this->create)(['domain' => 'Portal.Zxqnew.ac.tz'])->assertSessionHasNoErrors();

    expect(($this->school)()->domain)->toBe('portal.zxqnew.ac.tz');
});

it('sends no link when schools are reached through path entry without their own domain', function () {
    config()->set('tenancy.allow_path_entry', true);
    Notification::fake();

    ($this->create)()->assertSessionHasNoErrors()->assertSessionHas('status', __('platform.created_not_sent'));

    Notification::assertNothingSent();
    expect(($this->school)())->not->toBeNull();
});

it('refuses a bad, reserved or taken short name, a bad currency and the central domain', function (array $over, string $field) {
    ($this->create)($over)->assertSessionHasErrors($field);

    expect(Tenant::query()->count())->toBe(1);
})->with([
    'taken' => [['slug' => 'acme'], 'slug'],
    'reserved' => [['slug' => 'admin'], 'slug'],
    'spaces are not allowed' => [['slug' => 'my school'], 'slug'],
    'too short' => [['slug' => 'ab'], 'slug'],
    'bad currency' => [['currency' => 'XYZ'], 'currency'],
    'central domain' => [['domain' => 'somaplus.test'], 'domain'],
    'not a domain' => [['domain' => 'not a domain'], 'domain'],
    'unknown plan' => [['subscription_plan_id' => 9999], 'subscription_plan_id'],
    'bad owner email' => [['owner_email' => 'nope'], 'owner_email'],
]);

it('changes the plan and the subscription state but never the short name or the currency', function () {
    ($this->create)();
    $school = ($this->school)();

    $this->actingAs($this->admin)->put($this->central.'/platform/schools/'.$school->id, [
        'name' => 'Zxqrenamed', 'domain' => '', 'subscription_plan_id' => $this->plan->id, 'subscription_status' => 'read_only',
        'slug' => 'hacked', 'currency' => 'USD',
    ])->assertSessionHasNoErrors()->assertRedirect();

    $school = $school->fresh();
    expect($school->name)->toBe('Zxqrenamed')->and($school->isReadOnly())->toBeTrue()
        ->and($school->slug)->toBe('zxqnew')->and($school->currency)->toBe('KES');
});

it('needs a date for the grace period and clears it on another state', function () {
    ($this->create)();
    $school = ($this->school)();
    $put = fn (array $over) => $this->actingAs($this->admin)->put($this->central.'/platform/schools/'.$school->id, $over + [
        'name' => 'Zxqnew School', 'domain' => '', 'subscription_plan_id' => $this->plan->id,
    ]);

    $put(['subscription_status' => 'grace'])->assertSessionHasErrors('grace_ends_at');
    $put(['subscription_status' => 'grace', 'grace_ends_at' => '2030-01-31'])->assertSessionHasNoErrors();
    expect($school->fresh()->subscription_status)->toBe(SubscriptionStatus::Grace)->and($school->fresh()->grace_ends_at->toDateString())->toBe('2030-01-31');

    $put(['subscription_status' => 'active'])->assertSessionHasNoErrors();
    expect($school->fresh()->grace_ends_at)->toBeNull();
});

it('keeps the school data when it goes read-only', function () {
    $this->actingAs($this->admin)->put($this->central.'/platform/schools/'.$this->acme->id, [
        'name' => 'Zxqacme School', 'domain' => '', 'subscription_plan_id' => $this->plan->id, 'subscription_status' => 'read_only',
    ])->assertSessionHasNoErrors();

    expect(Tenant::query()->count())->toBe(1)->and($this->acme->fresh()->isReadOnly())->toBeTrue();
});

it('shows the Schools link only to a platform administrator on the central address', function () {
    $this->actingAs($this->admin)->get($this->central.'/')->assertOk()->assertSee('/platform/schools');

    $this->app['auth']->forgetGuards();
    $this->actingAs(userWithRole($this->acme, 'owner'))->get('http://acme.somaplus.test/')->assertOk()->assertDontSee('/platform/schools');
});

it('speaks Swahili', function () {
    $this->actingAs($this->admin)->withSession(['locale' => 'sw'])->get($this->central.'/platform/schools')->assertOk()->assertSee(__('platform.intro', [], 'sw'));
});
