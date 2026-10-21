<?php

use App\Domain\Integrations\Services\IntegrationManager;
use App\Enums\SubscriptionStatus;
use App\Models\AuditLog;
use App\Models\Tenant;
use App\Models\TenantIntegration;
use App\Tenancy\TenantContext;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    useTestHosts();

    $this->school = Tenant::factory()->create(['slug' => 'acme']);
    $this->other = Tenant::factory()->create(['slug' => 'beta']);
    $this->url = 'http://acme.somaplus.test';
    $this->owner = userWithRole($this->school, 'owner');
    $this->inSchool = fn (Closure $work, ?Tenant $school = null) => TenantContext::run($school ?? $this->school, $work);
    $this->save = fn (array $data, ?object $user = null) => $this->actingAs($user ?? $this->owner)
        ->put("{$this->url}/settings/integrations/payments", $data);
    $this->row = fn (?Tenant $school = null) => ($this->inSchool)(fn () => TenantIntegration::query()->first(), $school);
});

it('only lets the owner see and change integrations', function (string $role, bool $allowed) {
    $user = userWithRole($this->school, $role);

    $get = $this->actingAs($user)->get("{$this->url}/settings/integrations");
    $allowed ? $get->assertOk() : $get->assertForbidden();

    $put = ($this->save)(['enabled' => '0', 'driver' => ''], $user);
    $allowed ? $put->assertRedirect() : $put->assertForbidden();
})->with([
    'owner' => ['owner', true],
    'head teacher' => ['head_teacher', false],
    'accountant' => ['accountant', false],
    'teacher' => ['teacher', false],
]);

it('shows the settings entry in the menu to the owner only', function () {
    $this->actingAs($this->owner)->get("{$this->url}/settings/integrations")->assertSee(__('menu.integrations'));
    $this->actingAs(userWithRole($this->school, 'accountant'))->get("{$this->url}/fees/invoices")->assertDontSee(route('settings.integrations.index', [], false));
});

it('is off by default and has no saved row', function () {
    expect(($this->row)())->toBeNull()
        ->and(($this->inSchool)(fn () => app(IntegrationManager::class)->active('payments')))->toBeNull();

    $this->actingAs($this->owner)->get("{$this->url}/settings/integrations")->assertOk()->assertSee(__('integrations.channels.payments.title'));
});

it('saves a typed secret encrypted and never shows it again', function () {
    ($this->save)(['enabled' => '1', 'driver' => 'signed_webhook', 'config' => ['webhook_secret' => 'my-very-long-secret-1']])
        ->assertSessionHasNoErrors()->assertRedirect(route('settings.integrations.index', [], false));

    $row = ($this->row)();
    expect($row->enabled)->toBeTrue()->and($row->driver)->toBe('signed_webhook')->and($row->secrets['webhook_secret'])->toBe('my-very-long-secret-1')
        ->and(DB::table('tenant_integrations')->value('secrets'))->not->toContain('my-very-long-secret-1')
        ->and(($this->inSchool)(fn () => app(IntegrationManager::class)->active('payments')))->not->toBeNull();

    $page = $this->actingAs($this->owner)->get("{$this->url}/settings/integrations")->assertOk()->assertSee(__('integrations.secret_saved'));
    expect($page->getContent())->not->toContain('my-very-long-secret-1');
});

it('keeps the saved secret when the field is left blank', function () {
    ($this->save)(['enabled' => '1', 'driver' => 'signed_webhook', 'config' => ['webhook_secret' => 'my-very-long-secret-1']]);
    ($this->save)(['enabled' => '1', 'driver' => 'signed_webhook', 'config' => ['webhook_secret' => '']])->assertSessionHasNoErrors();

    expect(($this->row)()->secrets['webhook_secret'])->toBe('my-very-long-secret-1');
});

it('makes a secret when left blank, shows it once and stores it encrypted', function () {
    $response = ($this->save)(['enabled' => '1', 'driver' => 'signed_webhook', 'config' => ['webhook_secret' => '']])->assertOk();

    $secret = ($this->row)()->secrets['webhook_secret'];
    expect($secret)->toHaveLength(48)->and($response->getContent())->toContain($secret)
        ->and(DB::table('tenant_integrations')->value('secrets'))->not->toContain($secret);

    $this->actingAs($this->owner)->get("{$this->url}/settings/integrations")->assertDontSee($secret);
});

it('refuses a short secret, an unknown provider and switching on without a provider', function () {
    ($this->save)(['enabled' => '1', 'driver' => 'signed_webhook', 'config' => ['webhook_secret' => 'short']])->assertSessionHasErrors('config.webhook_secret');
    ($this->save)(['enabled' => '1', 'driver' => 'nope'])->assertSessionHasErrors('driver');
    ($this->save)(['enabled' => '1', 'driver' => ''])->assertSessionHasErrors('driver');

    expect(($this->row)())->toBeNull();
});

it('switches off without a provider and keeps the saved configuration', function () {
    ($this->save)(['enabled' => '1', 'driver' => 'signed_webhook', 'config' => ['webhook_secret' => 'my-very-long-secret-1']]);
    ($this->save)(['enabled' => '0', 'driver' => ''])->assertSessionHasNoErrors();

    $row = ($this->row)();
    expect($row->enabled)->toBeFalse()->and($row->secrets['webhook_secret'])->toBe('my-very-long-secret-1')
        ->and(($this->inSchool)(fn () => app(IntegrationManager::class)->active('payments')))->toBeNull();
});

it('keeps each school its own configuration', function () {
    ($this->save)(['enabled' => '1', 'driver' => 'signed_webhook', 'config' => ['webhook_secret' => 'my-very-long-secret-1']]);

    expect(($this->row)($this->other))->toBeNull()
        ->and(($this->inSchool)(fn () => app(IntegrationManager::class)->active('payments'), $this->other))->toBeNull();
});

it('answers 404 for an unknown channel', function () {
    $this->actingAs($this->owner)->put("{$this->url}/settings/integrations/nope", ['enabled' => '0'])->assertNotFound();
});

it('writes the save to the audit log with ids only', function () {
    ($this->save)(['enabled' => '1', 'driver' => 'signed_webhook', 'config' => ['webhook_secret' => 'my-very-long-secret-1']]);

    $logs = ($this->inSchool)(fn () => AuditLog::query()->where('action', 'integration.saved')->get());
    expect($logs)->toHaveCount(1)->and($logs->first()->subject_id)->toBe(($this->row)()->id)
        ->and(json_encode($logs->toArray()))->not->toContain('my-very-long-secret-1');
});

it('does not let a read-only school change integrations but lets it look', function () {
    $this->school->update(['subscription_status' => SubscriptionStatus::ReadOnly]);
    $owner = userWithRole($this->school, 'owner');

    $this->actingAs($owner)->get("{$this->url}/settings/integrations")->assertOk();
    ($this->save)(['enabled' => '0', 'driver' => ''], $owner)->assertForbidden();
});
