<?php

use App\Domain\Exams\ReportCard;
use App\Domain\Exams\ResultSheet;
use App\Domain\Exams\StudentResult;
use App\Enums\SubscriptionStatus;
use App\Models\AuditLog;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\Guardian;
use App\Models\SchoolClass;
use App\Models\Stream;
use App\Models\Student;
use App\Models\Tenant;
use App\Tenancy\TenantContext;

beforeEach(function () {
    useTestHosts();

    $this->school = Tenant::factory()->create(['slug' => 'acme']);
    $this->other = Tenant::factory()->create(['slug' => 'beta']);
    $this->url = 'http://acme.somaplus.test';
    $this->teacher = userWithRole($this->school, 'teacher');
    $this->owner = userWithRole($this->school, 'owner');
    $this->w = makeExamWorld($this->school);

    $this->inSchool = fn (Closure $work, ?Tenant $school = null) => TenantContext::run($school ?? $this->school, $work);

    $this->makeExam = fn (array $attributes = []) => ($this->inSchool)(fn () => Exam::factory()->create($attributes + [
        'name' => 'Midterm', 'academic_year_id' => $this->w['year']->id, 'term_id' => $this->w['term']->id,
        'grading_scale_id' => $this->w['scale']->id, 'max_mark' => 100,
        'locked_at' => now(), 'locked_by' => $this->owner->id,
    ]));
    $this->exam = ($this->makeExam)();

    $this->place = fn (string $name, ?SchoolClass $class = null, ?Stream $stream = null) => ($this->inSchool)(function () use ($name, $class, $stream) {
        $student = Student::factory()->create(['first_name' => $name]);
        Enrollment::factory()->create([
            'student_id' => $student->id, 'academic_year_id' => $this->w['year']->id,
            'school_class_id' => ($class ?? $this->w['form1'])->id, 'stream_id' => $stream?->id,
        ]);

        return $student;
    });

    // Stored marks, like RecordMarks leaves them: score as a decimal string, class and stream snapshot.
    $this->mark = fn (Student $student, string $subject, ?string $score, bool $absent = false, ?Exam $exam = null, ?SchoolClass $class = null, ?Stream $stream = null) => ($this->inSchool)(
        fn () => ($exam ?? $this->exam)->marks()->create([
            'subject_id' => $this->w[$subject]->id, 'student_id' => $student->id, 'score' => $score, 'is_absent' => $absent,
            'school_class_id' => ($class ?? $this->w['form1'])->id, 'stream_id' => $stream?->id,
        ])
    );

    $this->results = fn (?Exam $exam = null, ?Stream $stream = null, ?SchoolClass $class = null) => ($this->inSchool)(
        fn () => app(ResultSheet::class)->rows(($exam ?? $this->exam)->fresh(), $class ?? $this->w['form1'], $stream)
    );
    $this->byName = fn (array $rows) => collect($rows)->keyBy(fn (StudentResult $r) => $r->student->first_name);
    $this->page = fn (?int $classId = null, array $extra = []) => "{$this->url}/exams/{$this->exam->id}/results?".http_build_query(array_filter(['school_class_id' => $classId ?? $this->w['form1']->id] + $extra));
});

it('works out totals, averages, grades and positions, and ties share a position', function () {
    $a = ($this->place)('Aone');
    $b = ($this->place)('Btwo');
    $c = ($this->place)('Cthree');
    $d = ($this->place)('Dfour');
    foreach ([[$a, '90', '80'], [$b, '80', '90'], [$c, '70', '60']] as [$s, $m, $e]) {
        ($this->mark)($s, 'math', $m);
        ($this->mark)($s, 'english', $e);
    }
    ($this->mark)($d, 'math', '100'); // one subject only, average 100

    $rows = ($this->byName)(($this->results)());

    expect($rows['Dfour']->classPosition)->toBe(1)
        ->and($rows['Aone']->classPosition)->toBe(2)
        ->and($rows['Btwo']->classPosition)->toBe(2)
        ->and($rows['Cthree']->classPosition)->toBe(4)
        ->and($rows['Aone']->totalText())->toBe('170')
        ->and($rows['Aone']->averageText())->toBe('85')
        ->and($rows['Aone']->band?->grade)->toBe('A')
        ->and($rows['Cthree']->band?->grade)->toBe('C')
        ->and($rows['Aone']->classSize)->toBe(4)
        ->and(collect(($this->results)())->pluck('student.first_name')->all())->toBe(['Dfour', 'Aone', 'Btwo', 'Cthree']);
});

it('keeps decimals exact and never rounds an average up into a better grade', function () {
    $a = ($this->place)('Edge');
    ($this->mark)($a, 'math', '80');
    ($this->mark)($a, 'english', '79.99'); // average 79.995

    $row = ($this->byName)(($this->results)())['Edge'];

    expect($row->totalText())->toBe('159.99')
        ->and($row->averageText())->toBe('79.99')
        ->and($row->band?->grade)->toBe('B');
});

it('grades against the exam maximum, not against 100', function () {
    $exam = ($this->makeExam)(['name' => 'Quiz', 'max_mark' => 50]);
    $a = ($this->place)('Quiza');
    ($this->mark)($a, 'math', '40', false, $exam);
    ($this->mark)($a, 'english', '40', false, $exam);
    $b = ($this->place)('Quizb');
    ($this->mark)($b, 'math', '39.99', false, $exam);
    ($this->mark)($b, 'english', '39.99', false, $exam);

    $rows = ($this->byName)(($this->results)($exam));

    expect($rows['Quiza']->band?->grade)->toBe('A')->and($rows['Quizb']->band?->grade)->toBe('B');
});

it('does not count absent subjects or empty scores, and a zero is a real score', function () {
    $a = ($this->place)('Abs');
    ($this->mark)($a, 'math', '60');
    ($this->mark)($a, 'english', null, true);
    $z = ($this->place)('Zero');
    ($this->mark)($z, 'math', '0');
    ($this->mark)($z, 'english', '0');
    $e = ($this->place)('Empty');
    ($this->mark)($e, 'math', null);

    $rows = ($this->byName)(($this->results)());

    expect($rows['Abs']->subjects)->toBe(1)->and($rows['Abs']->absent)->toBe(1)->and($rows['Abs']->averageText())->toBe('60')
        ->and($rows['Zero']->subjects)->toBe(2)->and($rows['Zero']->averageText())->toBe('0')->and($rows['Zero']->band?->grade)->toBe('F')
        ->and($rows['Zero']->classPosition)->toBe(2)
        ->and($rows['Empty']->hasScores())->toBeFalse()->and($rows['Empty']->classPosition)->toBeNull()->and($rows['Empty']->band)->toBeNull();
});

it('lists a student placed in the class without marks last, with no average or position', function () {
    $a = ($this->place)('Scored');
    ($this->mark)($a, 'math', '50');
    ($this->place)('Nomarks');

    $rows = ($this->results)();
    $last = end($rows);

    expect($last->student->first_name)->toBe('Nomarks')->and($last->classPosition)->toBeNull()->and($last->average())->toBeNull()
        ->and($rows[0]->classSize)->toBe(1);
});

it('gives stream positions inside the stream and class positions across the class', function () {
    $a1 = ($this->place)('Aone', null, $this->w['streamA']);
    $a2 = ($this->place)('Atwo', null, $this->w['streamA']);
    $b1 = ($this->place)('Bone', null, $this->w['streamB']);
    ($this->mark)($a1, 'math', '90', false, null, null, $this->w['streamA']);
    ($this->mark)($a2, 'math', '70', false, null, null, $this->w['streamA']);
    ($this->mark)($b1, 'math', '80', false, null, null, $this->w['streamB']);

    $rows = ($this->byName)(($this->results)());

    expect($rows['Aone']->classPosition)->toBe(1)->and($rows['Aone']->streamPosition)->toBe(1)
        ->and($rows['Bone']->classPosition)->toBe(2)->and($rows['Bone']->streamPosition)->toBe(1)
        ->and($rows['Atwo']->classPosition)->toBe(3)->and($rows['Atwo']->streamPosition)->toBe(2)
        ->and($rows['Atwo']->streamSize)->toBe(2)->and($rows['Bone']->streamSize)->toBe(1);

    $onlyB = ($this->results)(null, $this->w['streamB']);
    expect($onlyB)->toHaveCount(1)->and($onlyB[0]->student->first_name)->toBe('Bone')->and($onlyB[0]->classPosition)->toBe(2);
});

it('keeps results of a past term the same when the student moves class later', function () {
    $a = ($this->place)('Mover');
    ($this->mark)($a, 'math', '75');
    $before = ($this->byName)(($this->results)())['Mover']->averageText();

    ($this->inSchool)(fn () => Enrollment::factory()->create([
        'student_id' => $a->id, 'academic_year_id' => $this->w['year']->id, 'school_class_id' => $this->w['form2']->id,
    ]));

    expect(($this->byName)(($this->results)())['Mover']->averageText())->toBe($before)
        ->and(($this->byName)(($this->results)())['Mover']->classPosition)->toBe(1)
        ->and(($this->results)(null, null, $this->w['form2']))->toHaveCount(1) // listed as placed, without marks
        ->and(($this->results)(null, null, $this->w['form2'])[0]->hasScores())->toBeFalse();
});

it('keeps the graded result after a student leaves school', function () {
    $a = ($this->place)('Leaver');
    ($this->mark)($a, 'math', '66');
    ($this->inSchool)(fn () => $a->forceFill(['status' => 'left'])->save());

    expect(($this->byName)(($this->results)())['Leaver']->classPosition)->toBe(1);
});

it('refuses to work out the results of an exam that is not locked', function () {
    $open = ($this->makeExam)(['name' => 'Open', 'locked_at' => null, 'locked_by' => null]);

    expect(fn () => ($this->results)($open))->toThrow(LogicException::class);
});

it('shows the results page to a teacher with positions and a link to the report card', function () {
    $a = ($this->place)('Pageone');
    ($this->mark)($a, 'math', '88');

    $this->actingAs($this->teacher)->get(($this->page)())
        ->assertOk()->assertSee('Pageone')->assertSee('88')->assertSee('1 / 1')->assertSee('Position in class')
        ->assertSee("/students/{$a->id}/report/{$this->w['term']->id}", false);
});

it('tells the person the exam must be locked and shows no results before that', function () {
    $open = ($this->makeExam)(['name' => 'Openexam', 'locked_at' => null, 'locked_by' => null]);
    $a = ($this->place)('Hidden');
    ($this->mark)($a, 'math', '88', false, $open);

    $this->actingAs($this->teacher)->get("{$this->url}/exams/{$open->id}/results?school_class_id={$this->w['form1']->id}")
        ->assertOk()->assertSee(__('exams.results.not_locked'))->assertDontSee('Hidden');
    expect(($this->inSchool)(fn () => AuditLog::where('action', 'results.viewed')->count()))->toBe(0);
});

// Names are distinctive on purpose: a short name such as "Sa" also turns up in random sample text on the page.
it('shows only the stream asked for but with class-wide positions', function () {
    $a = ($this->place)('Zxqalpha', null, $this->w['streamA']);
    $b = ($this->place)('Zxqbeta', null, $this->w['streamB']);
    ($this->mark)($a, 'math', '90', false, null, null, $this->w['streamA']);
    ($this->mark)($b, 'math', '50', false, null, null, $this->w['streamB']);

    $this->actingAs($this->teacher)->get(($this->page)(null, ['stream_id' => $this->w['streamB']->id]))
        ->assertOk()->assertSee('Zxqbeta')->assertDontSee('Zxqalpha')->assertSee('2 / 2');
});

it('refuses a class or stream of another school or a stream of another class', function () {
    $foreign = TenantContext::run($this->other, fn () => SchoolClass::factory()->create(['name' => 'Foreign']));

    $this->actingAs($this->teacher)->get("{$this->url}/exams/{$this->exam->id}/results?school_class_id={$foreign->id}")
        ->assertSessionHasErrors('school_class_id');
    $this->actingAs($this->teacher)->get(($this->page)($this->w['form2']->id, ['stream_id' => $this->w['streamA']->id]))
        ->assertSessionHasErrors('stream_id');
});

it('does not mix in the marks or students of another school', function () {
    $mine = ($this->place)('Mine');
    ($this->mark)($mine, 'math', '50');

    $otherWorld = makeExamWorld($this->other);
    TenantContext::run($this->other, function () use ($otherWorld) {
        $exam = Exam::factory()->create([
            'name' => 'Theirs', 'academic_year_id' => $otherWorld['year']->id, 'term_id' => $otherWorld['term']->id,
            'grading_scale_id' => $otherWorld['scale']->id, 'max_mark' => 100, 'locked_at' => now(), 'locked_by' => null,
        ]);
        $student = Student::factory()->create(['first_name' => 'Theirstudent']);
        Enrollment::factory()->create(['student_id' => $student->id, 'academic_year_id' => $otherWorld['year']->id, 'school_class_id' => $otherWorld['form1']->id]);
        $exam->marks()->create(['subject_id' => $otherWorld['math']->id, 'student_id' => $student->id, 'score' => '99.37', 'is_absent' => false, 'school_class_id' => $otherWorld['form1']->id]);
    });

    $this->actingAs($this->teacher)->get(($this->page)())->assertOk()->assertSee('Mine')->assertDontSee('Theirstudent')->assertDontSee('99.37');
    expect(($this->results)())->toHaveCount(1);
});

it('answers 404 for the exam, student or term of another school', function () {
    $otherWorld = makeExamWorld($this->other);
    [$exam, $student] = TenantContext::run($this->other, function () use ($otherWorld) {
        return [
            Exam::factory()->create(['name' => 'Theirs', 'academic_year_id' => $otherWorld['year']->id, 'term_id' => $otherWorld['term']->id, 'grading_scale_id' => $otherWorld['scale']->id, 'max_mark' => 100, 'locked_at' => now()]),
            Student::factory()->create(),
        ];
    });

    $this->actingAs($this->teacher);
    $this->get("{$this->url}/exams/{$exam->id}/results")->assertNotFound();
    $this->get("{$this->url}/students/{$student->id}/report/{$this->w['term']->id}")->assertNotFound();
    $mine = ($this->place)('Mine');
    $this->get("{$this->url}/students/{$mine->id}/report/{$otherWorld['term']->id}")->assertNotFound();
});

it('lets only the roles with results.view open results and report cards', function (string $role, bool $allowed) {
    $user = userWithRole($this->school, $role);
    $a = ($this->place)('Roleone');
    ($this->mark)($a, 'math', '70');

    $results = $this->actingAs($user)->get(($this->page)());
    $report = $this->actingAs($user)->get("{$this->url}/students/{$a->id}/report/{$this->w['term']->id}");

    $allowed ? $results->assertOk() : $results->assertForbidden();
    $allowed ? $report->assertOk() : $report->assertForbidden();
})->with([
    'owner' => ['owner', true],
    'head teacher' => ['head_teacher', true],
    'teacher' => ['teacher', true],
    'accountant' => ['accountant', false],
    'parent' => ['parent_student', false],
]);

it('keeps guests out and never writes through these routes', function () {
    $a = ($this->place)('Guestcase');

    $this->get(($this->page)())->assertRedirect("{$this->url}/login");
    $this->get("{$this->url}/students/{$a->id}/report/{$this->w['term']->id}")->assertRedirect("{$this->url}/login");
    $this->actingAs($this->owner)->post("{$this->url}/exams/{$this->exam->id}/results", [])->assertStatus(405);
    $this->actingAs($this->owner)->put("{$this->url}/students/{$a->id}/report/{$this->w['term']->id}", [])->assertStatus(405);
});

it('lets a read-only school look at results and report cards', function () {
    $a = ($this->place)('Readonly');
    ($this->mark)($a, 'math', '77');
    $this->school->update(['subscription_status' => SubscriptionStatus::ReadOnly]);
    $teacher = userWithRole($this->school, 'teacher');

    $this->actingAs($teacher)->get(($this->page)())->assertOk()->assertSee('Readonly')->assertSee('77');
    $this->actingAs($teacher)->get("{$this->url}/students/{$a->id}/report/{$this->w['term']->id}")->assertOk()->assertSee('Mathematics');
});

it('writes the viewing of results and report cards to the audit log with ids only', function () {
    $a = ($this->place)('Audited');
    ($this->mark)($a, 'math', '77.31');

    $this->actingAs($this->teacher)->get(($this->page)())->assertOk();
    $this->actingAs($this->teacher)->get("{$this->url}/students/{$a->id}/report/{$this->w['term']->id}")->assertOk();

    $logs = ($this->inSchool)(fn () => AuditLog::query()->whereIn('action', ['results.viewed', 'report.viewed'])->get());
    expect($logs->pluck('action')->sort()->values()->all())->toBe(['report.viewed', 'results.viewed'])
        ->and($logs->firstWhere('action', 'results.viewed')->subject_id)->toBe($this->exam->id)
        ->and($logs->firstWhere('action', 'report.viewed')->subject_id)->toBe($a->id)
        ->and($logs->every(fn ($l) => $l->user_id === $this->teacher->id))->toBeTrue()
        ->and(json_encode($logs->toArray()))->not->toContain('Audited')->not->toContain('77.31');
});

it('does not log a viewing that was refused', function () {
    $accountant = userWithRole($this->school, 'accountant');
    $a = ($this->place)('Refused');

    $this->actingAs($accountant)->get(($this->page)())->assertForbidden();
    $this->actingAs($accountant)->get("{$this->url}/students/{$a->id}/report/{$this->w['term']->id}")->assertForbidden();

    expect(($this->inSchool)(fn () => AuditLog::query()->whereIn('action', ['results.viewed', 'report.viewed'])->count()))->toBe(0);
});

it('builds a report card of the locked exams of a term with subject grades and positions', function () {
    $a = ($this->place)('Carda', null, $this->w['streamA']);
    $b = ($this->place)('Cardb', null, $this->w['streamA']);
    ($this->mark)($a, 'math', '90', false, null, null, $this->w['streamA']);
    ($this->mark)($a, 'english', '45', false, null, null, $this->w['streamA']);
    ($this->mark)($b, 'math', '95', false, null, null, $this->w['streamA']);
    ($this->mark)($b, 'english', '95', false, null, null, $this->w['streamA']);
    $second = ($this->makeExam)(['name' => 'Endterm']);
    ($this->mark)($a, 'math', null, true, $second, null, $this->w['streamA']);
    $open = ($this->makeExam)(['name' => 'Unlockedexam', 'locked_at' => null, 'locked_by' => null]);
    ($this->mark)($a, 'math', '1', false, $open, null, $this->w['streamA']);
    $otherTerm = ($this->makeExam)(['name' => 'Othertermexam', 'term_id' => $this->w['term2']->id]);
    ($this->mark)($a, 'math', '5', false, $otherTerm, null, $this->w['streamA']);

    $cards = ($this->inSchool)(fn () => app(ReportCard::class)->handle($a, $this->w['term']));

    expect($cards)->toHaveCount(2)
        ->and(collect($cards)->map(fn ($c) => $c->exam->name)->all())->toBe(['Midterm', 'Endterm'])
        ->and(collect($cards[0]->lines)->map(fn ($l) => [$l->subject->name, $l->scoreText(), $l->band?->grade])->all())
        ->toBe([['English', '45', 'F'], ['Mathematics', '90', 'A']])
        ->and($cards[0]->result->totalText())->toBe('135')
        ->and($cards[0]->result->averageText())->toBe('67.5')
        ->and($cards[0]->result->band?->grade)->toBe('C')
        ->and($cards[0]->result->classPosition)->toBe(2)
        ->and($cards[0]->result->classSize)->toBe(2)
        ->and($cards[0]->result->streamPosition)->toBe(2)
        ->and($cards[1]->lines[0]->absent)->toBeTrue()
        ->and($cards[1]->result->hasScores())->toBeFalse();
});

it('shows the report card page with only the student\'s own marks', function () {
    $a = ($this->place)('Pagecard');
    $b = ($this->place)('Otherchild');
    ($this->mark)($a, 'math', '91.37');
    ($this->mark)($b, 'math', '33.41');

    $this->actingAs($this->teacher)->get("{$this->url}/students/{$a->id}/report/{$this->w['term']->id}")
        ->assertOk()->assertSee('Pagecard')->assertSee('Midterm')->assertSee('Mathematics')->assertSee('91.37')
        ->assertDontSee('Otherchild')->assertDontSee('33.41');
});

it('shows an empty report card for a term without locked marks', function () {
    $a = ($this->place)('Emptycard');

    $this->actingAs($this->teacher)->get("{$this->url}/students/{$a->id}/report/{$this->w['term2']->id}")
        ->assertOk()->assertSee(__('exams.report.empty'));
});

it('shows no guardian data on the results or the report card', function () {
    $a = ($this->place)('Private');
    ($this->mark)($a, 'math', '60');
    ($this->inSchool)(function () use ($a) {
        $guardian = Guardian::factory()->create(['name' => 'Secretguardian', 'phone' => '+255700111222']);
        $a->guardians()->attach($guardian->id, ['relationship' => 'mother', 'is_primary' => true]);
    });

    $this->actingAs($this->teacher)->get(($this->page)())->assertOk()->assertDontSee('Secretguardian')->assertDontSee('255700111222');
    $this->actingAs($this->teacher)->get("{$this->url}/students/{$a->id}/report/{$this->w['term']->id}")->assertOk()->assertDontSee('Secretguardian')->assertDontSee('255700111222');
});

it('offers report cards on the student page only to those who may see them', function () {
    $a = ($this->place)('Linked');
    ($this->mark)($a, 'math', '60');
    $accountant = userWithRole($this->school, 'accountant');

    $this->actingAs($this->teacher)->get("{$this->url}/students/{$a->id}")->assertOk()
        ->assertSee("/students/{$a->id}/report/{$this->w['term']->id}", false);
    $this->actingAs($accountant)->get("{$this->url}/students/{$a->id}")->assertOk()
        ->assertDontSee('/report/', false)->assertDontSee(__('students.show.reports'));
});

it('offers the results from the exam page only when the exam is locked', function () {
    $open = ($this->makeExam)(['name' => 'Notyet', 'locked_at' => null, 'locked_by' => null]);

    $this->actingAs($this->teacher)->get("{$this->url}/exams/{$this->exam->id}")->assertOk()->assertSee("/exams/{$this->exam->id}/results", false);
    $this->actingAs($this->teacher)->get("{$this->url}/exams/{$open->id}")->assertOk()->assertDontSee("/exams/{$open->id}/results", false);
});

it('gives the new permission to the roles of every school', function () {
    foreach (['owner', 'head_teacher', 'teacher'] as $role) {
        $user = userWithRole($this->school, $role);
        expect(($this->inSchool)(fn () => $user->can('results.view')))->toBeTrue();
    }
    foreach (['accountant', 'parent_student'] as $role) {
        $user = userWithRole($this->school, $role);
        expect(($this->inSchool)(fn () => $user->can('results.view')))->toBeFalse();
    }
});

it('shows the pages in Swahili', function () {
    $a = ($this->place)('Swahili');
    ($this->mark)($a, 'math', '60');

    $this->actingAs($this->teacher)->withSession(['locale' => 'sw'])->get(($this->page)())
        ->assertOk()->assertSee('Nafasi darasani')->assertSee('Jumla');
});
