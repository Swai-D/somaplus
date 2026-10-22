<?php

use App\Models\AcademicYear;
use App\Models\GradingScale;
use App\Models\SchoolClass;
use App\Models\Stream;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\Term;
use App\Tenancy\TenantContext;

/**
 * The forms of the list pages are pop-ups, as in the template: the page is not loaded
 * again, and a form that was refused opens its pop-up again.
 */
beforeEach(function () {
    useTestHosts();

    $plan = SubscriptionPlan::factory()->create(['key' => 'medium', 'name' => 'Medium Plan']);
    $this->school = Tenant::factory()->onPlan($plan)->create(['slug' => 'acme', 'name' => 'Acme Secondary']);
    $this->owner = userWithRole($this->school, 'owner');

    TenantContext::run($this->school, function () {
        $year = AcademicYear::factory()->current()->create(['name' => '2026']);
        Term::factory()->create(['academic_year_id' => $year->id, 'name' => 'Term 1']);
        $class = SchoolClass::factory()->create(['name' => 'Form 1', 'sort_order' => 1]);
        Stream::factory()->create(['school_class_id' => $class->id, 'name' => 'A']);
        GradingScale::factory()->standard()->create(['name' => 'Standard']);
    });
});

it('shows the form of a list page in a pop-up, with the button that opens it', function (string $path, string $modal, string $action) {
    $html = $this->actingAs($this->owner)->get("http://acme.somaplus.test{$path}")->assertOk()->getContent();

    expect($html)->toContain('id="'.$modal.'"')
        ->toContain('data-bs-target="#'.$modal.'"')
        ->toContain('action="'.route($action).'"')
        ->toContain('name="form" type="hidden" value="'.$modal.'"');
})->with([
    'students' => ['/students', 'student-modal', 'students.store'],
    'classes' => ['/academics/classes', 'class-modal', 'academics.classes.store'],
    'subjects' => ['/academics/subjects', 'subject-modal', 'academics.subjects.store'],
    'academic years' => ['/academics/years', 'year-modal', 'academics.years.store'],
    'exams' => ['/exams', 'exam-modal', 'exams.store'],
    'grading scales' => ['/grading-scales', 'scale-modal', 'grading-scales.store'],
    'fee structures' => ['/fees/structures', 'structure-modal', 'fees.structures.store'],
    'payments' => ['/fees/payments', 'payment-modal', 'fees.payments.store'],
    'users' => ['/settings/users', 'user-modal', 'settings.users.store'],
]);

it('opens the list with the pop-up ready instead of a page of its own', function (string $path, string $list, string $modal) {
    $this->actingAs($this->owner)->get("http://acme.somaplus.test{$path}")
        ->assertRedirect("http://acme.somaplus.test{$list}?add={$modal}");
})->with([
    'a student' => ['/students/create', '/students', 'student-modal'],
    'an exam' => ['/exams/create', '/exams', 'exam-modal'],
    'a grading scale' => ['/grading-scales/create', '/grading-scales', 'scale-modal'],
    'a fee structure' => ['/fees/structures/create', '/fees/structures', 'structure-modal'],
    'a payment' => ['/fees/payments/create', '/fees/payments', 'payment-modal'],
    'a person' => ['/settings/users/create', '/settings/users', 'user-modal'],
]);

it('opens the pop-up of a form that was refused', function () {
    $this->actingAs($this->owner)
        ->post('http://acme.somaplus.test/academics/classes', ['name' => '', 'sort_order' => 0, 'form' => 'class-modal'])
        ->assertSessionHasErrors('name');

    $html = $this->actingAs($this->owner)->get('http://acme.somaplus.test/academics/classes')->assertOk()->getContent();

    expect($html)->toContain('data-open-modal="class-modal"');
});

it('adds a class through the pop-up', function () {
    $this->actingAs($this->owner)
        ->post('http://acme.somaplus.test/academics/classes', ['name' => 'Form 3', 'sort_order' => 3, 'form' => 'class-modal'])
        ->assertSessionHasNoErrors();

    expect(TenantContext::run($this->school, fn () => SchoolClass::query()->where('name', 'Form 3')->exists()))->toBeTrue();
});
