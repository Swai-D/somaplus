<?php

use App\Enums\SubscriptionStatus;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\Enrollment;
use App\Models\Guardian;
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
    $this->owner = userWithRole($this->school, 'owner');

    $this->inSchool = fn (Closure $work, ?Tenant $school = null) => TenantContext::run($school ?? $this->school, $work);

    [$this->year, $this->form1, $this->streamA, $this->form2] = ($this->inSchool)(function () {
        $year = AcademicYear::factory()->current()->create(['name' => '2026']);
        $form1 = SchoolClass::factory()->create(['name' => 'Form 1', 'sort_order' => 1]);
        $form2 = SchoolClass::factory()->create(['name' => 'Form 2', 'sort_order' => 2]);

        return [$year, $form1, Stream::factory()->create(['school_class_id' => $form1->id, 'name' => 'A']), $form2];
    });

    $this->valid = fn (array $override = []) => $override + [
        'admission_number' => 'A-001', 'first_name' => 'Amina', 'last_name' => 'Juma', 'gender' => 'female',
        'date_of_birth' => '2012-05-06', 'admitted_on' => '2026-01-10',
    ];

    $this->student = fn (array $attributes = [], ?Tenant $school = null) => ($this->inSchool)(
        fn () => Student::factory()->create($attributes), $school
    );
});

it('adds a student with a first placement and records it in the audit log', function () {
    $this->actingAs($this->owner)
        ->post("{$this->url}/students", ($this->valid)([
            'academic_year_id' => $this->year->id, 'school_class_id' => $this->form1->id, 'stream_id' => $this->streamA->id,
        ]))
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    ($this->inSchool)(function () {
        $student = Student::firstOrFail();
        expect($student->tenant_id)->toBe($this->school->id)
            ->and($student->status)->toBe('active')
            ->and($student->enrollments()->count())->toBe(1)
            ->and($student->enrollments()->first()->stream_id)->toBe($this->streamA->id)
            ->and(AuditLog::where('action', 'student.created')->where('subject_id', $student->id)->where('user_id', $this->owner->id)->exists())->toBeTrue();
    });
});

it('adds a student without a class', function () {
    $this->actingAs($this->owner)->post("{$this->url}/students", ($this->valid)())->assertSessionHasNoErrors();

    expect(($this->inSchool)(fn () => Enrollment::count()))->toBe(0);
});

it('refuses bad student data', function (array $override, string $field) {
    ($this->student)(['admission_number' => 'TAKEN']);

    $this->actingAs($this->owner)
        ->post("{$this->url}/students", ($this->valid)($override))
        ->assertSessionHasErrors($field);
})->with([
    'no admission number' => [['admission_number' => ''], 'admission_number'],
    'repeated admission number' => [['admission_number' => 'TAKEN'], 'admission_number'],
    'no first name' => [['first_name' => ''], 'first_name'],
    'bad gender' => [['gender' => 'x'], 'gender'],
    'birth in the future' => [['date_of_birth' => '2999-01-01'], 'date_of_birth'],
    'no date admitted' => [['admitted_on' => ''], 'admitted_on'],
    'class without a year' => [['school_class_id' => 1], 'academic_year_id'],
]);

it('allows the same admission number at another school', function () {
    ($this->student)(['admission_number' => 'A-001'], $this->other);

    $this->actingAs($this->owner)->post("{$this->url}/students", ($this->valid)())->assertSessionHasNoErrors();
});

it('refuses a stream that belongs to another class', function () {
    $this->actingAs($this->owner)
        ->post("{$this->url}/students", ($this->valid)([
            'academic_year_id' => $this->year->id, 'school_class_id' => $this->form2->id, 'stream_id' => $this->streamA->id,
        ]))
        ->assertSessionHasErrors('stream_id');
});

it('refuses a class, stream or year of another school', function () {
    [$year, $class, $stream] = ($this->inSchool)(function () {
        $class = SchoolClass::factory()->create();

        return [AcademicYear::factory()->create(), $class, Stream::factory()->create(['school_class_id' => $class->id])];
    }, $this->other);

    $this->actingAs($this->owner)
        ->post("{$this->url}/students", ($this->valid)(['academic_year_id' => $year->id, 'school_class_id' => $class->id, 'stream_id' => $stream->id]))
        ->assertSessionHasErrors(['academic_year_id', 'school_class_id', 'stream_id']);
});

it('edits a student', function () {
    $student = ($this->student)(['admission_number' => 'A-001']);

    $this->actingAs($this->owner)
        ->put("{$this->url}/students/{$student->id}", ($this->valid)(['first_name' => 'Zawadi']))
        ->assertSessionHasNoErrors();

    expect($student->refresh()->first_name)->toBe('Zawadi');
});

it('adds a placement for a promotion and keeps the earlier one', function () {
    $student = ($this->student)();
    $next = ($this->inSchool)(fn () => AcademicYear::factory()->create(['name' => '2027']));

    $this->actingAs($this->owner)->post("{$this->url}/students/{$student->id}/enrollments", [
        'academic_year_id' => $this->year->id, 'school_class_id' => $this->form1->id, 'enrolled_on' => '2026-01-10',
    ])->assertSessionHasNoErrors();
    $this->actingAs($this->owner)->post("{$this->url}/students/{$student->id}/enrollments", [
        'academic_year_id' => $next->id, 'school_class_id' => $this->form2->id, 'enrolled_on' => '2027-01-10',
    ])->assertSessionHasNoErrors();

    ($this->inSchool)(function () use ($student) {
        $rows = Enrollment::where('student_id', $student->id)->orderBy('id')->get();
        expect($rows)->toHaveCount(2)
            ->and($rows[0]->school_class_id)->toBe($this->form1->id)
            ->and($rows[1]->school_class_id)->toBe($this->form2->id);
    });
});

it('adds a move inside a year as a new row, and ignores the same request sent twice', function () {
    $student = ($this->student)();
    $move = fn (SchoolClass $class) => $this->actingAs($this->owner)->post("{$this->url}/students/{$student->id}/enrollments", [
        'academic_year_id' => $this->year->id, 'school_class_id' => $class->id, 'enrolled_on' => '2026-03-01',
    ])->assertSessionHasNoErrors();

    $move($this->form1);
    $move($this->form1);
    $move($this->form2);

    expect(($this->inSchool)(fn () => Enrollment::where('student_id', $student->id)->count()))->toBe(2);

    $this->actingAs($this->owner)->get("{$this->url}/students")->assertSee('Form 2');
});

it('does not place a student who has left school', function () {
    $student = ($this->student)([], null);
    ($this->inSchool)(fn () => $student->forceFill(['status' => 'left', 'left_on' => '2026-02-01'])->save());

    $this->actingAs($this->owner)->post("{$this->url}/students/{$student->id}/enrollments", [
        'academic_year_id' => $this->year->id, 'school_class_id' => $this->form1->id, 'enrolled_on' => '2026-03-01',
    ])->assertSessionHasErrors('school_class_id');
});

it('marks a student as left and brings the student back', function () {
    $student = ($this->student)();

    $this->actingAs($this->owner)->post("{$this->url}/students/{$student->id}/leave", ['left_on' => '2026-06-01', 'left_reason' => 'Moved'])
        ->assertSessionHasNoErrors();
    expect($student->refresh()->status)->toBe('left')->and($student->left_reason)->toBe('Moved');

    $this->actingAs($this->owner)->get("{$this->url}/students")->assertDontSee($student->first_name);
    $this->actingAs($this->owner)->get("{$this->url}/students?status=left")->assertSee($student->first_name);

    $this->actingAs($this->owner)->post("{$this->url}/students/{$student->id}/reinstate")->assertSessionHasNoErrors();
    expect($student->refresh()->status)->toBe('active')->and($student->left_on)->toBeNull();
});

it('shares one guardian between brothers and sisters by phone number', function () {
    $a = ($this->student)();
    $b = ($this->student)();
    $data = ['name' => 'Mama Amina', 'phone' => '0712 345 678', 'relationship' => 'mother', 'is_primary' => 1];

    $this->actingAs($this->owner)->post("{$this->url}/students/{$a->id}/guardians", $data)->assertSessionHasNoErrors();
    $this->actingAs($this->owner)->post("{$this->url}/students/{$b->id}/guardians", ['name' => 'Another name'] + $data)->assertSessionHasNoErrors();

    ($this->inSchool)(function () use ($a, $b) {
        expect(Guardian::count())->toBe(1)
            ->and(Guardian::first()->name)->toBe('Mama Amina')
            ->and(Guardian::first()->phone)->toBe('0712345678')
            ->and($a->guardians()->count())->toBe(1)
            ->and($b->guardians()->count())->toBe(1);
    });
});

it('keeps one main contact per student and removes a guardian nobody needs', function () {
    $student = ($this->student)();
    $post = fn (string $phone, int $primary) => $this->actingAs($this->owner)->post("{$this->url}/students/{$student->id}/guardians", [
        'name' => "G {$phone}", 'phone' => $phone, 'relationship' => 'guardian', 'is_primary' => $primary,
    ])->assertSessionHasNoErrors();

    $post('0711111111', 1);
    $post('0722222222', 1);

    ($this->inSchool)(function () use ($student) {
        expect($student->guardians()->wherePivot('is_primary', true)->count())->toBe(1)
            ->and($student->guardians()->wherePivot('is_primary', true)->first()->phone)->toBe('0722222222');
    });

    $guardian = ($this->inSchool)(fn () => Guardian::where('phone', '0711111111')->firstOrFail());
    $this->actingAs($this->owner)->delete("{$this->url}/students/{$student->id}/guardians/{$guardian->id}")->assertRedirect();

    expect(($this->inSchool)(fn () => Guardian::where('phone', '0711111111')->exists()))->toBeFalse();
});

it('refuses a bad guardian', function (array $override, string $field) {
    $student = ($this->student)();

    $this->actingAs($this->owner)
        ->post("{$this->url}/students/{$student->id}/guardians", $override + ['name' => 'X', 'phone' => '0712345678', 'relationship' => 'mother'])
        ->assertSessionHasErrors($field);
})->with([
    'no name' => [['name' => ''], 'name'],
    'bad phone' => [['phone' => 'abc'], 'phone'],
    'bad email' => [['email' => 'nope'], 'email'],
    'bad relationship' => [['relationship' => 'cat'], 'relationship'],
]);

it('does not remove a guardian that is not linked to the student', function () {
    $student = ($this->student)();
    $guardian = ($this->inSchool)(fn () => Guardian::factory()->create());

    $this->actingAs($this->owner)->delete("{$this->url}/students/{$student->id}/guardians/{$guardian->id}")->assertNotFound();
});

it('searches by name or admission number without the term in the address', function () {
    ($this->student)(['first_name' => 'Zawadi', 'last_name' => 'Mwangi', 'admission_number' => 'X-1']);
    ($this->student)(['first_name' => 'Baraka', 'last_name' => 'Otieno', 'admission_number' => 'Y-2']);

    $this->actingAs($this->owner)->post("{$this->url}/students/search", ['term' => 'zawa'])
        ->assertOk()->assertSee('Zawadi')->assertDontSee('Baraka');
    $this->actingAs($this->owner)->post("{$this->url}/students/search", ['term' => 'y-2'])
        ->assertOk()->assertSee('Baraka')->assertDontSee('Zawadi');
    $this->actingAs($this->owner)->post("{$this->url}/students/search", ['term' => 'a'])->assertSessionHasErrors('term');
    $this->actingAs($this->owner)->get("{$this->url}/students/search")->assertStatus(405);
});

it('filters the list by class for the current year', function () {
    $in = ($this->student)(['first_name' => 'Inform']);
    $out = ($this->student)(['first_name' => 'Outform']);
    ($this->inSchool)(function () use ($in, $out) {
        Enrollment::factory()->create(['student_id' => $in->id, 'academic_year_id' => $this->year->id, 'school_class_id' => $this->form1->id]);
        Enrollment::factory()->create(['student_id' => $out->id, 'academic_year_id' => $this->year->id, 'school_class_id' => $this->form2->id]);
    });

    $this->actingAs($this->owner)->get("{$this->url}/students?class={$this->form1->id}")
        ->assertSee('Inform')->assertDontSee('Outform');
});

it('shows a student and records that it was looked at', function () {
    $student = ($this->student)();

    $this->actingAs(userWithRole($this->school, 'teacher'))->get("{$this->url}/students/{$student->id}")
        ->assertOk()->assertSee($student->admission_number);

    expect(($this->inSchool)(fn () => AuditLog::where('action', 'student.viewed')->where('subject_id', $student->id)->count()))->toBe(1);
});

it('never puts a name or number in an address', function () {
    $student = ($this->student)();

    $this->actingAs($this->owner)->get("{$this->url}/students")
        ->assertDontSee('students/'.$student->admission_number, false)
        ->assertSee("/students/{$student->id}", false);
});

it('lets teachers and accountants look, and only the owner and head teacher change', function (string $role, bool $manage) {
    $student = ($this->student)();
    $user = userWithRole($this->school, $role);

    $this->actingAs($user)->get("{$this->url}/students")->assertOk();
    $this->actingAs($user)->get("{$this->url}/students/{$student->id}")->assertOk();

    $create = $this->actingAs($user)->get("{$this->url}/students/create");
    $post = $this->actingAs($user)->post("{$this->url}/students", ($this->valid)(['admission_number' => 'Z-9']));
    $manage ? $create->assertRedirect() : $create->assertForbidden();
    $manage ? $post->assertRedirect() : $post->assertForbidden();
})->with([
    'owner' => ['owner', true],
    'head teacher' => ['head_teacher', true],
    'teacher' => ['teacher', false],
    'accountant' => ['accountant', false],
]);

it('keeps students away from parents and students', function () {
    $user = userWithRole($this->school, 'parent_student');

    $this->actingAs($user)->get("{$this->url}/students")->assertForbidden();
});

it('hides a school from another school', function () {
    $theirs = ($this->student)(['first_name' => 'Zxqhidden'], $this->other);

    $this->actingAs($this->owner)->get("{$this->url}/students")->assertDontSee('Zxqhidden');
    $this->actingAs($this->owner)->get("{$this->url}/students/{$theirs->id}")->assertNotFound();
    $this->actingAs($this->owner)->put("{$this->url}/students/{$theirs->id}", ($this->valid)())->assertNotFound();
    $this->actingAs($this->owner)->post("{$this->url}/students/{$theirs->id}/leave", ['left_on' => '2026-01-01'])->assertNotFound();
    $this->actingAs($this->owner)->post("{$this->url}/students/search", ['term' => 'zxq'])->assertDontSee('Zxqhidden');
});

it('lets a read-only school look but not change', function () {
    $student = ($this->student)();
    $this->school->update(['subscription_status' => SubscriptionStatus::ReadOnly]);
    $owner = userWithRole($this->school, 'owner');

    $this->actingAs($owner)->get("{$this->url}/students")->assertOk();
    $this->actingAs($owner)->get("{$this->url}/students/{$student->id}")->assertOk();
    $this->actingAs($owner)->post("{$this->url}/students", ($this->valid)())->assertForbidden();
    $this->actingAs($owner)->post("{$this->url}/students/{$student->id}/leave", ['left_on' => '2026-01-01'])->assertForbidden();
});

it('sends a guest to sign in', function () {
    $this->get("{$this->url}/students")->assertRedirect("{$this->url}/login");
});

it('gives the central address no students', function () {
    $this->actingAs(User::factory()->superAdmin()->create())->get('http://somaplus.test/students')->assertForbidden();
});

it('keeps a year, a class and a stream that have students', function () {
    $student = ($this->student)();
    ($this->inSchool)(fn () => Enrollment::factory()->create([
        'student_id' => $student->id, 'academic_year_id' => $this->year->id,
        'school_class_id' => $this->form1->id, 'stream_id' => $this->streamA->id,
    ]));

    $this->actingAs($this->owner)->delete("{$this->url}/academics/streams/{$this->streamA->id}")
        ->assertSessionHasErrors(['delete' => __('academics.streams.has_students')]);
    $this->actingAs($this->owner)->delete("{$this->url}/academics/classes/{$this->form2->id}")->assertSessionHasNoErrors();

    $this->actingAs($this->owner)->delete("{$this->url}/academics/years/{$this->year->id}")
        ->assertSessionHasErrors(['delete' => __('academics.years.has_students')]);

    expect(($this->inSchool)(fn () => [Stream::count(), AcademicYear::count()]))->toBe([1, 1]);
});

it('keeps a class that has students', function () {
    $student = ($this->student)();
    ($this->inSchool)(fn () => Enrollment::factory()->create([
        'student_id' => $student->id, 'academic_year_id' => $this->year->id, 'school_class_id' => $this->form2->id,
    ]));

    $this->actingAs($this->owner)->delete("{$this->url}/academics/classes/{$this->form2->id}")
        ->assertSessionHasErrors(['delete' => __('academics.classes.has_students')]);
});

it('shows Students in the sidebar only to people who may look', function () {
    $this->actingAs(userWithRole($this->school, 'teacher'))->get($this->url)
        ->assertSee('/students"', false)
        ->assertDontSee('/students/create"', false);
    $this->actingAs($this->owner)->get($this->url)->assertSee('/students/create"', false);
    $this->actingAs(userWithRole($this->school, 'parent_student'))->get($this->url)->assertDontSee('/students"', false);
});

it('shows the pages in Swahili when that language is chosen', function () {
    $student = ($this->student)();

    $this->actingAs($this->owner)->withSession(['locale' => 'sw'])->get("{$this->url}/students/{$student->id}")
        ->assertOk()
        ->assertSee(__('students.show.guardians', [], 'sw'))
        ->assertDontSee(__('students.show.guardians', [], 'en'));
});

it('has every students text in English and Swahili', function () {
    $keys = fn (string $locale) => array_keys(Arr::dot(require base_path("lang/{$locale}/students.php")));

    expect($keys('sw'))->toBe($keys('en'));
});
