<?php

use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\Student;
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
