<?php

use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use App\Support\Menu;
use App\Tenancy\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Route;

/**
 * @param  array<int, array<string, mixed>>  $items
 * @return list<string>
 */
function menuLabels(array $items, ?User $user, string $uri = '/'): array
{
    $sections = (new Menu([['label' => 'menu.sections.main', 'items' => $items]]))->build($user, Request::create($uri));

    return collect($sections)->flatMap(fn ($section) => collect($section['items'])->pluck('label'))->all();
}

beforeEach(function () {
    useTestHosts();

    config()->set('roles.permissions', ['students.view' => ['teacher']]);
    $this->plan = SubscriptionPlan::factory()->withFeatures(['reports'])->create();
    $this->school = Tenant::factory()->onPlan($this->plan)->create(['slug' => 'acme']);
    $this->teacher = makeSchoolUser($this->school, [], 'teacher');
    $this->accountant = makeSchoolUser($this->school, [], 'accountant');
});

it('shows an entry whose route exists', function () {
    $labels = TenantContext::run($this->school, fn () => menuLabels(
        [['label' => 'menu.dashboard', 'route' => 'dashboard']],
        $this->teacher,
    ));

    expect($labels)->toBe([__('menu.dashboard')]);
});

it('hides an entry whose route does not exist', function () {
    $labels = TenantContext::run($this->school, fn () => menuLabels(
        [['label' => 'menu.dashboard', 'route' => 'no.such.route']],
        $this->teacher,
    ));

    expect($labels)->toBe([]);
});

it('shows an entry that needs a permission only to people who hold it', function () {
    $entry = [['label' => 'menu.dashboard', 'route' => 'dashboard', 'permission' => 'students.view']];

    $teacher = TenantContext::run($this->school, fn () => menuLabels($entry, $this->teacher));
    $accountant = TenantContext::run($this->school, fn () => menuLabels($entry, $this->accountant));

    expect($teacher)->toHaveCount(1)->and($accountant)->toBe([]);
});

it('shows an entry that needs a plan feature only when the plan has it', function () {
    $withFeature = [['label' => 'menu.dashboard', 'route' => 'dashboard', 'feature' => 'reports']];
    $withoutFeature = [['label' => 'menu.dashboard', 'route' => 'dashboard', 'feature' => 'exports']];

    $shown = TenantContext::run($this->school, fn () => menuLabels($withFeature, $this->teacher));
    $hidden = TenantContext::run($this->school, fn () => menuLabels($withoutFeature, $this->teacher));

    expect($shown)->toHaveCount(1)->and($hidden)->toBe([]);
});

it('shows a parent only when one of its children is shown', function () {
    $parent = [[
        'label' => 'menu.dashboard',
        'icon' => 'ti ti-school',
        'children' => [
            ['label' => 'menu.dashboard', 'route' => 'dashboard', 'permission' => 'students.view'],
        ],
    ]];

    $teacher = TenantContext::run($this->school, fn () => menuLabels($parent, $this->teacher));
    $accountant = TenantContext::run($this->school, fn () => menuLabels($parent, $this->accountant));

    expect($teacher)->toHaveCount(1)->and($accountant)->toBe([]);
});

it('keeps school entries off the central menu and platform entries off a school menu', function () {
    $entries = [
        ['label' => 'menu.dashboard', 'route' => 'dashboard'],
        ['label' => 'menu.sections.main', 'route' => 'dashboard', 'scope' => 'central'],
        ['label' => 'layout.logout', 'route' => 'dashboard', 'scope' => 'both'],
    ];

    $inSchool = TenantContext::run($this->school, fn () => menuLabels($entries, $this->teacher));
    $central = menuLabels($entries, User::factory()->superAdmin()->create());

    expect($inSchool)->toBe([__('menu.dashboard'), __('layout.logout')])
        ->and($central)->toBe([__('menu.sections.main'), __('layout.logout')]);
});

it('drops a section that has nothing to show', function () {
    $sections = (new Menu([['label' => 'menu.sections.main', 'items' => [['label' => 'menu.dashboard', 'route' => 'no.such.route']]]]))
        ->build($this->teacher, Request::create('/'));

    expect($sections)->toBe([]);
});

it('marks the entry of the current page as active', function () {
    $this->actingAs($this->teacher)
        ->get('http://acme.somaplus.test/')
        ->assertOk()
        ->assertSee('class="active"', false);
});

it('only lists entries whose routes exist', function () {
    $entries = collect(config('menu.sections'))->flatMap(fn ($section) => $section['items'])
        ->flatMap(fn ($item) => $item['children'] ?? [$item]);

    expect($entries->pluck('route')->filter()->reject(fn ($route) => Route::has($route))->all())->toBe([])
        ->and($entries->pluck('label')->reject(fn ($label) => Lang::has($label, 'en') && Lang::has($label, 'sw'))->all())->toBe([]);
});
