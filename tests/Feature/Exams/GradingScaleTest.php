<?php

use App\Enums\SubscriptionStatus;
use App\Models\AuditLog;
use App\Models\Exam;
use App\Models\GradingScale;
use App\Models\Tenant;
use App\Models\User;
use App\Tenancy\TenantContext;

beforeEach(function () {
    useTestHosts();

    $this->school = Tenant::factory()->create(['slug' => 'acme']);
    $this->other = Tenant::factory()->create(['slug' => 'beta']);
    $this->url = 'http://acme.somaplus.test';
    $this->owner = userWithRole($this->school, 'owner');
    $this->w = makeExamWorld($this->school);

    $this->inSchool = fn (Closure $work, ?Tenant $school = null) => TenantContext::run($school ?? $this->school, $work);

    $this->bands = fn (array $rows) => collect($rows)->map(fn ($r) => ['min_mark' => (string) $r[0], 'grade' => $r[1]] + (isset($r[2]) ? ['points' => (string) $r[2]] : []))->all();
    $this->createScale = fn (array $override = []) => $this->actingAs($this->owner)->post("{$this->url}/grading-scales", $override + [
        'name' => 'New scale',
        'bands' => ($this->bands)([[0, 'F'], [50, 'D'], [80, 'A']]),
    ]);
    $this->useScale = fn (GradingScale $scale) => ($this->inSchool)(fn () => Exam::factory()->create([
        'academic_year_id' => $this->w['year']->id, 'term_id' => $this->w['term']->id, 'grading_scale_id' => $scale->id,
    ]));
});

it('creates a scale with its bands in order and records it in the audit log', function () {
    ($this->createScale)(['name' => 'Primary', 'bands' => ($this->bands)([[80, 'A', 1], [0, 'F', 5], [50, 'D', 4]])])
        ->assertSessionHasNoErrors()->assertRedirect();

    ($this->inSchool)(function () {
        $scale = GradingScale::where('name', 'Primary')->firstOrFail();
        expect($scale->tenant_id)->toBe($this->school->id)
            ->and($scale->bands->pluck('grade')->all())->toBe(['F', 'D', 'A'])
            ->and($scale->bands->pluck('min_mark')->all())->toBe(['0.00', '50.00', '80.00'])
            ->and($scale->bands->pluck('points')->all())->toBe([5, 4, 1])
            ->and($scale->bands->every(fn ($b) => $b->tenant_id === $this->school->id))->toBeTrue()
            ->and(AuditLog::where('action', 'grading_scale.created')->where('subject_id', $scale->id)->exists())->toBeTrue();
    });
});

it('ignores the empty spare lines of the form', function () {
    ($this->createScale)(['bands' => [
        ['min_mark' => '0', 'grade' => 'F', 'points' => '', 'remark' => ''],
        ['min_mark' => '', 'grade' => '', 'points' => '', 'remark' => ''],
        ['min_mark' => '50', 'grade' => 'P', 'points' => '', 'remark' => ''],
    ]])->assertSessionHasNoErrors();

    expect(($this->inSchool)(fn () => GradingScale::where('name', 'New scale')->firstOrFail()->bands->count()))->toBe(2);
});

it('accepts decimal boundaries', function () {
    ($this->createScale)(['bands' => ($this->bands)([[0, 'F'], ['44.5', 'D'], ['74,5', 'A']])])->assertSessionHasNoErrors();

    expect(($this->inSchool)(fn () => GradingScale::where('name', 'New scale')->firstOrFail()->bands->pluck('min_mark')->all()))
        ->toBe(['0.00', '44.50', '74.50']);
});

it('refuses a bad scale and saves nothing', function (array $override) {
    ($this->createScale)($override)->assertSessionHasErrors();

    expect(($this->inSchool)(fn () => GradingScale::where('name', 'New scale')->count()))->toBe(0);
})->with(function () {
    $b = fn (array $rows) => collect($rows)->map(fn ($r) => ['min_mark' => (string) $r[0], 'grade' => $r[1]])->all();

    return [
        'no name' => [['name' => '']],
        'no bands' => [['bands' => []]],
        'no band at zero' => [['bands' => $b([[10, 'F'], [50, 'P']])]],
        'two bands at the same percentage' => [['bands' => $b([[0, 'F'], [50, 'D'], [50, 'C']])]],
        'two bands with the same grade' => [['bands' => $b([[0, 'F'], [50, 'F']])]],
        'the same grade in other letters' => [['bands' => $b([[0, 'a'], [50, 'A']])]],
        'above 100' => [['bands' => $b([[0, 'F'], [101, 'A']])]],
        'not a number' => [['bands' => $b([[0, 'F'], ['abc', 'A']])]],
        'negative' => [['bands' => $b([[0, 'F'], ['-5', 'A']])]],
        'three decimals' => [['bands' => $b([[0, 'F'], ['50.555', 'A']])]],
        'no grade' => [['bands' => $b([[0, 'F'], [50, '']])]],
        'grade too long' => [['bands' => $b([[0, 'F'], [50, 'ABCDEF']])]],
        'points too high' => [['bands' => [['min_mark' => '0', 'grade' => 'F', 'points' => '21']]]],
        'points not a whole number' => [['bands' => [['min_mark' => '0', 'grade' => 'F', 'points' => '1.5']]]],
        'remark too long' => [['bands' => [['min_mark' => '0', 'grade' => 'F', 'remark' => str_repeat('x', 101)]]]],
        'too many bands' => [['bands' => collect(range(0, 15))->map(fn ($i) => ['min_mark' => (string) $i, 'grade' => "G{$i}"])->all()]],
    ];
});

it('refuses a repeated name in the same school but not in another', function () {
    ($this->inSchool)(fn () => GradingScale::factory()->standard()->create(['name' => 'Primary']), $this->other);

    ($this->createScale)(['name' => 'Primary'])->assertSessionHasNoErrors();
    ($this->createScale)(['name' => 'Primary'])->assertSessionHasErrors('name');
});

it('changes a scale that no exam uses', function () {
    $scale = $this->w['scale'];

    $this->actingAs($this->owner)->put("{$this->url}/grading-scales/{$scale->id}", [
        'name' => 'Changed', 'bands' => ($this->bands)([[0, 'F'], [40, 'P']]),
    ])->assertSessionHasNoErrors();

    ($this->inSchool)(function () use ($scale) {
        $fresh = GradingScale::findOrFail($scale->id)->load('bands');
        expect($fresh->name)->toBe('Changed')->and($fresh->bands->pluck('grade')->all())->toBe(['F', 'P']);
    });
});

it('never changes a scale that an exam uses', function () {
    $scale = $this->w['scale'];
    ($this->useScale)($scale);

    $this->actingAs($this->owner)->put("{$this->url}/grading-scales/{$scale->id}", [
        'name' => 'Changed', 'bands' => ($this->bands)([[0, 'F'], [40, 'P']]),
    ])->assertSessionHasErrors('name');

    ($this->inSchool)(function () use ($scale) {
        $fresh = GradingScale::findOrFail($scale->id)->load('bands');
        expect($fresh->name)->toBe('Standard')->and($fresh->bands->pluck('grade')->all())->toBe(['F', 'D', 'C', 'B', 'A']);
    });
});

it('keeps the bands when an invalid change is refused', function () {
    $scale = $this->w['scale'];

    $this->actingAs($this->owner)->put("{$this->url}/grading-scales/{$scale->id}", [
        'name' => 'Broken', 'bands' => ($this->bands)([[10, 'F']]),
    ])->assertSessionHasErrors('bands');

    expect(($this->inSchool)(fn () => GradingScale::findOrFail($scale->id)->bands()->count()))->toBe(5);
});

it('deletes an unused scale and refuses a used one', function () {
    $unused = ($this->inSchool)(fn () => GradingScale::factory()->standard()->create(['name' => 'Spare']));
    $used = $this->w['scale'];
    ($this->useScale)($used);

    $this->actingAs($this->owner)->delete("{$this->url}/grading-scales/{$unused->id}")->assertSessionHasNoErrors();
    $this->actingAs($this->owner)->delete("{$this->url}/grading-scales/{$used->id}")->assertSessionHasErrors('delete');

    ($this->inSchool)(function () use ($unused, $used) {
        expect(GradingScale::find($unused->id))->toBeNull()
            ->and(GradingScale::find($used->id)?->bands()->count())->toBe(5);
    });
});

it('copies a used scale so the copy can be changed', function () {
    $used = $this->w['scale'];
    $exam = ($this->useScale)($used);

    $this->actingAs($this->owner)->post("{$this->url}/grading-scales/{$used->id}/copy")->assertRedirect();
    $this->actingAs($this->owner)->post("{$this->url}/grading-scales/{$used->id}/copy")->assertRedirect();

    ($this->inSchool)(function () use ($used, $exam) {
        $copies = GradingScale::where('name', 'like', 'Standard (copy)%')->orderBy('id')->get();
        expect($copies)->toHaveCount(2)
            ->and($copies[1]->name)->toBe('Standard (copy) 2')
            ->and($copies[0]->bands->pluck('grade')->all())->toBe($used->bands->pluck('grade')->all())
            ->and($copies[0]->isUsed())->toBeFalse()
            ->and($exam->refresh()->grading_scale_id)->toBe($used->id);
    });
});

it('lets teachers look but only owners and head teachers change scales', function (string $role, bool $manage) {
    $user = userWithRole($this->school, $role);
    $scale = $this->w['scale'];

    $this->actingAs($user)->get("{$this->url}/grading-scales")->assertOk();

    $create = $this->actingAs($user)->get("{$this->url}/grading-scales/create");
    $store = $this->actingAs($user)->post("{$this->url}/grading-scales", ['name' => 'X', 'bands' => ($this->bands)([[0, 'F']])]);
    $edit = $this->actingAs($user)->get("{$this->url}/grading-scales/{$scale->id}/edit");
    $delete = $this->actingAs($user)->delete("{$this->url}/grading-scales/{$scale->id}");
    $copy = $this->actingAs($user)->post("{$this->url}/grading-scales/{$scale->id}/copy");

    foreach ([$create, $store, $edit, $delete, $copy] as $response) {
        $manage ? expect($response->status())->not->toBe(403) : $response->assertForbidden();
    }
})->with([
    'owner' => ['owner', true],
    'head teacher' => ['head_teacher', true],
    'teacher' => ['teacher', false],
]);

it('keeps scales away from accountants and parents', function (string $role) {
    $this->actingAs(userWithRole($this->school, $role))->get("{$this->url}/grading-scales")->assertForbidden();
})->with(['accountant', 'parent_student']);

it('shows nothing of another school and refuses its scales', function () {
    $theirs = ($this->inSchool)(fn () => GradingScale::factory()->standard()->create(['name' => 'Zxqtheirs']), $this->other);

    $this->actingAs($this->owner)->get("{$this->url}/grading-scales")->assertDontSee('Zxqtheirs');
    $this->actingAs($this->owner)->get("{$this->url}/grading-scales/{$theirs->id}/edit")->assertNotFound();
    $this->actingAs($this->owner)->put("{$this->url}/grading-scales/{$theirs->id}", ['name' => 'X', 'bands' => ($this->bands)([[0, 'F']])])->assertNotFound();
    $this->actingAs($this->owner)->delete("{$this->url}/grading-scales/{$theirs->id}")->assertNotFound();
    $this->actingAs($this->owner)->post("{$this->url}/grading-scales/{$theirs->id}/copy")->assertNotFound();

    expect(($this->inSchool)(fn () => GradingScale::find($theirs->id)?->bands()->count(), $this->other))->toBe(5);
});

it('escapes what is typed in a scale', function () {
    ($this->createScale)(['name' => '<script>alert(1)</script>', 'bands' => [['min_mark' => '0', 'grade' => '<b>', 'remark' => '<img src=x onerror=alert(1)>']]])
        ->assertSessionHasNoErrors();

    $this->actingAs($this->owner)->get("{$this->url}/grading-scales")
        ->assertDontSee('<script>alert(1)</script>', false)
        ->assertDontSee('<img src=x', false)
        ->assertSee('&lt;script&gt;', false);
});

it('lets a read-only school look but not change', function () {
    $this->school->update(['subscription_status' => SubscriptionStatus::ReadOnly]);
    $owner = userWithRole($this->school, 'owner');

    $this->actingAs($owner)->get("{$this->url}/grading-scales")->assertOk();
    $this->actingAs($owner)->post("{$this->url}/grading-scales", ['name' => 'X', 'bands' => ($this->bands)([[0, 'F']])])->assertForbidden();
    $this->actingAs($owner)->delete("{$this->url}/grading-scales/{$this->w['scale']->id}")->assertForbidden();
});

it('sends a guest to sign in and gives the central address no scales', function () {
    $this->get("{$this->url}/grading-scales")->assertRedirect("{$this->url}/login");
});

it('gives the central address no scales', function () {
    $this->actingAs(User::factory()->superAdmin()->create())->get('http://somaplus.test/grading-scales')->assertForbidden();
});
