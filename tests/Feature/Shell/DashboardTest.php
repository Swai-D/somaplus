<?php

use App\Domain\Fees\GenerateInvoices;
use App\Enums\SubscriptionStatus;
use App\Models\AttendanceRecord;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\FeeItem;
use App\Models\FeeStructure;
use App\Models\Guardian;
use App\Models\Invoice;
use App\Models\Student;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use App\Tenancy\TenantContext;
use Illuminate\Support\Str;

beforeEach(function () {
    useTestHosts();

    $plan = SubscriptionPlan::factory()->create(['key' => 'medium', 'name' => 'Medium Plan']);
    $this->school = Tenant::factory()->onPlan($plan)->create(['slug' => 'acme', 'name' => 'Acme Secondary']);
    $this->head = makeSchoolUser($this->school, ['name' => 'Asha Mwinyi'], 'head_teacher');
    $this->freshHead = fn () => TenantContext::runAsSystem(fn () => User::findOrFail($this->head->id));
});

it('shows a school its own name, plan, subscription and role', function () {
    $this->actingAs(($this->freshHead)())
        ->get('http://acme.somaplus.test/')
        ->assertOk()
        ->assertSee('Acme Secondary')
        ->assertSee('Medium Plan')
        ->assertSee(__('dashboard.status.active'))
        ->assertSee(__('roles.head_teacher'))
        ->assertSee(__('dashboard.welcome', ['name' => 'Asha Mwinyi']));
});

it('shows the end of the grace period', function () {
    $this->school->update([
        'subscription_status' => SubscriptionStatus::Grace,
        'grace_ends_at' => now()->addDays(10),
    ]);

    $this->actingAs(($this->freshHead)())
        ->get('http://acme.somaplus.test/')
        ->assertOk()
        ->assertSee(__('dashboard.status.grace'))
        ->assertSee(__('dashboard.grace_ends', ['date' => now()->addDays(10)->toFormattedDateString()]));
});

it('warns a read-only school and still lets it read', function () {
    $this->school->update(['subscription_status' => SubscriptionStatus::ReadOnly]);

    $this->actingAs(($this->freshHead)())
        ->get('http://acme.somaplus.test/')
        ->assertOk()
        ->assertSee(__('tenancy.read_only'))
        ->assertSee(__('dashboard.status.read_only'));
});

it('does not warn a school that is active', function () {
    $this->actingAs(($this->freshHead)())
        ->get('http://acme.somaplus.test/')
        ->assertDontSee(__('tenancy.read_only'));
});

it('shows the dashboard in Swahili when that language is chosen', function () {
    $this->actingAs(($this->freshHead)())
        ->withSession(['locale' => 'sw'])
        ->get('http://acme.somaplus.test/')
        ->assertOk()
        ->assertSee(__('dashboard.status.active', [], 'sw'))
        ->assertSee(__('roles.head_teacher', [], 'sw'))
        ->assertSee(__('menu.dashboard', [], 'sw'));
});

it('does not show one school anything of another school', function () {
    Tenant::factory()->create(['slug' => 'beta', 'name' => 'Beta Academy']);

    $this->actingAs(($this->freshHead)())
        ->get('http://acme.somaplus.test/')
        ->assertDontSee('Beta Academy');
});

it('shows the platform administrator the number of schools', function () {
    Tenant::factory()->count(2)->create();
    $admin = User::factory()->superAdmin()->create(['name' => 'Platform Root']);

    $this->actingAs($admin)
        ->get('http://somaplus.test/')
        ->assertOk()
        ->assertSee(__('dashboard.platform'))
        ->assertSee(__('dashboard.schools'))
        ->assertSee(__('layout.platform_admin'))
        ->assertSeeInOrder(['<h3 class="mb-0 text-white">', '3', '</h3>'], false);
});

it('shows a head teacher the real student, guardian, class and attendance figures', function () {
    $world = makeExamWorld($this->school);

    TenantContext::run($this->school, function () use ($world) {
        $present = Student::factory()->create(['first_name' => 'Zuhura', 'last_name' => 'Mwakyusa', 'admitted_on' => now()->toDateString()]);
        $absent = Student::factory()->create(['first_name' => 'Baraka', 'last_name' => 'Massawe', 'admitted_on' => '2024-02-01']);
        Student::factory()->create(['first_name' => 'Neema', 'last_name' => 'Kimaro', 'admitted_on' => '2024-01-01']);
        Student::factory()->left()->create(['first_name' => 'Gone', 'last_name' => 'Away']);
        Guardian::factory()->create();

        foreach ([[$present, AttendanceRecord::PRESENT], [$absent, AttendanceRecord::ABSENT]] as [$student, $status]) {
            AttendanceRecord::factory()->create([
                'student_id' => $student->id,
                'academic_year_id' => $world['year']->id,
                'school_class_id' => $world['form1']->id,
                'status' => $status,
            ]);
        }
    });

    $this->actingAs(($this->freshHead)())
        ->get('http://acme.somaplus.test/')
        ->assertOk()
        ->assertSee('<h4 class="text-white mb-1">3</h4>', false) // three active students (the one who left is not counted)
        ->assertSee(__('dashboard.new_this_month', ['count' => 1]))
        ->assertSee(__('dashboard.attendance_today'))
        ->assertSee('1 (50%)')
        ->assertSee('Zuhura Mwakyusa')
        ->assertSee('Baraka Massawe')
        ->assertDontSee('Gone Away')
        // A head teacher holds fees.view, so the money blocks are on the page as well.
        ->assertSee(__('dashboard.fees_collected'))
        ->assertSee('feesChart');
});

it('says so when no attendance was recorded today', function () {
    $this->actingAs(($this->freshHead)())
        ->get('http://acme.somaplus.test/')
        ->assertOk()
        ->assertSee(__('dashboard.attendance_none'));
});

it('shows only the blocks the person may see anywhere else in the application', function () {
    $teacher = userWithRole($this->school, 'teacher');
    $accountant = userWithRole($this->school, 'accountant');
    $parent = userWithRole($this->school, 'parent_student');
    makeExamWorld($this->school);

    // A teacher: students, classes and attendance, but no money.
    $this->actingAs($teacher)->get('http://acme.somaplus.test/')->assertOk()
        ->assertSee(__('dashboard.attendance_today'))
        ->assertSee(__('dashboard.new_admissions'))
        ->assertDontSee(__('dashboard.fees_chart'))
        ->assertDontSee(__('dashboard.recent_payments'));

    // An accountant: money and students, but no attendance register.
    $this->actingAs($accountant)->get('http://acme.somaplus.test/')->assertOk()
        ->assertSee(__('dashboard.fees_chart'))
        ->assertSee(__('dashboard.recent_payments'))
        ->assertSee('feesChart')
        ->assertDontSee(__('dashboard.attendance_today'));

    // A parent has no school dashboard: they are sent to their children, and none of those blocks is there.
    $this->actingAs($parent)->get('http://acme.somaplus.test/')->assertRedirect(route('family.index', [], false));
    $this->app['auth']->forgetGuards();
    $this->actingAs($parent)->get('http://acme.somaplus.test/family')->assertOk()
        ->assertDontSee(__('dashboard.new_admissions'))
        ->assertDontSee(__('dashboard.recent_payments'))
        ->assertDontSee(__('dashboard.fees_chart'))
        ->assertDontSee(__('dashboard.attendance_today'));
});

it('never shows the figures or names of another school', function () {
    $beta = Tenant::factory()->create(['slug' => 'beta']);
    TenantContext::run($beta, function () {
        Student::factory()->create(['first_name' => 'Hidden', 'last_name' => 'Betastudent']);
        Student::factory()->create();
    });
    TenantContext::run($this->school, fn () => Student::factory()->create(['first_name' => 'Visible', 'last_name' => 'Acmestudent']));

    $this->actingAs(($this->freshHead)())
        ->get('http://acme.somaplus.test/')
        ->assertOk()
        ->assertSee('<h4 class="text-white mb-1">1</h4>', false)
        ->assertSee('Visible Acmestudent')
        ->assertDontSee('Betastudent');
});

it('works out invoiced, collected and outstanding fees and leaves reversed payments out', function () {
    $world = makeExamWorld($this->school);
    $accountant = userWithRole($this->school, 'accountant');
    $owner = userWithRole($this->school, 'owner');

    // One invoice of TZS 100,000.00 for a student placed in Form 1.
    $invoice = TenantContext::run($this->school, function () use ($world) {
        $structure = FeeStructure::factory()->create([
            'academic_year_id' => $world['year']->id, 'term_id' => $world['term']->id, 'school_class_id' => $world['form1']->id,
        ]);
        $item = new FeeItem(['name' => 'Tuition', 'amount' => 10000000]);
        $item->fee_structure_id = $structure->id;
        $item->save();
        $student = Student::factory()->create(['first_name' => 'Payer']);
        Enrollment::factory()->create(['student_id' => $student->id, 'academic_year_id' => $world['year']->id, 'school_class_id' => $world['form1']->id]);
        app(GenerateInvoices::class)->handle($structure);

        return Invoice::query()->where('student_id', $student->id)->firstOrFail();
    });

    $this->actingAs($accountant)->post("http://acme.somaplus.test/fees/invoices/{$invoice->id}/payments", [
        'amount' => '40000', 'method' => 'cash', 'paid_on' => now()->toDateString(), 'idempotency_key' => (string) Str::uuid(),
    ])->assertSessionHasNoErrors();

    $this->actingAs($accountant)->get('http://acme.somaplus.test/')->assertOk()
        ->assertSee('<h4 class="text-white mb-1">TZS 100,000.00</h4>', false)
        ->assertSee('<h4 class="text-white mb-1">TZS 40,000.00</h4>', false)
        ->assertSee('<h4 class="text-white mb-1">TZS 60,000.00</h4>', false)
        ->assertSee('Payer');

    $payment = TenantContext::run($this->school, fn () => $invoice->payments()->firstOrFail());
    $this->actingAs($owner)->post("http://acme.somaplus.test/fees/payments/{$payment->id}/reverse", ['reason' => 'Entered twice'])
        ->assertSessionHasNoErrors();

    $this->actingAs($accountant)->get('http://acme.somaplus.test/')->assertOk()
        ->assertSee('<h4 class="text-white mb-1">TZS 0.00</h4>', false)
        ->assertSee('<h4 class="text-white mb-1">TZS 100,000.00</h4>', false)
        ->assertDontSee('<h4 class="text-white mb-1">TZS 40,000.00</h4>', false);
});

it('shows the fee labels in Swahili when that language is chosen', function () {
    makeExamWorld($this->school);
    $accountant = userWithRole($this->school, 'accountant');

    $this->actingAs($accountant)
        ->withSession(['locale' => 'sw'])
        ->get('http://acme.somaplus.test/')
        ->assertOk()
        ->assertSee(__('dashboard.fees_chart', [], 'sw'))
        ->assertSee(__('dashboard.recent_payments', [], 'sw'));
});

it('shows the people of the school and how many pupils each class holds', function () {
    $world = makeExamWorld($this->school);
    $head = userWithRole($this->school, 'head_teacher');

    TenantContext::run($this->school, function () use ($world) {
        $student = Student::factory()->create();
        Enrollment::factory()->create([
            'student_id' => $student->id,
            'academic_year_id' => $world['year']->id,
            'school_class_id' => $world['form1']->id,
        ]);
    });

    $this->actingAs($head)
        ->get('http://acme.somaplus.test/')
        ->assertOk()
        ->assertSee(__('dashboard.people'))
        ->assertSee(__('dashboard.teachers'))
        ->assertSee(__('dashboard.students_by_class'))
        ->assertSee(__('dashboard.attendance_trend'))
        ->assertSee($world['form1']->name);
});

it('shows the best pupils of the last locked exam', function () {
    $world = makeExamWorld($this->school);
    $head = userWithRole($this->school, 'head_teacher');

    TenantContext::run($this->school, function () use ($world) {
        $best = Student::factory()->create(['first_name' => 'Zuhura', 'last_name' => 'Mwakyusa']);
        $other = Student::factory()->create(['first_name' => 'Baraka', 'last_name' => 'Massawe']);

        $exam = Exam::factory()->create([
            'academic_year_id' => $world['year']->id,
            'term_id' => $world['term']->id,
            'grading_scale_id' => $world['scale']->id,
            'name' => 'Midterm',
            'locked_at' => now(),
        ]);

        foreach ([[$best, 91], [$other, 55]] as [$student, $score]) {
            $mark = new ExamMark;
            $mark->forceFill([
                'tenant_id' => $this->school->id,
                'exam_id' => $exam->id,
                'subject_id' => $world['math']->id,
                'student_id' => $student->id,
                'school_class_id' => $world['form1']->id,
                'score' => $score,
            ])->save();
        }
    });

    $this->actingAs($head)
        ->get('http://acme.somaplus.test/')
        ->assertOk()
        ->assertSee(__('dashboard.top_students'))
        ->assertSee('Midterm')
        ->assertSee('Zuhura Mwakyusa')
        ->assertSee(__('dashboard.position', ['position' => 1]));
});

it('does not show the people, the results or the register to a person who may not see them', function () {
    makeExamWorld($this->school);
    $parent = userWithRole($this->school, 'parent_student');

    $this->actingAs($parent)
        ->get('http://acme.somaplus.test/family')
        ->assertOk()
        ->assertDontSee(__('dashboard.people'))
        ->assertDontSee(__('dashboard.top_students'))
        ->assertDontSee(__('dashboard.students_by_class'))
        ->assertDontSee(__('dashboard.attendance_today'))
        ->assertDontSee(__('dashboard.attendance_trend'));
});
