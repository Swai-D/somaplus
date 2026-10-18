<?php

use App\Models\AcademicYear;
use App\Models\Tenant;
use App\Models\Term;
use App\Tenancy\TenantContext;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;

beforeEach(function () {
    useTestHosts();

    $this->school = Tenant::factory()->create(['slug' => 'acme']);
    $this->other = Tenant::factory()->create(['slug' => 'beta']);
    $this->url = 'http://acme.somaplus.test';
    $this->owner = userWithRole($this->school, 'owner');

    $this->inSchool = fn (Closure $work, ?Tenant $school = null) => TenantContext::run($school ?? $this->school, $work);
    $this->year = fn (array $attributes = [], ?Tenant $school = null) => ($this->inSchool)(fn () => AcademicYear::factory()->create($attributes), $school);
});

it('adds an academic year', function () {
    $this->actingAs($this->owner)
        ->post("{$this->url}/academics/years", ['name' => '2026', 'starts_on' => '2026-01-01', 'ends_on' => '2026-12-31'])
        ->assertRedirect()
        ->assertSessionHas('status', __('academics.saved'));

    $year = ($this->inSchool)(fn () => AcademicYear::firstOrFail());

    expect($year->name)->toBe('2026')
        ->and($year->tenant_id)->toBe($this->school->id)
        ->and($year->is_current)->toBeFalse();
});

it('lists the years of the school, newest first', function () {
    ($this->year)(['name' => '2025', 'starts_on' => '2025-01-01', 'ends_on' => '2025-12-31']);
    ($this->year)(['name' => '2026', 'starts_on' => '2026-01-01', 'ends_on' => '2026-12-31']);

    $this->actingAs($this->owner)
        ->get("{$this->url}/academics/years")
        ->assertOk()
        ->assertSeeInOrder(['2026', '2025']);
});

it('requires a name and valid dates', function () {
    $this->actingAs($this->owner)
        ->post("{$this->url}/academics/years", ['name' => '', 'starts_on' => 'tomorrow-ish', 'ends_on' => ''])
        ->assertSessionHasErrors(['name', 'starts_on', 'ends_on']);
});

it('refuses an end date before the start date', function () {
    $this->actingAs($this->owner)
        ->post("{$this->url}/academics/years", ['name' => '2026', 'starts_on' => '2026-12-31', 'ends_on' => '2026-01-01'])
        ->assertSessionHasErrors('ends_on');
});

it('refuses the same name twice in one school but allows it at another school', function () {
    ($this->year)(['name' => '2026']);

    $this->actingAs($this->owner)
        ->post("{$this->url}/academics/years", ['name' => '2026', 'starts_on' => '2030-01-01', 'ends_on' => '2030-12-31'])
        ->assertSessionHasErrors('name');

    $otherOwner = userWithRole($this->other, 'owner');

    $this->actingAs($otherOwner)
        ->post('http://beta.somaplus.test/academics/years', ['name' => '2026', 'starts_on' => '2026-01-01', 'ends_on' => '2026-12-31'])
        ->assertSessionHasNoErrors();
});

it('refuses a year that overlaps another year of the school', function () {
    ($this->year)(['name' => '2026', 'starts_on' => '2026-01-01', 'ends_on' => '2026-12-31']);

    $this->actingAs($this->owner)
        ->post("{$this->url}/academics/years", ['name' => '2026-27', 'starts_on' => '2026-12-31', 'ends_on' => '2027-11-30'])
        ->assertSessionHasErrors(['starts_on' => __('academics.years.overlap')]);
});

it('allows a year that starts the day after another ends', function () {
    ($this->year)(['name' => '2026', 'starts_on' => '2026-01-01', 'ends_on' => '2026-12-31']);

    $this->actingAs($this->owner)
        ->post("{$this->url}/academics/years", ['name' => '2027', 'starts_on' => '2027-01-01', 'ends_on' => '2027-12-31'])
        ->assertSessionHasNoErrors();
});

it('lets another school use the same dates', function () {
    ($this->year)(['name' => '2026', 'starts_on' => '2026-01-01', 'ends_on' => '2026-12-31'], $this->other);

    $this->actingAs($this->owner)
        ->post("{$this->url}/academics/years", ['name' => '2026', 'starts_on' => '2026-01-01', 'ends_on' => '2026-12-31'])
        ->assertSessionHasNoErrors();
});

it('keeps only one year current', function () {
    $first = ($this->year)(['name' => '2025', 'starts_on' => '2025-01-01', 'ends_on' => '2025-12-31', 'is_current' => true]);

    $this->actingAs($this->owner)
        ->post("{$this->url}/academics/years", ['name' => '2026', 'starts_on' => '2026-01-01', 'ends_on' => '2026-12-31', 'is_current' => '1'])
        ->assertSessionHasNoErrors();

    $current = ($this->inSchool)(fn () => AcademicYear::where('is_current', true)->pluck('name')->all());

    expect($current)->toBe(['2026'])
        ->and(($this->inSchool)(fn () => $first->fresh()->is_current))->toBeFalse();
});

it('does not touch the current year of another school', function () {
    ($this->year)(['name' => '2026', 'is_current' => true], $this->other);

    $this->actingAs($this->owner)
        ->post("{$this->url}/academics/years", ['name' => '2026', 'starts_on' => '2026-01-01', 'ends_on' => '2026-12-31', 'is_current' => '1']);

    $stillCurrent = ($this->inSchool)(fn () => AcademicYear::where('is_current', true)->count(), $this->other);

    expect($stillCurrent)->toBe(1);
});

it('is enforced by the database that a school has one current year', function () {
    ($this->year)(['name' => '2025', 'is_current' => true]);
    ($this->year)(['name' => '2026', 'is_current' => true]);
})->throws(QueryException::class);

it('lets two schools each have a current year', function () {
    ($this->year)(['name' => '2026', 'is_current' => true]);
    ($this->year)(['name' => '2026', 'is_current' => true], $this->other);

    expect(AcademicYear::query()->withoutGlobalScopes()->where('is_current', true)->count())->toBe(2);
});

it('changes a year', function () {
    $year = ($this->year)(['name' => '2026', 'starts_on' => '2026-01-01', 'ends_on' => '2026-12-31']);

    $this->actingAs($this->owner)
        ->put("{$this->url}/academics/years/{$year->id}", ['name' => '2026 (revised)', 'starts_on' => '2026-01-05', 'ends_on' => '2026-12-20'])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $fresh = ($this->inSchool)(fn () => $year->fresh());

    expect($fresh->name)->toBe('2026 (revised)')->and($fresh->starts_on->toDateString())->toBe('2026-01-05');
});

it('keeps its own dates and name valid when a year is saved again unchanged', function () {
    $year = ($this->year)(['name' => '2026', 'starts_on' => '2026-01-01', 'ends_on' => '2026-12-31']);

    $this->actingAs($this->owner)
        ->put("{$this->url}/academics/years/{$year->id}", ['name' => '2026', 'starts_on' => '2026-01-01', 'ends_on' => '2026-12-31'])
        ->assertSessionHasNoErrors();
});

it('refuses to shrink a year so that a term falls outside it', function () {
    $year = ($this->year)(['name' => '2026', 'starts_on' => '2026-01-01', 'ends_on' => '2026-12-31']);
    ($this->inSchool)(fn () => Term::factory()->create(['academic_year_id' => $year->id, 'starts_on' => '2026-01-10', 'ends_on' => '2026-03-31']));

    $this->actingAs($this->owner)
        ->put("{$this->url}/academics/years/{$year->id}", ['name' => '2026', 'starts_on' => '2026-02-01', 'ends_on' => '2026-12-31'])
        ->assertSessionHasErrors(['starts_on' => __('academics.years.terms_outside')]);
});

it('does not delete a year that still has terms', function () {
    $year = ($this->year)(['name' => '2026']);
    ($this->inSchool)(fn () => Term::factory()->create(['academic_year_id' => $year->id]));

    $this->actingAs($this->owner)
        ->delete("{$this->url}/academics/years/{$year->id}")
        ->assertSessionHasErrors(['delete' => __('academics.years.has_terms')]);

    expect(($this->inSchool)(fn () => AcademicYear::count()))->toBe(1);
});

it('deletes a year without terms', function () {
    $year = ($this->year)(['name' => '2026']);

    $this->actingAs($this->owner)
        ->delete("{$this->url}/academics/years/{$year->id}")
        ->assertRedirect("{$this->url}/academics/years");

    expect(($this->inSchool)(fn () => AcademicYear::count()))->toBe(0);
});

it('adds terms inside the year', function () {
    $year = ($this->year)(['name' => '2026', 'starts_on' => '2026-01-01', 'ends_on' => '2026-12-31']);

    $this->actingAs($this->owner)
        ->post("{$this->url}/academics/years/{$year->id}/terms", ['name' => 'Term 1', 'starts_on' => '2026-01-12', 'ends_on' => '2026-04-03'])
        ->assertRedirect("{$this->url}/academics/years/{$year->id}")
        ->assertSessionHasNoErrors();

    $this->actingAs($this->owner)
        ->post("{$this->url}/academics/years/{$year->id}/terms", ['name' => 'Term 2', 'starts_on' => '2026-05-04', 'ends_on' => '2026-08-14'])
        ->assertSessionHasNoErrors();

    $terms = ($this->inSchool)(fn () => Term::orderBy('starts_on')->get());

    expect($terms->pluck('name')->all())->toBe(['Term 1', 'Term 2'])
        ->and($terms->every(fn ($term) => $term->tenant_id === $this->school->id && $term->academic_year_id === $year->id))->toBeTrue();
});

it('lists the terms of a year on its page', function () {
    $year = ($this->year)(['name' => '2026']);
    ($this->inSchool)(fn () => Term::factory()->create(['academic_year_id' => $year->id, 'name' => 'Muhula wa Kwanza']));

    $this->actingAs($this->owner)
        ->get("{$this->url}/academics/years/{$year->id}")
        ->assertOk()
        ->assertSee('Muhula wa Kwanza');
});

it('refuses a term outside its year', function () {
    $year = ($this->year)(['name' => '2026', 'starts_on' => '2026-01-01', 'ends_on' => '2026-12-31']);

    $this->actingAs($this->owner)
        ->post("{$this->url}/academics/years/{$year->id}/terms", ['name' => 'Term 0', 'starts_on' => '2025-12-01', 'ends_on' => '2026-03-01'])
        ->assertSessionHasErrors('starts_on');

    $this->actingAs($this->owner)
        ->post("{$this->url}/academics/years/{$year->id}/terms", ['name' => 'Term 4', 'starts_on' => '2026-11-01', 'ends_on' => '2027-01-15'])
        ->assertSessionHasErrors('starts_on');
});

it('refuses overlapping terms and a repeated term name', function () {
    $year = ($this->year)(['name' => '2026', 'starts_on' => '2026-01-01', 'ends_on' => '2026-12-31']);
    ($this->inSchool)(fn () => Term::factory()->create(['academic_year_id' => $year->id, 'name' => 'Term 1', 'starts_on' => '2026-01-12', 'ends_on' => '2026-04-03']));

    $this->actingAs($this->owner)
        ->post("{$this->url}/academics/years/{$year->id}/terms", ['name' => 'Term 2', 'starts_on' => '2026-04-03', 'ends_on' => '2026-08-14'])
        ->assertSessionHasErrors(['starts_on' => __('academics.terms.overlap')]);

    $this->actingAs($this->owner)
        ->post("{$this->url}/academics/years/{$year->id}/terms", ['name' => 'Term 1', 'starts_on' => '2026-05-04', 'ends_on' => '2026-08-14'])
        ->assertSessionHasErrors('name');
});

it('allows the same term name in another year', function () {
    $y2025 = ($this->year)(['name' => '2025', 'starts_on' => '2025-01-01', 'ends_on' => '2025-12-31']);
    $y2026 = ($this->year)(['name' => '2026', 'starts_on' => '2026-01-01', 'ends_on' => '2026-12-31']);
    ($this->inSchool)(fn () => Term::factory()->create(['academic_year_id' => $y2025->id, 'name' => 'Term 1', 'starts_on' => '2025-01-12', 'ends_on' => '2025-04-03']));

    $this->actingAs($this->owner)
        ->post("{$this->url}/academics/years/{$y2026->id}/terms", ['name' => 'Term 1', 'starts_on' => '2026-01-12', 'ends_on' => '2026-04-03'])
        ->assertSessionHasNoErrors();
});

it('changes and deletes a term', function () {
    $year = ($this->year)(['name' => '2026', 'starts_on' => '2026-01-01', 'ends_on' => '2026-12-31']);
    $term = ($this->inSchool)(fn () => Term::factory()->create(['academic_year_id' => $year->id, 'name' => 'Term 1', 'starts_on' => '2026-01-12', 'ends_on' => '2026-04-03']));

    $this->actingAs($this->owner)
        ->put("{$this->url}/academics/terms/{$term->id}", ['name' => 'First term', 'starts_on' => '2026-01-12', 'ends_on' => '2026-04-10'])
        ->assertRedirect("{$this->url}/academics/years/{$year->id}")
        ->assertSessionHasNoErrors();

    expect(($this->inSchool)(fn () => $term->fresh()->name))->toBe('First term');

    $this->actingAs($this->owner)->delete("{$this->url}/academics/terms/{$term->id}")->assertRedirect();

    expect(($this->inSchool)(fn () => Term::count()))->toBe(0);
});

it('shows the current year and term on the dashboard', function () {
    $today = Carbon::today();
    $year = ($this->year)([
        'name' => 'Year in progress',
        'starts_on' => $today->copy()->subDays(30)->toDateString(),
        'ends_on' => $today->copy()->addDays(300)->toDateString(),
        'is_current' => true,
    ]);
    ($this->inSchool)(fn () => Term::factory()->create([
        'academic_year_id' => $year->id,
        'name' => 'Term in progress',
        'starts_on' => $today->copy()->subDays(10)->toDateString(),
        'ends_on' => $today->copy()->addDays(10)->toDateString(),
    ]));
    ($this->inSchool)(fn () => Term::factory()->create([
        'academic_year_id' => $year->id,
        'name' => 'Term later',
        'starts_on' => $today->copy()->addDays(40)->toDateString(),
        'ends_on' => $today->copy()->addDays(80)->toDateString(),
    ]));

    $this->actingAs($this->owner)
        ->get($this->url)
        ->assertOk()
        ->assertSee('Year in progress')
        ->assertSee('Term in progress')
        ->assertDontSee('Term later');
});

it('says so on the dashboard when no year is current', function () {
    ($this->year)(['name' => '2020']);

    $this->actingAs($this->owner)
        ->get($this->url)
        ->assertOk()
        ->assertSee(__('dashboard.no_year'));
});
