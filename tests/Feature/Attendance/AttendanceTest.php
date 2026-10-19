<?php

use App\Enums\SubscriptionStatus;
use App\Models\AcademicYear;
use App\Models\AttendanceRecord;
use App\Models\AuditLog;
use App\Models\Enrollment;
use App\Models\SchoolClass;
use App\Models\Stream;
use App\Models\Student;
use App\Models\Tenant;
use App\Models\User;
use App\Tenancy\TenantContext;
use Illuminate\Support\Arr;

beforeEach(function () {
    useTestHosts();

    $this->school = Tenant::factory()->create(['slug' => 'acme']);
    $this->other = Tenant::factory()->create(['slug' => 'beta']);
    $this->url = 'http://acme.somaplus.test';
    $this->teacher = userWithRole($this->school, 'teacher');
    $this->today = now()->toDateString();

    $this->inSchool = fn (Closure $work, ?Tenant $school = null) => TenantContext::run($school ?? $this->school, $work);

    [$this->year, $this->form1, $this->form2, $this->streamA, $this->streamB] = ($this->inSchool)(function () {
        $year = AcademicYear::factory()->current()->create([
            'name' => 'Now', 'starts_on' => now()->subYear()->toDateString(), 'ends_on' => now()->addYear()->toDateString(),
        ]);
        $form1 = SchoolClass::factory()->create(['name' => 'Form 1']);
        $form2 = SchoolClass::factory()->create(['name' => 'Form 2']);

        return [
            $year, $form1, $form2,
            Stream::factory()->create(['school_class_id' => $form1->id, 'name' => 'A']),
            Stream::factory()->create(['school_class_id' => $form1->id, 'name' => 'B']),
        ];
    });

    $this->place = fn (array $attributes, SchoolClass $class, ?Stream $stream = null, ?AcademicYear $year = null) => ($this->inSchool)(function () use ($attributes, $class, $stream, $year) {
        $student = Student::factory()->create($attributes);
        Enrollment::factory()->create([
            'student_id' => $student->id, 'academic_year_id' => ($year ?? $this->year)->id,
            'school_class_id' => $class->id, 'stream_id' => $stream?->id,
        ]);

        return $student;
    });

    $this->save = fn (array $payload, ?User $user = null) => $this->actingAs($user ?? $this->teacher)
        ->post("{$this->url}/attendance", $payload);
});

it('shows the register of a class with the students placed in it', function () {
    ($this->place)(['first_name' => 'Inone'], $this->form1);
    ($this->place)(['first_name' => 'Intwo'], $this->form2);

    $this->actingAs($this->teacher)->get("{$this->url}/attendance?school_class_id={$this->form1->id}&date={$this->today}")
        ->assertOk()->assertSee('Inone')->assertDontSee('Intwo');
});

it('shows only the stream asked for, and not students who left or were moved', function () {
    ($this->place)(['first_name' => 'Ina'], $this->form1, $this->streamA);
    ($this->place)(['first_name' => 'Inb'], $this->form1, $this->streamB);
    $left = ($this->place)(['first_name' => 'Gone'], $this->form1, $this->streamA);
    ($this->inSchool)(fn () => $left->forceFill(['status' => 'left'])->save());
    $moved = ($this->place)(['first_name' => 'Moved'], $this->form1, $this->streamA);
    ($this->inSchool)(fn () => Enrollment::factory()->create([
        'student_id' => $moved->id, 'academic_year_id' => $this->year->id, 'school_class_id' => $this->form2->id,
    ]));

    $this->actingAs($this->teacher)
        ->get("{$this->url}/attendance?school_class_id={$this->form1->id}&stream_id={$this->streamA->id}&date={$this->today}")
        ->assertOk()->assertSee('Ina')->assertDontSee('Inb')->assertDontSee('Gone')->assertDontSee('Moved');
});

it('records the register and keeps the placement of that day', function () {
    $a = ($this->place)([], $this->form1, $this->streamA);
    $b = ($this->place)([], $this->form1, $this->streamA);

    ($this->save)([
        'school_class_id' => $this->form1->id, 'stream_id' => $this->streamA->id, 'date' => $this->today,
        'rows' => [$a->id => ['status' => 'present'], $b->id => ['status' => 'absent', 'note' => 'Sick']],
    ])->assertSessionHasNoErrors()->assertRedirect();

    ($this->inSchool)(function () {
        $rows = AttendanceRecord::orderBy('student_id')->get();
        expect($rows)->toHaveCount(2)
            ->and($rows[0]->tenant_id)->toBe($this->school->id)
            ->and($rows[1]->status)->toBe('absent')->and($rows[1]->note)->toBe('Sick')
            ->and($rows[1]->school_class_id)->toBe($this->form1->id)
            ->and($rows[1]->stream_id)->toBe($this->streamA->id)
            ->and($rows[1]->academic_year_id)->toBe($this->year->id)
            ->and($rows[1]->recorded_by)->toBe($this->teacher->id)
            ->and(AuditLog::where('action', 'attendance.recorded')->where('subject_id', $this->form1->id)->exists())->toBeTrue();
    });
});

it('corrects a register sent again without duplicates', function () {
    $a = ($this->place)([], $this->form1);
    $payload = fn (string $status) => [
        'school_class_id' => $this->form1->id, 'date' => $this->today, 'rows' => [$a->id => ['status' => $status]],
    ];

    ($this->save)($payload('absent'))->assertSessionHasNoErrors();
    ($this->save)($payload('absent'))->assertSessionHasNoErrors();
    ($this->save)($payload('late'))->assertSessionHasNoErrors();

    ($this->inSchool)(function () {
        expect(AttendanceRecord::count())->toBe(1)->and(AttendanceRecord::first()->status)->toBe('late');
    });

    $this->actingAs($this->teacher)->get("{$this->url}/attendance?school_class_id={$this->form1->id}&date={$this->today}")
        ->assertSee('value="late" checked', false);
});

it('refuses a bad register', function (array $override, string $field) {
    $a = ($this->place)([], $this->form1);

    ($this->save)($override + [
        'school_class_id' => $this->form1->id, 'date' => $this->today, 'rows' => [$a->id => ['status' => 'present']],
    ])->assertSessionHasErrors($field);
})->with([
    'no class' => [['school_class_id' => ''], 'school_class_id'],
    'no date' => [['date' => ''], 'date'],
    'future date' => [['date' => '2999-01-01'], 'date'],
    'no rows' => [['rows' => []], 'rows'],
    'bad status' => [['rows' => [1 => ['status' => 'sleeping']]], 'rows.1.status'],
    'date outside every year' => [['date' => '2000-01-01'], 'date'],
]);

it('refuses a student who is not in the class that day', function () {
    $mine = ($this->place)([], $this->form1);
    $elsewhere = ($this->place)([], $this->form2);

    ($this->save)([
        'school_class_id' => $this->form1->id, 'date' => $this->today,
        'rows' => [$mine->id => ['status' => 'present'], $elsewhere->id => ['status' => 'present']],
    ])->assertSessionHasErrors('rows');

    expect(($this->inSchool)(fn () => AttendanceRecord::count()))->toBe(0);
});

it('refuses a stream of another class, and a class of another school', function () {
    $foreign = ($this->inSchool)(fn () => SchoolClass::factory()->create(), $this->other);

    ($this->save)(['school_class_id' => $this->form2->id, 'stream_id' => $this->streamA->id, 'date' => $this->today, 'rows' => [1 => ['status' => 'present']]])
        ->assertSessionHasErrors('stream_id');
    ($this->save)(['school_class_id' => $foreign->id, 'date' => $this->today, 'rows' => [1 => ['status' => 'present']]])
        ->assertSessionHasErrors('school_class_id');
});

it('does not mark a student of another school', function () {
    $foreignStudent = ($this->inSchool)(fn () => Student::factory()->create(), $this->other);

    ($this->save)([
        'school_class_id' => $this->form1->id, 'date' => $this->today, 'rows' => [$foreignStudent->id => ['status' => 'present']],
    ])->assertSessionHasErrors('rows');
});

it('summarises each student between two dates', function () {
    $a = ($this->place)(['first_name' => 'Sumone'], $this->form1);
    ($this->inSchool)(function () use ($a) {
        foreach (['2026-03-02' => 'present', '2026-03-03' => 'present', '2026-03-04' => 'late', '2026-03-05' => 'absent', '2026-03-06' => 'excused', '2026-04-01' => 'absent'] as $day => $status) {
            AttendanceRecord::factory()->create([
                'student_id' => $a->id, 'academic_year_id' => $this->year->id, 'school_class_id' => $this->form1->id,
                'attended_on' => $day, 'status' => $status,
            ]);
        }
    });

    $this->actingAs($this->teacher)
        ->get("{$this->url}/attendance/summary?school_class_id={$this->form1->id}&from=2026-03-01&to=2026-03-31")
        ->assertOk()
        ->assertSee('Sumone')
        ->assertSee('75%');
});

it('says so when nothing was recorded', function () {
    $this->actingAs($this->teacher)
        ->get("{$this->url}/attendance/summary?school_class_id={$this->form1->id}&from=2026-03-01&to=2026-03-31")
        ->assertOk()->assertSee(__('attendance.summary_empty'));
});

it('refuses a summary that ends before it starts', function () {
    $this->actingAs($this->teacher)
        ->get("{$this->url}/attendance/summary?school_class_id={$this->form1->id}&from=2026-03-10&to=2026-03-01")
        ->assertSessionHasErrors('to');
});

it('shows nothing of another school', function () {
    $theirYear = ($this->inSchool)(fn () => AcademicYear::factory()->create([
        'name' => 'T', 'starts_on' => now()->subYear()->toDateString(), 'ends_on' => now()->addYear()->toDateString(),
    ]), $this->other);
    $theirClass = ($this->inSchool)(fn () => SchoolClass::factory()->create(['name' => 'Beta class']), $this->other);
    $theirs = ($this->place)(['first_name' => 'Zxqtheirs'], $theirClass, null, $theirYear);
    ($this->inSchool)(fn () => AttendanceRecord::factory()->create([
        'student_id' => $theirs->id, 'academic_year_id' => $theirYear->id, 'school_class_id' => $theirClass->id,
    ]), $this->other);

    $this->actingAs($this->teacher)->get("{$this->url}/attendance?school_class_id={$theirClass->id}")->assertSessionHasErrors('school_class_id');
    $this->actingAs($this->teacher)->get("{$this->url}/attendance/summary?school_class_id={$theirClass->id}")->assertSessionHasErrors('school_class_id');
});

it('lets teachers, head teachers and owners take attendance, and nobody else', function (string $role, bool $allowed) {
    $user = userWithRole($this->school, $role);

    $page = $this->actingAs($user)->get("{$this->url}/attendance");
    $summary = $this->actingAs($user)->get("{$this->url}/attendance/summary");
    $allowed ? $page->assertOk() : $page->assertForbidden();
    $allowed ? $summary->assertOk() : $summary->assertForbidden();

    $a = ($this->place)([], $this->form1);
    $post = ($this->save)(['school_class_id' => $this->form1->id, 'date' => $this->today, 'rows' => [$a->id => ['status' => 'present']]], $user);
    $allowed ? $post->assertRedirect() : $post->assertForbidden();
})->with([
    'owner' => ['owner', true],
    'head teacher' => ['head_teacher', true],
    'teacher' => ['teacher', true],
    'accountant' => ['accountant', false],
    'parent' => ['parent_student', false],
]);

it('lets a read-only school look but not save', function () {
    $a = ($this->place)([], $this->form1);
    $this->school->update(['subscription_status' => SubscriptionStatus::ReadOnly]);
    $teacher = userWithRole($this->school, 'teacher');

    $this->actingAs($teacher)->get("{$this->url}/attendance?school_class_id={$this->form1->id}")->assertOk();
    ($this->save)(['school_class_id' => $this->form1->id, 'date' => $this->today, 'rows' => [$a->id => ['status' => 'present']]], $teacher)->assertForbidden();
});

it('sends a guest to sign in', function () {
    $this->get("{$this->url}/attendance")->assertRedirect("{$this->url}/login");
});

it('gives the central address no attendance', function () {
    $this->actingAs(User::factory()->superAdmin()->create())->get('http://somaplus.test/attendance')->assertForbidden();
});

it('shows Attendance in the sidebar only to people who may use it', function () {
    $this->actingAs($this->teacher)->get($this->url)->assertSee('/attendance"', false)->assertSee('/attendance/summary"', false);
    $this->actingAs(userWithRole($this->school, 'accountant'))->get($this->url)->assertDontSee('/attendance"', false);
});

it('shows the page in Swahili when that language is chosen', function () {
    $this->actingAs($this->teacher)->withSession(['locale' => 'sw'])->get("{$this->url}/attendance")
        ->assertOk()
        ->assertSee(__('attendance.intro', [], 'sw'))
        ->assertDontSee(__('attendance.intro', [], 'en'));
});

it('has every attendance text in English and Swahili', function () {
    $keys = fn (string $locale) => array_keys(Arr::dot(require base_path("lang/{$locale}/attendance.php")));

    expect($keys('sw'))->toBe($keys('en'));
});
