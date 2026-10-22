<?php

use App\Models\Book;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\LibraryMember;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Tenant;
use App\Models\User;
use App\Tenancy\TenantContext;

beforeEach(function () {
    useTestHosts();

    $this->school = Tenant::factory()->create(['slug' => 'acme', 'name' => 'Acme Secondary']);
    $this->url = 'http://acme.somaplus.test';
    $this->teacher = userWithRole($this->school, 'teacher');
    $this->w = makeExamWorld($this->school);
});

/** An exam of the current year with one mark entered by the given user and one by somebody else. */
function examWithMarks(Tenant $school, array $world, User $teacher, string $name = 'Midterm', bool $locked = false): Exam
{
    return TenantContext::run($school, function () use ($world, $teacher, $name, $locked) {
        $exam = Exam::factory()->create([
            'academic_year_id' => $world['year']->id, 'term_id' => $world['term']->id,
            'grading_scale_id' => $world['scale']->id, 'name' => $name,
        ]);
        if ($locked) {
            $exam->forceFill(['locked_at' => now()])->save();
        }

        foreach ([$teacher->id, null] as $enteredBy) {
            ExamMark::unguarded(fn () => ExamMark::query()->create([
                'exam_id' => $exam->id, 'subject_id' => $world['math']->id, 'student_id' => Student::factory()->create()->id,
                'school_class_id' => $world['form1']->id, 'score' => '55.00', 'is_absent' => false, 'entered_by' => $enteredBy,
            ]));
        }

        return $exam;
    });
}

it('shows a teacher the school counts, their own marks and the exams of the year', function () {
    examWithMarks($this->school, $this->w, $this->teacher, 'Midterm');
    examWithMarks($this->school, $this->w, $this->teacher, 'Final', locked: true);

    $this->actingAs($this->teacher)
        ->get("{$this->url}/dashboard/teacher")
        ->assertOk()
        ->assertSee(__('dashboard.teacher.title'))
        ->assertSee('Acme Secondary')
        ->assertSee('Midterm')
        ->assertSee('Final')
        ->assertSee('<h4 class="text-white mb-0">2</h4>', false) // two marks entered by this teacher (the other two are not theirs)
        ->assertSee('<h4 class="text-white mb-0">1</h4>', false) // one exam still open
        ->assertSee(__('dashboard.teacher.enter_marks'))
        ->assertSee(__('dashboard.teacher.results'))
        ->assertSee(__('dashboard.attendance_today'));
});

it('shows what the school assigned the teacher under Teachers', function () {
    $name = $this->teacher->id;
    TenantContext::run($this->school, function () use ($name) {
        $profile = Teacher::factory()->create(['user_id' => $name]);
        $profile->classes()->attach($this->w['form1']->id);
        $profile->subjects()->attach($this->w['math']->id);
    });

    $this->actingAs($this->teacher)
        ->get("{$this->url}/dashboard/teacher")
        ->assertOk()
        ->assertSee(__('dashboard.teacher.teaches'))
        ->assertSee('Mathematics')
        ->assertSee('Form 1');
});

it('says nothing is assigned when the person holds no teacher record', function () {
    $this->actingAs($this->teacher)
        ->get("{$this->url}/dashboard/teacher")
        ->assertOk()
        ->assertDontSee(__('dashboard.teacher.teaches'));
});

it('shows how the library stands to a person who may see it', function () {
    TenantContext::run($this->school, function () {
        $book1 = Book::factory()->create(['quantity' => 3]);
        $book2 = Book::factory()->create(['name' => 'Pili', 'quantity' => 2]);
        $student = Student::factory()->create();
        $member = LibraryMember::factory()->create(['student_id' => $student->id]);
        // One copy whose return day has passed and one still within it.
        $book1->issues()->create(['member_id' => $member->id, 'issued_on' => '2026-02-01', 'due_on' => now()->subDay()->toDateString()]);
        $book2->issues()->create(['member_id' => $member->id, 'issued_on' => '2026-02-02', 'due_on' => now()->addDay()->toDateString()]);
    });

    $this->actingAs($this->teacher)
        ->get("{$this->url}/dashboard/teacher")
        ->assertOk()
        ->assertSee(__('dashboard.teacher.library'))
        ->assertSee('<h6 class="fw-bold mb-0">3</h6>', false) // two copies out of five on the shelf
        ->assertSee('<h6 class="fw-bold mb-0">2</h6>', false) // two copies out
        ->assertSee('<h6 class="fw-bold mb-0 text-danger">1</h6>', false) // one overdue
        ->assertSee(__('dashboard.teacher.library_open_issues'));
});

it('leaves the library block away from a person who may not see it', function () {
    $marksOnly = makeSchoolUser($this->school);
    TenantContext::run($this->school, fn () => $marksOnly->givePermissionTo('marks.enter'));
    $marksOnly = TenantContext::runAsSystem(fn () => User::findOrFail($marksOnly->id));

    $this->actingAs($marksOnly)->get("{$this->url}/dashboard/teacher")->assertOk()
        ->assertDontSee(__('dashboard.teacher.library'));
});

it('returns the library figures even when the library is empty', function () {
    $this->actingAs($this->teacher)
        ->get("{$this->url}/dashboard/teacher")
        ->assertOk()
        ->assertSee(__('dashboard.teacher.library_copies', ['copies' => '0']));
});

it('says so when the year has no exams', function () {
    $this->actingAs($this->teacher)
        ->get("{$this->url}/dashboard/teacher")
        ->assertOk()
        ->assertSee(__('dashboard.teacher.no_exams'));
});

it('is only for people who may enter marks', function () {
    $accountant = userWithRole($this->school, 'accountant');
    $parent = userWithRole($this->school, 'parent_student');

    $this->actingAs($accountant)->get("{$this->url}/dashboard/teacher")->assertForbidden();
    $this->actingAs($parent)->get("{$this->url}/dashboard/teacher")->assertForbidden();
    // actingAs keeps the last user signed in for the rest of the test; a guest is someone nobody is signed in as.
    $this->app['auth']->forgetGuards();
    $this->get("{$this->url}/dashboard/teacher")->assertRedirect();
});

it('is not available on the central address', function () {
    $admin = User::factory()->superAdmin()->create();

    expect($this->actingAs($admin)->get('http://somaplus.test/dashboard/teacher')->status())->toBeIn([403, 404]);
});

it('shows the Dashboard submenu with School for everybody and Teacher only to those who enter marks', function () {
    $accountant = userWithRole($this->school, 'accountant');

    $this->actingAs($this->teacher)->get("{$this->url}/")->assertOk()
        ->assertSee(__('menu.dashboard_school'))
        ->assertSee(__('menu.dashboard_teacher'))
        ->assertSee("{$this->url}/dashboard/teacher", false);

    $this->actingAs($accountant)->get("{$this->url}/")->assertOk()
        ->assertSee(__('menu.dashboard_school'))
        ->assertDontSee("{$this->url}/dashboard/teacher", false);
});

it('shows the teacher dashboard in Swahili when that language is chosen', function () {
    $this->actingAs($this->teacher)
        ->withSession(['locale' => 'sw'])
        ->get("{$this->url}/dashboard/teacher")
        ->assertOk()
        ->assertSee(__('dashboard.teacher.title', [], 'sw'))
        ->assertSee(__('dashboard.teacher.no_exams', [], 'sw'));
});
