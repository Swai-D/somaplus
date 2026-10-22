<?php

use App\Enums\SubscriptionStatus;
use App\Models\AuditLog;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\GradingScale;
use App\Models\SchoolClass;
use App\Models\Stream;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Tenant;
use App\Models\User;
use App\Tenancy\TenantContext;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    useTestHosts();

    $this->school = Tenant::factory()->create(['slug' => 'acme']);
    $this->other = Tenant::factory()->create(['slug' => 'beta']);
    $this->url = 'http://acme.somaplus.test';
    $this->teacher = userWithRole($this->school, 'teacher');
    $this->owner = userWithRole($this->school, 'owner');
    $this->w = makeExamWorld($this->school);

    // The tests' teacher teaches Form 1 and Form 2 in Mathematics and English,
    // because a teacher may enter marks only for what the school assigned them.
    $teacherId = $this->teacher->id;
    $assigned = [$this->w['form1']->id, $this->w['form2']->id];
    $subjectIds = [$this->w['math']->id, $this->w['english']->id];
    TenantContext::run($this->school, function () use ($teacherId, $assigned, $subjectIds) {
        $profile = Teacher::factory()->create(['user_id' => $teacherId]);
        $profile->classes()->attach($assigned);
        $profile->subjects()->attach($subjectIds);
    });

    $this->inSchool = fn (Closure $work, ?Tenant $school = null) => TenantContext::run($school ?? $this->school, $work);

    $this->exam = ($this->inSchool)(fn () => Exam::factory()->create([
        'name' => 'Midterm', 'academic_year_id' => $this->w['year']->id, 'term_id' => $this->w['term']->id,
        'grading_scale_id' => $this->w['scale']->id, 'max_mark' => 100,
    ]));

    $this->place = fn (array $attributes, ?SchoolClass $class = null, ?Stream $stream = null) => ($this->inSchool)(function () use ($attributes, $class, $stream) {
        $student = Student::factory()->create($attributes);
        Enrollment::factory()->create([
            'student_id' => $student->id, 'academic_year_id' => $this->w['year']->id,
            'school_class_id' => ($class ?? $this->w['form1'])->id, 'stream_id' => $stream?->id,
        ]);

        return $student;
    });

    $this->save = fn (array $rows, array $extra = [], ?User $user = null, ?Exam $exam = null) => $this->actingAs($user ?? $this->teacher)
        ->post('http://acme.somaplus.test/exams/'.($exam ?? $this->exam)->id.'/marks', $extra + [
            'school_class_id' => $this->w['form1']->id, 'subject_id' => $this->w['math']->id, 'rows' => $rows,
        ]);

    $this->marks = fn () => ($this->inSchool)(fn () => ExamMark::query()->orderBy('student_id')->get());
});

it('shows the sheet of a class with the students placed in it and no others', function () {
    ($this->place)(['first_name' => 'Inone']);
    ($this->place)(['first_name' => 'Intwo'], $this->w['form2']);
    $left = ($this->place)(['first_name' => 'Gone']);
    ($this->inSchool)(fn () => $left->forceFill(['status' => 'left'])->save());

    $this->actingAs($this->teacher)->get("{$this->url}/exams/{$this->exam->id}/marks?school_class_id={$this->w['form1']->id}&subject_id={$this->w['math']->id}")
        ->assertOk()->assertSee('Inone')->assertDontSee('Intwo')->assertDontSee('Gone');
});

it('shows only the stream asked for', function () {
    ($this->place)(['first_name' => 'Ina'], null, $this->w['streamA']);
    ($this->place)(['first_name' => 'Inb'], null, $this->w['streamB']);

    $this->actingAs($this->teacher)->get("{$this->url}/exams/{$this->exam->id}/marks?school_class_id={$this->w['form1']->id}&stream_id={$this->w['streamA']->id}&subject_id={$this->w['math']->id}")
        ->assertOk()->assertSee('Ina')->assertDontSee('Inb');
});

it('records scores exactly with two decimals and records it in the audit log', function () {
    $a = ($this->place)([]);
    $b = ($this->place)([]);
    $c = ($this->place)([]);

    ($this->save)([$a->id => ['score' => '85'], $b->id => ['score' => '45,5'], $c->id => ['score' => '0']])
        ->assertSessionHasNoErrors()->assertRedirect();

    $marks = ($this->marks)();
    expect($marks->pluck('score', 'student_id')->all())->toBe([$a->id => '85.00', $b->id => '45.50', $c->id => '0.00'])
        ->and($marks->every(fn ($m) => $m->tenant_id === $this->school->id && $m->entered_by === $this->teacher->id && $m->school_class_id === $this->w['form1']->id))->toBeTrue()
        ->and($marks->every(fn ($m) => $m->is_absent === false))->toBeTrue()
        ->and(($this->inSchool)(fn () => AuditLog::where('action', 'marks.recorded')->where('subject_id', $this->exam->id)->count()))->toBe(1);
});

it('treats zero as a score and not as empty', function () {
    $a = ($this->place)([]);

    ($this->save)([$a->id => ['score' => '0']])->assertSessionHasNoErrors();

    expect(($this->marks)())->toHaveCount(1)->and(($this->marks)()->first()->score)->toBe('0.00');
});

it('accepts the highest score and refuses one above it', function () {
    $a = ($this->place)([]);

    ($this->save)([$a->id => ['score' => '100']])->assertSessionHasNoErrors();
    ($this->save)([$a->id => ['score' => '100.01']])->assertSessionHasErrors("rows.{$a->id}.score");

    expect(($this->marks)()->first()->score)->toBe('100.00');
});

it('uses the maximum of the exam, not 100', function () {
    $exam = ($this->inSchool)(fn () => Exam::factory()->create([
        'name' => 'Quiz', 'academic_year_id' => $this->w['year']->id, 'term_id' => $this->w['term']->id,
        'grading_scale_id' => $this->w['scale']->id, 'max_mark' => 50,
    ]));
    $a = ($this->place)([]);

    ($this->save)([$a->id => ['score' => '50.01']], [], null, $exam)->assertSessionHasErrors("rows.{$a->id}.score");
    ($this->save)([$a->id => ['score' => '50']], [], null, $exam)->assertSessionHasNoErrors();
});

it('refuses scores that are not plain numbers', function (string $typed) {
    $a = ($this->place)([]);

    ($this->save)([$a->id => ['score' => $typed]])->assertSessionHasErrors("rows.{$a->id}.score");

    expect(($this->marks)())->toHaveCount(0);
})->with(['abc', '-1', '+5', '45.555', '1e2', '4 5', '45.', '.5', '0x1F', 'NaN', '45%', '1; drop table exam_marks', '<script>', '100,001']);

it('refuses an array or a number where a text is expected', function () {
    $a = ($this->place)([]);

    ($this->save)([$a->id => ['score' => ['1', '2']]])->assertSessionHasErrors("rows.{$a->id}.score");
    expect(($this->marks)())->toHaveCount(0);
});

it('marks a student absent without a score, and refuses absent together with a score', function () {
    $a = ($this->place)([]);

    ($this->save)([$a->id => ['score' => '', 'absent' => '1']])->assertSessionHasNoErrors();
    $mark = ($this->marks)()->first();
    expect($mark->is_absent)->toBeTrue()->and($mark->score)->toBeNull();

    ($this->save)([$a->id => ['score' => '50', 'absent' => '1']])->assertSessionHasErrors("rows.{$a->id}.score");
    expect(($this->marks)()->first()->is_absent)->toBeTrue();
});

it('turns an absent mark into a score when it is entered later', function () {
    $a = ($this->place)([]);

    ($this->save)([$a->id => ['absent' => '1']]);
    ($this->save)([$a->id => ['score' => '70', 'absent' => '0']]);

    $mark = ($this->marks)()->first();
    expect($mark->is_absent)->toBeFalse()->and($mark->score)->toBe('70.00')->and(($this->marks)())->toHaveCount(1);
});

it('leaves an empty score alone and does not create a row for it', function () {
    $a = ($this->place)([]);
    $b = ($this->place)([]);

    ($this->save)([$a->id => ['score' => '60'], $b->id => ['score' => '']]);
    ($this->save)([$a->id => ['score' => '']]);

    $marks = ($this->marks)();
    expect($marks)->toHaveCount(1)->and($marks->first()->score)->toBe('60.00');
});

it('corrects a score when the same sheet is sent again without adding rows', function () {
    $a = ($this->place)([]);

    ($this->save)([$a->id => ['score' => '40']]);
    ($this->save)([$a->id => ['score' => '40']]);
    ($this->save)([$a->id => ['score' => '65.25']]);

    expect(($this->marks)())->toHaveCount(1)->and(($this->marks)()->first()->score)->toBe('65.25');
});

it('saves nothing at all when one row is wrong', function () {
    $a = ($this->place)([]);
    $b = ($this->place)([]);

    ($this->save)([$a->id => ['score' => '90'], $b->id => ['score' => 'oops']])
        ->assertSessionHasErrors("rows.{$b->id}.score");

    expect(($this->marks)())->toHaveCount(0)
        ->and(($this->inSchool)(fn () => AuditLog::where('action', 'marks.recorded')->count()))->toBe(0);
});

it('keeps what the person typed when a row is refused', function () {
    $a = ($this->place)([]);

    ($this->save)([$a->id => ['score' => '9x']])
        ->assertSessionHasErrors("rows.{$a->id}.score")
        ->assertSessionHasInput("rows.{$a->id}.score", '9x');
});

it('refuses a student who is not in the class for the exam year', function () {
    $mine = ($this->place)([]);
    $elsewhere = ($this->place)([], $this->w['form2']);
    $left = ($this->place)([]);
    ($this->inSchool)(fn () => $left->forceFill(['status' => 'left'])->save());
    $foreign = ($this->inSchool)(fn () => Student::factory()->create(), $this->other);

    foreach ([$elsewhere->id, $left->id, $foreign->id, 999999, 'abc', '0', '-1', '1.5'] as $id) {
        ($this->save)([$mine->id => ['score' => '70'], $id => ['score' => '70']])->assertSessionHasErrors('rows');
    }

    expect(($this->marks)())->toHaveCount(0);
});

it('refuses a stream that is not of the class, and a class, stream or subject of another school', function () {
    $foreign = makeExamWorld($this->other);
    $a = ($this->place)([]);
    $rows = [$a->id => ['score' => '70']];

    ($this->save)($rows, ['school_class_id' => $this->w['form2']->id, 'stream_id' => $this->w['streamA']->id])->assertSessionHasErrors('stream_id');
    ($this->save)($rows, ['school_class_id' => $foreign['form1']->id])->assertSessionHasErrors('school_class_id');
    ($this->save)($rows, ['stream_id' => $foreign['streamA']->id])->assertSessionHasErrors('stream_id');
    ($this->save)($rows, ['subject_id' => $foreign['math']->id])->assertSessionHasErrors('subject_id');
    ($this->save)($rows, ['subject_id' => ''])->assertSessionHasErrors('subject_id');
    ($this->save)([])->assertSessionHasErrors('rows');

    expect(($this->marks)())->toHaveCount(0);
});

it('refuses more than five hundred rows', function () {
    $rows = collect(range(1, 501))->mapWithKeys(fn ($i) => [$i => ['score' => '1']])->all();

    ($this->save)($rows)->assertSessionHasErrors('rows');
});

it('ignores a grade, a tenant or any other field sent with the marks', function () {
    $a = ($this->place)([]);

    ($this->save)([$a->id => ['score' => '10', 'grade' => 'A', 'tenant_id' => $this->other->id, 'entered_by' => 999, 'exam_id' => 999]], ['tenant_id' => $this->other->id, 'grade' => 'A'])
        ->assertSessionHasNoErrors();

    $mark = ($this->marks)()->first();
    expect($mark->tenant_id)->toBe($this->school->id)
        ->and($mark->entered_by)->toBe($this->teacher->id)
        ->and($mark->exam_id)->toBe($this->exam->id)
        ->and(Schema::hasColumn('exam_marks', 'grade'))->toBeFalse();

    $this->actingAs($this->teacher)->get("{$this->url}/exams/{$this->exam->id}/marks?school_class_id={$this->w['form1']->id}&subject_id={$this->w['math']->id}")
        ->assertSee('<span class="fw-medium">F</span>', false);
});

it('works out the grade on the server from the exam scale, exactly at the boundaries', function () {
    $scores = ['100' => 'A', '80' => 'A', '79.99' => 'B', '70' => 'B', '69.99' => 'C', '60' => 'C', '59.99' => 'D', '50' => 'D', '49.99' => 'F', '0' => 'F'];
    $names = [];
    foreach ($scores as $score => $grade) {
        $names[$score] = ($this->place)(['first_name' => 'S'.str_replace('.', 'p', (string) $score), 'last_name' => 'Zz'])->id;
    }

    ($this->save)(collect($names)->map(fn ($id, $score) => [$id, ['score' => (string) $score]])->mapWithKeys(fn ($pair) => [$pair[0] => $pair[1]])->all())
        ->assertSessionHasNoErrors();

    $html = $this->actingAs($this->teacher)->get("{$this->url}/exams/{$this->exam->id}/marks?school_class_id={$this->w['form1']->id}&subject_id={$this->w['math']->id}")->assertOk()->getContent();

    foreach ($scores as $score => $grade) {
        $name = 'S'.str_replace('.', 'p', (string) $score).' Zz';
        $row = substr($html, strpos($html, $name));
        $row = substr($row, 0, strpos($row, '</tr>'));
        expect($row)->toMatch('/<span class="fw-medium">'.$grade.'<\/span>/');
    }
});

it('grades an exam out of fifty by percentage', function () {
    $exam = ($this->inSchool)(fn () => Exam::factory()->create([
        'name' => 'Quiz', 'academic_year_id' => $this->w['year']->id, 'term_id' => $this->w['term']->id,
        'grading_scale_id' => $this->w['scale']->id, 'max_mark' => 50,
    ]));
    $a = ($this->place)(['first_name' => 'Fortyone', 'last_name' => 'Zz']);
    $b = ($this->place)(['first_name' => 'Thirtynine', 'last_name' => 'Zz']);

    ($this->save)([$a->id => ['score' => '40'], $b->id => ['score' => '39.99']], [], null, $exam)->assertSessionHasNoErrors();

    $html = $this->actingAs($this->teacher)->get("{$this->url}/exams/{$exam->id}/marks?school_class_id={$this->w['form1']->id}&subject_id={$this->w['math']->id}")->getContent();
    $rowOf = fn (string $name) => substr(substr($html, strpos($html, $name)), 0, strpos(substr($html, strpos($html, $name)), '</tr>'));

    expect($rowOf('Fortyone'))->toContain('<span class="fw-medium">A</span>')
        ->and($rowOf('Thirtynine'))->toContain('<span class="fw-medium">B</span>');
});

it('keeps past grades the same after the scale is copied and the copy changed', function () {
    $a = ($this->place)(['first_name' => 'Keeper', 'last_name' => 'Zz']);
    ($this->save)([$a->id => ['score' => '85']]);

    $this->actingAs($this->owner)->post("{$this->url}/grading-scales/{$this->w['scale']->id}/copy");
    $copy = ($this->inSchool)(fn () => GradingScale::where('name', 'like', 'Standard (copy)%')->firstOrFail());
    $this->actingAs($this->owner)->put("{$this->url}/grading-scales/{$copy->id}", ['name' => 'Harder', 'bands' => [['min_mark' => '0', 'grade' => 'X'], ['min_mark' => '95', 'grade' => 'Y']]]);
    $this->actingAs($this->owner)->put("{$this->url}/grading-scales/{$this->w['scale']->id}", ['name' => 'Hacked', 'bands' => [['min_mark' => '0', 'grade' => 'Z']]])->assertSessionHasErrors('name');

    $html = $this->actingAs($this->teacher)->get("{$this->url}/exams/{$this->exam->id}/marks?school_class_id={$this->w['form1']->id}&subject_id={$this->w['math']->id}")->getContent();
    $row = substr($html, strpos($html, 'Keeper'));
    expect(substr($row, 0, strpos($row, '</tr>')))->toContain('<span class="fw-medium">A</span>');
});

it('keeps the class a mark was entered in when the student moves later', function () {
    $a = ($this->place)(['first_name' => 'Mover']);
    ($this->save)([$a->id => ['score' => '55']]);

    ($this->inSchool)(fn () => Enrollment::factory()->create([
        'student_id' => $a->id, 'academic_year_id' => $this->w['year']->id, 'school_class_id' => $this->w['form2']->id,
    ]));

    $mark = ($this->marks)()->first();
    expect($mark->school_class_id)->toBe($this->w['form1']->id)->and($mark->score)->toBe('55.00');

    $this->actingAs($this->teacher)->get("{$this->url}/exams/{$this->exam->id}/marks?school_class_id={$this->w['form1']->id}&subject_id={$this->w['math']->id}")
        ->assertDontSee('Mover');
});

it('keeps marks of different subjects and exams apart', function () {
    $a = ($this->place)([]);
    $other = ($this->inSchool)(fn () => Exam::factory()->create([
        'name' => 'Final', 'academic_year_id' => $this->w['year']->id, 'term_id' => $this->w['term']->id,
        'grading_scale_id' => $this->w['scale']->id, 'max_mark' => 100,
    ]));

    ($this->save)([$a->id => ['score' => '10']]);
    ($this->save)([$a->id => ['score' => '20']], ['subject_id' => $this->w['english']->id]);
    ($this->save)([$a->id => ['score' => '30']], [], null, $other);

    expect(($this->marks)()->pluck('score')->sort()->values()->all())->toBe(['10.00', '20.00', '30.00']);
});

it('does not accept marks for a locked exam and keeps what is stored', function () {
    $a = ($this->place)([]);
    ($this->save)([$a->id => ['score' => '40']]);
    $this->actingAs($this->owner)->post("{$this->url}/exams/{$this->exam->id}/lock");

    ($this->save)([$a->id => ['score' => '99']])->assertSessionHasErrors('exam');
    ($this->save)([$a->id => ['score' => '99']], [], $this->owner)->assertSessionHasErrors('exam');

    expect(($this->marks)()->first()->score)->toBe('40.00');

    $this->actingAs($this->teacher)->get("{$this->url}/exams/{$this->exam->id}/marks?school_class_id={$this->w['form1']->id}&subject_id={$this->w['math']->id}")
        ->assertOk()->assertSee(__('exams.errors.locked'))->assertDontSee(__('exams.marks.save'));
});

it('accepts marks again after the exam is unlocked', function () {
    $a = ($this->place)([]);
    $this->actingAs($this->owner)->post("{$this->url}/exams/{$this->exam->id}/lock");
    $this->actingAs($this->owner)->post("{$this->url}/exams/{$this->exam->id}/unlock");

    ($this->save)([$a->id => ['score' => '77']])->assertSessionHasNoErrors();

    expect(($this->marks)()->first()->score)->toBe('77.00');
});

it('lets teachers, head teachers and owners enter marks and nobody else', function (string $role, bool $allowed) {
    $a = ($this->place)([]);
    $user = userWithRole($this->school, $role);

    if ($role === 'teacher') {
        // A teacher writes only what the school assigned them.
        $teacherId = $user->id;
        $classId = $this->w['form1']->id;
        $subjectId = $this->w['math']->id;
        TenantContext::run($this->school, function () use ($teacherId, $classId, $subjectId) {
            $profile = Teacher::factory()->create(['user_id' => $teacherId]);
            $profile->classes()->attach($classId);
            $profile->subjects()->attach($subjectId);
        });
    }

    $page = $this->actingAs($user)->get("{$this->url}/exams/{$this->exam->id}/marks?school_class_id={$this->w['form1']->id}&subject_id={$this->w['math']->id}");
    $post = ($this->save)([$a->id => ['score' => '50']], [], $user);

    $allowed ? $page->assertOk() : $page->assertForbidden();
    $allowed ? $post->assertRedirect() : $post->assertForbidden();
    expect(($this->marks)())->toHaveCount($allowed ? 1 : 0);
})->with([
    'owner' => ['owner', true],
    'head teacher' => ['head_teacher', true],
    'teacher' => ['teacher', true],
    'accountant' => ['accountant', false],
    'parent' => ['parent_student', false],
]);

it('records that marks were looked at', function () {
    ($this->place)([]);

    $this->actingAs($this->teacher)->get("{$this->url}/exams/{$this->exam->id}/marks?school_class_id={$this->w['form1']->id}&subject_id={$this->w['math']->id}")->assertOk();

    expect(($this->inSchool)(fn () => AuditLog::where('action', 'marks.viewed')->where('subject_id', $this->exam->id)->where('user_id', $this->teacher->id)->count()))->toBe(1);
});

it('lets a read-only school look at marks but not save them', function () {
    $a = ($this->place)([]);
    ($this->save)([$a->id => ['score' => '30']]);
    $this->school->update(['subscription_status' => SubscriptionStatus::ReadOnly]);
    $teacher = userWithRole($this->school, 'teacher');
    $teacherId = $teacher->id;
    $classId = $this->w['form1']->id;
    $subjectId = $this->w['math']->id;
    TenantContext::run($this->school, function () use ($teacherId, $classId, $subjectId) {
        $profile = Teacher::factory()->create(['user_id' => $teacherId]);
        $profile->classes()->attach($classId);
        $profile->subjects()->attach($subjectId);
    });

    $this->actingAs($teacher)->get("{$this->url}/exams/{$this->exam->id}/marks?school_class_id={$this->w['form1']->id}&subject_id={$this->w['math']->id}")
        ->assertOk()->assertSee('value="30"', false)->assertDontSee(__('exams.marks.save'));
    ($this->save)([$a->id => ['score' => '99']], [], $teacher)->assertForbidden();

    expect(($this->marks)()->first()->score)->toBe('30.00');
});

it('shows nothing of another school on the sheet', function () {
    $foreign = makeExamWorld($this->other);
    $theirs = ($this->inSchool)(function () use ($foreign) {
        $student = Student::factory()->create(['first_name' => 'Zxqtheirs']);
        Enrollment::factory()->create(['student_id' => $student->id, 'academic_year_id' => $foreign['year']->id, 'school_class_id' => $foreign['form1']->id]);

        return $student;
    }, $this->other);

    $this->actingAs($this->teacher)->get("{$this->url}/exams/{$this->exam->id}/marks?school_class_id={$foreign['form1']->id}&subject_id={$this->w['math']->id}")
        ->assertSessionHasErrors('school_class_id');
    $this->actingAs($this->teacher)->get("{$this->url}/exams/{$this->exam->id}/marks?school_class_id={$this->w['form1']->id}&subject_id={$this->w['math']->id}")
        ->assertDontSee('Zxqtheirs');

    expect($theirs->id)->not->toBeNull();
});

it('escapes student names on the sheet', function () {
    ($this->place)(['first_name' => '<script>alert(1)</script>']);

    $this->actingAs($this->teacher)->get("{$this->url}/exams/{$this->exam->id}/marks?school_class_id={$this->w['form1']->id}&subject_id={$this->w['math']->id}")
        ->assertDontSee('<script>alert(1)</script>', false)->assertSee('&lt;script&gt;', false);
});

it('shows the sheet in Swahili when that language is chosen', function () {
    ($this->place)([]);

    $this->actingAs($this->teacher)->withSession(['locale' => 'sw'])
        ->get("{$this->url}/exams/{$this->exam->id}/marks?school_class_id={$this->w['form1']->id}&subject_id={$this->w['math']->id}")
        ->assertOk()->assertSee(__('exams.marks.save', [], 'sw'))->assertDontSee(__('exams.marks.save', [], 'en'));
});

it('sends a guest to sign in', function () {
    $this->get("{$this->url}/exams/{$this->exam->id}/marks")->assertRedirect("{$this->url}/login");
    $this->post("{$this->url}/exams/{$this->exam->id}/marks", [])->assertRedirect("{$this->url}/login");
});

it('refuses a subject or a class the school did not assign the teacher', function () {
    $a = ($this->place)([]);
    $extra = ($this->inSchool)(fn () => [
        'form3' => SchoolClass::factory()->create(['name' => 'Form 3', 'sort_order' => 3]),
        'kiswahili' => Subject::factory()->create(['name' => 'Kiswahili']),
    ]);

    ($this->save)([$a->id => ['score' => '55']], ['subject_id' => $extra['kiswahili']->id])->assertForbidden();
    ($this->save)([$a->id => ['score' => '55']], ['school_class_id' => $extra['form3']->id])->assertForbidden();

    expect(($this->marks)())->toHaveCount(0);
});

it('lets the owner enter marks for anything without a teaching assignment', function () {
    $a = ($this->place)([]);

    ($this->save)([$a->id => ['score' => '66']], [], $this->owner)->assertSessionHasNoErrors();

    expect(($this->marks)()->first()->score)->toBe('66.00');
});

it('lists only the assigned classes and subjects for a teacher on the marks page', function () {
    ($this->inSchool)(fn () => SchoolClass::factory()->create(['name' => 'Form 3', 'sort_order' => 3]));
    ($this->inSchool)(fn () => Subject::factory()->create(['name' => 'Agriculture']));

    $this->actingAs($this->teacher)->get("{$this->url}/exams/{$this->exam->id}/marks")
        ->assertOk()->assertSee('Form 1')->assertSee('Mathematics')->assertDontSee('Form 3')->assertDontSee('Agriculture');
});

it('shows a sheet a teacher does not teach without the save button', function () {
    $extra = ($this->inSchool)(fn () => SchoolClass::factory()->create(['name' => 'Form 3', 'sort_order' => 3]));
    $student = ($this->place)(['first_name' => 'Thirdrow'], $extra);

    $this->actingAs($this->teacher)->get("{$this->url}/exams/{$this->exam->id}/marks?school_class_id={$extra->id}&subject_id={$this->w['math']->id}")
        ->assertOk()->assertSee('Thirdrow')->assertDontSee(__('exams.marks.save'));
});
