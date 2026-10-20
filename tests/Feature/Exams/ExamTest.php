<?php

use App\Enums\SubscriptionStatus;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\GradingScale;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Tenant;
use App\Models\Term;
use App\Models\User;
use App\Tenancy\TenantContext;

beforeEach(function () {
    useTestHosts();

    $this->school = Tenant::factory()->create(['slug' => 'acme']);
    $this->other = Tenant::factory()->create(['slug' => 'beta']);
    $this->url = 'http://acme.somaplus.test';
    $this->owner = userWithRole($this->school, 'owner');
    $this->teacher = userWithRole($this->school, 'teacher');
    $this->w = makeExamWorld($this->school);

    $this->inSchool = fn (Closure $work, ?Tenant $school = null) => TenantContext::run($school ?? $this->school, $work);

    $this->payload = fn (array $override = []) => $override + [
        'name' => 'Midterm', 'term_id' => $this->w['term']->id, 'grading_scale_id' => $this->w['scale']->id, 'max_mark' => 100,
    ];
    $this->exam = fn (array $attributes = []) => ($this->inSchool)(fn () => Exam::factory()->create($attributes + [
        'academic_year_id' => $this->w['year']->id, 'term_id' => $this->w['term']->id, 'grading_scale_id' => $this->w['scale']->id,
    ]));
    $this->addMark = fn (Exam $exam) => ($this->inSchool)(function () use ($exam) {
        $student = Student::factory()->create();
        Enrollment::factory()->create(['student_id' => $student->id, 'academic_year_id' => $this->w['year']->id, 'school_class_id' => $this->w['form1']->id]);
        $mark = new ExamMark(['subject_id' => $this->w['math']->id, 'student_id' => $student->id, 'school_class_id' => $this->w['form1']->id, 'score' => '50.00']);
        $mark->exam_id = $exam->id;
        $mark->save();

        return $mark;
    });
});

it('creates an exam in the year of its term and records it in the audit log', function () {
    $this->actingAs($this->owner)->post("{$this->url}/exams", ($this->payload)())->assertSessionHasNoErrors()->assertRedirect();

    ($this->inSchool)(function () {
        $exam = Exam::firstOrFail();
        expect($exam->tenant_id)->toBe($this->school->id)
            ->and($exam->academic_year_id)->toBe($this->w['year']->id)
            ->and($exam->term_id)->toBe($this->w['term']->id)
            ->and($exam->max_mark)->toBe(100)
            ->and($exam->isLocked())->toBeFalse()
            ->and(AuditLog::where('action', 'exam.created')->where('subject_id', $exam->id)->exists())->toBeTrue();
    });
});

it('refuses a bad exam', function (array $override, string $field) {
    $this->actingAs($this->owner)->post("{$this->url}/exams", ($this->payload)($override))->assertSessionHasErrors($field);

    expect(($this->inSchool)(fn () => Exam::count()))->toBe(0);
})->with([
    'no name' => [['name' => ''], 'name'],
    'name too long' => [['name' => str_repeat('x', 101)], 'name'],
    'no term' => [['term_id' => ''], 'term_id'],
    'no scale' => [['grading_scale_id' => ''], 'grading_scale_id'],
    'max zero' => [['max_mark' => 0], 'max_mark'],
    'max too high' => [['max_mark' => 1001], 'max_mark'],
    'max not a number' => [['max_mark' => 'abc'], 'max_mark'],
    'max with decimals' => [['max_mark' => '50.5'], 'max_mark'],
    'term that does not exist' => [['term_id' => 999999], 'term_id'],
]);

it('refuses a term or scale of another school', function () {
    $foreign = makeExamWorld($this->other);

    $this->actingAs($this->owner)->post("{$this->url}/exams", ($this->payload)(['term_id' => $foreign['term']->id]))->assertSessionHasErrors('term_id');
    $this->actingAs($this->owner)->post("{$this->url}/exams", ($this->payload)(['grading_scale_id' => $foreign['scale']->id]))->assertSessionHasErrors('grading_scale_id');

    expect(($this->inSchool)(fn () => Exam::count()))->toBe(0);
});

it('allows a repeated exam name in another term but not in the same term', function () {
    $this->actingAs($this->owner)->post("{$this->url}/exams", ($this->payload)())->assertSessionHasNoErrors();
    $this->actingAs($this->owner)->post("{$this->url}/exams", ($this->payload)())->assertSessionHasErrors('name');
    $this->actingAs($this->owner)->post("{$this->url}/exams", ($this->payload)(['term_id' => $this->w['term2']->id]))->assertSessionHasNoErrors();
});

it('lets an exam be renamed and its maximum changed while it has no marks', function () {
    $exam = ($this->exam)();

    $this->actingAs($this->owner)->put("{$this->url}/exams/{$exam->id}", ($this->payload)(['name' => 'Final', 'max_mark' => 50, 'term_id' => $this->w['term2']->id]))
        ->assertSessionHasNoErrors();

    $exam->refresh();
    expect($exam->name)->toBe('Final')->and($exam->max_mark)->toBe(50)->and($exam->term_id)->toBe($this->w['term2']->id);
});

it('keeps the maximum, term and scale once marks exist but still allows a rename', function () {
    $exam = ($this->exam)();
    ($this->addMark)($exam);
    $other = ($this->inSchool)(fn () => GradingScale::factory()->standard()->create(['name' => 'Other']));

    foreach ([['max_mark' => 50], ['term_id' => $this->w['term2']->id], ['grading_scale_id' => $other->id]] as $change) {
        $this->actingAs($this->owner)->put("{$this->url}/exams/{$exam->id}", ($this->payload)(['name' => 'Midterm'] + $change))
            ->assertSessionHasErrors('max_mark');
    }

    $exam->refresh();
    expect($exam->max_mark)->toBe(100)->and($exam->term_id)->toBe($this->w['term']->id)->and($exam->grading_scale_id)->toBe($this->w['scale']->id);

    $this->actingAs($this->owner)->put("{$this->url}/exams/{$exam->id}", ($this->payload)(['name' => 'Renamed']))->assertSessionHasNoErrors();
    expect($exam->refresh()->name)->toBe('Renamed');
});

it('locks and unlocks an exam, and records both in the audit log', function () {
    $exam = ($this->exam)();

    $this->actingAs($this->owner)->post("{$this->url}/exams/{$exam->id}/lock")->assertRedirect();
    $exam->refresh();
    expect($exam->isLocked())->toBeTrue()->and($exam->locked_by)->toBe($this->owner->id);

    $this->actingAs($this->owner)->post("{$this->url}/exams/{$exam->id}/unlock")->assertRedirect();
    expect($exam->refresh()->isLocked())->toBeFalse()->and($exam->locked_by)->toBeNull();

    ($this->inSchool)(fn () => expect(AuditLog::whereIn('action', ['exam.locked', 'exam.unlocked'])->where('subject_id', $exam->id)->orderBy('id')->pluck('action')->all())->toBe(['exam.locked', 'exam.unlocked']));
});

it('does not change a locked exam', function () {
    $exam = ($this->exam)();
    $this->actingAs($this->owner)->post("{$this->url}/exams/{$exam->id}/lock");

    $this->actingAs($this->owner)->put("{$this->url}/exams/{$exam->id}", ($this->payload)(['name' => 'Sneaky']))->assertSessionHasErrors('exam');
    $this->actingAs($this->owner)->delete("{$this->url}/exams/{$exam->id}")->assertSessionHasErrors('delete');

    expect($exam->refresh()->name)->not->toBe('Sneaky')->and(($this->inSchool)(fn () => Exam::find($exam->id)))->not->toBeNull();
});

it('lets only owners and head teachers lock, unlock and manage exams', function (string $role, bool $manage) {
    $user = userWithRole($this->school, $role);
    $exam = ($this->exam)();
    $this->actingAs($user)->get("{$this->url}/exams")->assertOk();
    $this->actingAs($user)->get("{$this->url}/exams/{$exam->id}")->assertOk();

    $responses = [
        $this->actingAs($user)->get("{$this->url}/exams/create"),
        $this->actingAs($user)->post("{$this->url}/exams", ($this->payload)(['name' => 'By '.$role])),
        $this->actingAs($user)->get("{$this->url}/exams/{$exam->id}/edit"),
        $this->actingAs($user)->post("{$this->url}/exams/{$exam->id}/lock"),
        $this->actingAs($user)->post("{$this->url}/exams/{$exam->id}/unlock"),
        $this->actingAs($user)->delete("{$this->url}/exams/{$exam->id}"),
    ];

    foreach ($responses as $response) {
        $manage ? expect($response->status())->not->toBe(403) : $response->assertForbidden();
    }
})->with([
    'owner' => ['owner', true],
    'head teacher' => ['head_teacher', true],
    'teacher' => ['teacher', false],
]);

it('leaves a teacher unable to unlock an exam that is locked', function () {
    $exam = ($this->exam)();
    $this->actingAs($this->owner)->post("{$this->url}/exams/{$exam->id}/lock");

    $this->actingAs($this->teacher)->post("{$this->url}/exams/{$exam->id}/unlock")->assertForbidden();

    expect($exam->refresh()->isLocked())->toBeTrue();
});

it('deletes an exam without marks and refuses one that has marks', function () {
    $empty = ($this->exam)();
    $marked = ($this->exam)();
    ($this->addMark)($marked);

    $this->actingAs($this->owner)->delete("{$this->url}/exams/{$empty->id}")->assertSessionHasNoErrors();
    $this->actingAs($this->owner)->delete("{$this->url}/exams/{$marked->id}")->assertSessionHasErrors('delete');

    ($this->inSchool)(fn () => expect(Exam::find($empty->id))->toBeNull()->and(Exam::find($marked->id))->not->toBeNull()->and(ExamMark::count())->toBe(1));
});

it('keeps the year, term and subject that exams or marks depend on', function () {
    $exam = ($this->exam)();
    $mark = ($this->addMark)($exam);

    $this->actingAs($this->owner)->delete("{$this->url}/academics/terms/{$this->w['term']->id}")
        ->assertSessionHasErrors(['delete' => __('academics.terms.has_exams')]);
    $this->actingAs($this->owner)->delete("{$this->url}/academics/subjects/{$this->w['math']->id}")
        ->assertSessionHasErrors(['delete' => __('academics.subjects.has_marks')]);
    $this->actingAs($this->owner)->delete("{$this->url}/academics/years/{$this->w['year']->id}")
        ->assertSessionHasErrors();

    ($this->inSchool)(fn () => expect(Term::find($this->w['term']->id))->not->toBeNull()
        ->and(Subject::find($this->w['math']->id))->not->toBeNull()
        ->and(AcademicYear::find($this->w['year']->id))->not->toBeNull());
});

it('deletes a subject and a term that nothing depends on', function () {
    $this->actingAs($this->owner)->delete("{$this->url}/academics/subjects/{$this->w['english']->id}")->assertSessionHasNoErrors();
    $this->actingAs($this->owner)->delete("{$this->url}/academics/terms/{$this->w['term2']->id}")->assertSessionHasNoErrors();

    ($this->inSchool)(fn () => expect(Subject::find($this->w['english']->id))->toBeNull()->and(Term::find($this->w['term2']->id))->toBeNull());
});

it('shows nothing of another school and refuses its exams', function () {
    $foreign = makeExamWorld($this->other);
    $theirs = ($this->inSchool)(fn () => Exam::factory()->create([
        'name' => 'Zxqtheirs', 'academic_year_id' => $foreign['year']->id, 'term_id' => $foreign['term']->id, 'grading_scale_id' => $foreign['scale']->id,
    ]), $this->other);

    $this->actingAs($this->owner)->get("{$this->url}/exams")->assertDontSee('Zxqtheirs');
    foreach (['get' => ["exams/{$theirs->id}", "exams/{$theirs->id}/edit", "exams/{$theirs->id}/marks"], 'post' => ["exams/{$theirs->id}/lock", "exams/{$theirs->id}/unlock", "exams/{$theirs->id}/marks"], 'put' => ["exams/{$theirs->id}"], 'delete' => ["exams/{$theirs->id}"]] as $method => $paths) {
        foreach ($paths as $path) {
            $this->actingAs($this->owner)->{$method}("{$this->url}/{$path}")->assertNotFound();
        }
    }

    expect(($this->inSchool)(fn () => Exam::find($theirs->id)->isLocked(), $this->other))->toBeFalse();
});

it('keeps exams away from accountants and parents', function (string $role) {
    $user = userWithRole($this->school, $role);

    $this->actingAs($user)->get("{$this->url}/exams")->assertForbidden();
    $this->actingAs($user)->get("{$this->url}/exams/".($this->exam)()->id)->assertForbidden();
})->with(['accountant', 'parent_student']);

it('lets a read-only school look but not change', function () {
    $exam = ($this->exam)();
    $this->school->update(['subscription_status' => SubscriptionStatus::ReadOnly]);
    $owner = userWithRole($this->school, 'owner');

    $this->actingAs($owner)->get("{$this->url}/exams")->assertOk();
    $this->actingAs($owner)->get("{$this->url}/exams/{$exam->id}")->assertOk();
    $this->actingAs($owner)->post("{$this->url}/exams", ($this->payload)(['name' => 'Late']))->assertForbidden();
    $this->actingAs($owner)->post("{$this->url}/exams/{$exam->id}/lock")->assertForbidden();
    $this->actingAs($owner)->delete("{$this->url}/exams/{$exam->id}")->assertForbidden();
});

it('shows exams in the sidebar only to people who may look', function () {
    $this->actingAs($this->teacher)->get($this->url)->assertSee('/exams"', false)->assertSee('/grading-scales"', false);
    $this->actingAs(userWithRole($this->school, 'accountant'))->get($this->url)->assertDontSee('/exams"', false);
    $this->actingAs(userWithRole($this->school, 'parent_student'))->get($this->url)->assertDontSee('/grading-scales"', false);
});

it('sends a guest to sign in', function () {
    $this->get("{$this->url}/exams")->assertRedirect("{$this->url}/login");
});

it('gives the central address no exams', function () {
    $this->actingAs(User::factory()->superAdmin()->create())->get('http://somaplus.test/exams')->assertForbidden();
});

it('shows the pages in Swahili when that language is chosen', function () {
    $exam = ($this->exam)();

    $this->actingAs($this->owner)->withSession(['locale' => 'sw'])->get("{$this->url}/exams/{$exam->id}")
        ->assertOk()
        ->assertSee(__('exams.marks.open', [], 'sw'))
        ->assertDontSee(__('exams.marks.open', [], 'en'));
});

it('escapes an exam name', function () {
    ($this->exam)(['name' => '<script>alert(1)</script>']);

    $this->actingAs($this->owner)->get("{$this->url}/exams")->assertDontSee('<script>alert(1)</script>', false)->assertSee('&lt;script&gt;', false);
});
