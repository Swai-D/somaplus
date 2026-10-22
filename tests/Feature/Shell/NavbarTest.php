<?php

use App\Enums\SubscriptionStatus;
use App\Models\AcademicYear;
use App\Models\Exam;
use App\Models\FeeStructure;
use App\Models\GradingScale;
use App\Models\Invoice;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\Term;
use App\Models\User;
use App\Tenancy\TenantContext;
use Illuminate\Support\Str;

beforeEach(function () {
    useTestHosts();

    $plan = SubscriptionPlan::factory()->create(['key' => 'medium', 'name' => 'Medium Plan']);
    $this->school = Tenant::factory()->onPlan($plan)->create(['slug' => 'acme', 'name' => 'Acme Secondary']);
    $this->owner = userWithRole($this->school, 'owner');
    $this->head = userWithRole($this->school, 'head_teacher');
    $this->teacher = userWithRole($this->school, 'teacher');
    $this->accountant = userWithRole($this->school, 'accountant');
    $this->parent = userWithRole($this->school, 'parent_student');

    $this->world = TenantContext::run($this->school, function () {
        $year = AcademicYear::factory()->current()->create(['name' => '2026']);
        $term = Term::factory()->create(['academic_year_id' => $year->id, 'name' => 'Term 1']);
        $class = SchoolClass::factory()->create(['name' => 'Form 1', 'sort_order' => 1]);
        $scale = GradingScale::factory()->standard()->create(['name' => 'Standard']);

        return ['year' => $year, 'term' => $term, 'class' => $class, 'scale' => $scale];
    });
});

it('offers the quick "Add new" menu with the pages the person may open', function () {
    $html = $this->actingAs($this->owner)->get('http://acme.somaplus.test/')->assertOk()->getContent();

    expect($html)->toContain(route('students.create'))
        ->toContain(route('students.import'))
        ->toContain(route('attendance.index'))
        ->toContain(route('exams.create'))
        ->toContain(route('fees.payments.create'));
});

it('hides the quick actions the person may not use', function () {
    // A teacher may take the attendance but not add a student, an exam or a payment.
    $html = $this->actingAs($this->teacher)->get('http://acme.somaplus.test/')->assertOk()->getContent();

    expect($html)->toContain(route('attendance.index'))
        ->not->toContain(route('students.create'))
        ->not->toContain(route('exams.create'))
        ->not->toContain(route('fees.payments.create'))
        ->not->toContain(route('fees.structures.create'));
});

it('shows no quick menu to a parent or student account', function () {
    $html = $this->actingAs($this->parent)->get('http://acme.somaplus.test/help')->assertOk()->getContent();

    expect($html)->not->toContain('link-item')
        ->not->toContain(route('students.create'));
});

it('shows the help and the settings in the top bar', function () {
    $owner = $this->actingAs($this->owner)->get('http://acme.somaplus.test/')->assertOk()->getContent();
    $teacher = $this->actingAs($this->teacher)->get('http://acme.somaplus.test/')->assertOk()->getContent();

    expect($owner)->toContain(route('help.index'))
        ->toContain(route('settings.integrations.index'))
        ->and($teacher)->toContain(route('help.index'))
        ->not->toContain(route('settings.integrations.index'));
});

it('tells the school that its subscription is in the grace period', function () {
    $this->school->update(['subscription_status' => SubscriptionStatus::Grace, 'grace_ends_at' => now()->addDays(5)]);

    $html = $this->actingAs($this->head)->get('http://acme.somaplus.test/')->assertOk()->getContent();

    expect($html)->toContain(__('layout.alert.grace'))
        ->toContain(now()->addDays(5)->toFormattedDateString());
});

it('tells a read-only school that it may not change anything', function () {
    $this->school->update(['subscription_status' => SubscriptionStatus::ReadOnly]);

    $html = $this->actingAs($this->head)->get('http://acme.somaplus.test/')->assertOk()->getContent();

    expect($html)->toContain(__('layout.alert.read_only'));
});

it('reports the exams of the current year that are still open', function () {
    $html = $this->actingAs($this->head)->get('http://acme.somaplus.test/')->assertOk()->getContent();

    expect($html)->not->toContain(__('layout.alert.open_exams'));

    TenantContext::run($this->school, function () {
        Exam::factory()->create([
            'academic_year_id' => $this->world['year']->id,
            'term_id' => $this->world['term']->id,
            'grading_scale_id' => $this->world['scale']->id,
            'name' => 'Midterm',
        ]);
    });

    $html = $this->actingAs($this->head)->get('http://acme.somaplus.test/')->assertOk()->getContent();

    expect($html)->toContain(__('layout.alert.open_exams'));
});

it('reports the invoices that are not paid in full, to the people who may see them', function () {
    TenantContext::run($this->school, function () {
        $student = Student::factory()->create();
        $structure = FeeStructure::factory()->create([
            'academic_year_id' => $this->world['year']->id,
            'term_id' => $this->world['term']->id,
            'school_class_id' => $this->world['class']->id,
            'name' => 'Tuition',
        ]);

        $invoice = new Invoice;
        $invoice->forceFill([
            'tenant_id' => $this->school->id,
            'number' => 1,
            'student_id' => $student->id,
            'academic_year_id' => $this->world['year']->id,
            'term_id' => $this->world['term']->id,
            'fee_structure_id' => $structure->id,
            'school_class_id' => $this->world['class']->id,
            'currency' => 'TZS',
            'total' => 100000,
            'issued_on' => now()->toDateString(),
        ])->save();
    });

    $accountant = $this->actingAs($this->accountant)->get('http://acme.somaplus.test/')->assertOk()->getContent();
    $teacher = $this->actingAs($this->teacher)->get('http://acme.somaplus.test/')->assertOk()->getContent();

    expect($accountant)->toContain(__('layout.alert.unpaid_invoices'))
        ->and($teacher)->not->toContain(__('layout.alert.unpaid_invoices'));
});

it('shows the platform administrator no school notices', function () {
    $admin = User::factory()->superAdmin()->create(['name' => 'Platform Root']);

    $html = $this->actingAs($admin)->get('http://somaplus.test/')->assertOk()->getContent();

    expect($html)->not->toContain('notification-message')
        ->not->toContain('link-item');
});

it('leaves no template placeholders in the top bar', function () {
    $html = $this->actingAs($this->owner)->get('http://acme.somaplus.test/')->assertOk()->getContent();

    expect($html)->not->toContain('School Admin')
        ->not->toContain('Administrator')
        ->not->toContain('email.html')
        ->not->toContain('activities.html')
        ->not->toContain('profile.html');
});

it('searches the students from the top bar, with a POST so the name stays out of the address', function () {
    $html = $this->actingAs($this->teacher)->get('http://acme.somaplus.test/')->assertOk()->getContent();

    expect($html)->toContain('name="term"')
        ->toContain('action="'.route('students.search').'"')
        ->toContain('name="_token"')
        ->toContain(__('layout.search_hint'))
        ->toContain(route('help.show', ['category' => 'students', 'slug' => 'add-and-find']));

    // A parent or student account may not look at the students, so there is no search box.
    $html = $this->actingAs($this->parent)->get('http://acme.somaplus.test/help')->assertOk()->getContent();

    expect($html)->not->toContain('name="term"');
});

it('shows the person, the role and the school in the profile of the top bar', function () {
    $html = $this->actingAs($this->owner)->get('http://acme.somaplus.test/')->assertOk()->getContent();

    expect($html)->toContain($this->owner->name)
        ->toContain(__('roles.owner'))
        ->toContain('Acme Secondary')
        ->toContain(route('settings.integrations.index'))
        ->toContain(route('help.index'))
        ->toContain('action="'.route('logout').'"');
});

it('does not use the words of the buttons of the pages in the quick menu', function () {
    // The invoice page has a "Record payment" button; the quick menu says "New payment",
    // so a paid invoice shows no button of that name.
    $invoice = TenantContext::run($this->school, function () {
        $student = Student::factory()->create();
        $structure = FeeStructure::factory()->create([
            'academic_year_id' => $this->world['year']->id,
            'term_id' => $this->world['term']->id,
            'school_class_id' => $this->world['class']->id,
            'name' => 'Tuition',
        ]);

        $invoice = new Invoice;
        $invoice->forceFill([
            'tenant_id' => $this->school->id,
            'number' => 1,
            'student_id' => $student->id,
            'academic_year_id' => $this->world['year']->id,
            'term_id' => $this->world['term']->id,
            'fee_structure_id' => $structure->id,
            'school_class_id' => $this->world['class']->id,
            'currency' => 'TZS',
            'total' => 100000,
            'issued_on' => now()->toDateString(),
        ])->save();

        return $invoice;
    });

    $this->actingAs($this->accountant)
        ->post("http://acme.somaplus.test/fees/invoices/{$invoice->id}/payments", [
            'amount' => '1000', 'method' => 'cash', 'paid_on' => now()->toDateString(), 'idempotency_key' => (string) Str::uuid(),
        ])->assertSessionHasNoErrors();

    $html = $this->actingAs($this->accountant)
        ->get("http://acme.somaplus.test/fees/invoices/{$invoice->id}")->assertOk()->getContent();

    expect($html)->not->toContain(__('fees.payments.record_button'))
        ->toContain(route('fees.payments.create'));
});
