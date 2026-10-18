<?php

use App\Models\Tenant;
use App\Models\User;
use App\Tenancy\TenantContext;
use Illuminate\Support\Facades\Lang;

beforeEach(function () {
    useTestHosts();

    $this->school = Tenant::factory()->create(['slug' => 'acme']);
    $teacher = makeSchoolUser($this->school, [], 'teacher');
    $this->teacher = TenantContext::runAsSystem(fn () => User::findOrFail($teacher->id));
});

it('has every configured article in English and Swahili', function (string $locale) {
    foreach (config('help.articles') as $slug) {
        expect(Lang::hasForLocale("help.articles.{$slug}.title", $locale))->toBeTrue("{$slug} has no {$locale} title")
            ->and(Lang::hasForLocale("help.articles.{$slug}.summary", $locale))->toBeTrue("{$slug} has no {$locale} summary")
            ->and(trans("help.articles.{$slug}.body", [], $locale))->toBeArray()->not->toBeEmpty();
    }
})->with(['en', 'sw']);

it('lists the articles for a signed-in person', function () {
    $response = $this->actingAs($this->teacher)->get('http://acme.somaplus.test/help')->assertOk();

    foreach (config('help.articles') as $slug) {
        $response->assertSee(__("help.articles.{$slug}.title"));
    }
});

it('shows an article', function () {
    $this->actingAs($this->teacher)
        ->get('http://acme.somaplus.test/help/roles')
        ->assertOk()
        ->assertSee(__('help.articles.roles.title'))
        ->assertSee(__('roles.owner'));
});

it('shows an article in Swahili when that language is chosen', function () {
    $this->actingAs($this->teacher)
        ->withSession(['locale' => 'sw'])
        ->get('http://acme.somaplus.test/help/subscription')
        ->assertOk()
        ->assertSee(__('help.articles.subscription.title', [], 'sw'))
        ->assertDontSee(__('help.articles.subscription.title', [], 'en'));
});

it('answers 404 for an article that does not exist', function () {
    $this->actingAs($this->teacher)->get('http://acme.somaplus.test/help/nothing-here')->assertNotFound();
});

it('does not read other languages files through the article name', function () {
    $this->actingAs($this->teacher)->get('http://acme.somaplus.test/help/..%2Froles')->assertNotFound();
});

it('sends a guest to sign in', function () {
    $this->get('http://acme.somaplus.test/help')->assertRedirect('http://acme.somaplus.test/login');
});

it('is also available on the central address to a platform administrator', function () {
    $this->actingAs(User::factory()->superAdmin()->create())
        ->get('http://somaplus.test/help')
        ->assertOk();
});

it('shows Help in the sidebar', function () {
    $this->actingAs($this->teacher)
        ->get('http://acme.somaplus.test/')
        ->assertSee('/help"', false);
});
