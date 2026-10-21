<?php

use App\Enums\SubscriptionStatus;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\Enrollment;
use App\Models\SchoolClass;
use App\Models\Stream;
use App\Models\Student;
use App\Models\Tenant;
use App\Tenancy\TenantContext;

beforeEach(function () {
    useTestHosts();

    $this->school = Tenant::factory()->create(['slug' => 'acme', 'name' => 'Acme Secondary']);
    $this->other = Tenant::factory()->create(['slug' => 'beta']);
    $this->url = 'http://acme.somaplus.test';
    $this->owner = userWithRole($this->school, 'owner');

    $this->inSchool = fn (Closure $work, ?Tenant $school = null) => TenantContext::run($school ?? $this->school, $work);

    [$this->year, $this->form1, $this->form2, $this->streamA] = ($this->inSchool)(function () {
        $year = AcademicYear::factory()->current()->create(['name' => '2026']);
        $form1 = SchoolClass::factory()->create(['name' => 'Form 1', 'sort_order' => 1]);
        $form2 = SchoolClass::factory()->create(['name' => 'Form 2', 'sort_order' => 2]);

        return [$year, $form1, $form2, Stream::factory()->create(['school_class_id' => $form1->id, 'name' => 'A'])];
    });

    $this->student = fn (array $attributes = [], $class = null, $stream = null, ?Tenant $school = null) => ($this->inSchool)(function () use ($attributes, $class, $stream) {
        $student = Student::factory()->create($attributes + ['status' => 'active']);
        if ($class !== null) {
            Enrollment::factory()->create(['student_id' => $student->id, 'academic_year_id' => $this->year->id, 'school_class_id' => $class->id, 'stream_id' => $stream?->id]);
        }

        return $student;
    }, $school);

    $this->amina = ($this->student)(['first_name' => 'Amina', 'last_name' => 'Juma', 'admission_number' => 'A-001', 'date_of_birth' => '2012-05-06'], $this->form1, $this->streamA);
    $this->baraka = ($this->student)(['first_name' => 'Baraka', 'last_name' => 'Mushi', 'admission_number' => 'A-002'], $this->form1);
    $this->zawadi = ($this->student)(['first_name' => 'Zawadi', 'last_name' => 'Ally', 'admission_number' => 'B-001'], $this->form2);
});

it('prints one card with school, name, number, class, year and a barcode, and audits it', function () {
    $this->actingAs($this->owner)->get("{$this->url}/students/{$this->amina->id}/id-card")
        ->assertOk()
        ->assertSee('Acme Secondary')->assertSee('Amina')->assertSee('A-001')->assertSee('Form 1 A')->assertSee('2026')
        ->assertSee('<svg', false)->assertSee('window.print()', false)
        ->assertDontSee('2012-05-06')->assertDontSee('Baraka');

    ($this->inSchool)(fn () => expect(AuditLog::where('action', 'student.id_card_printed')->where('subject_id', $this->amina->id)->where('user_id', $this->owner->id)->count())->toBe(1));
});

it('prints the chosen students together, in name order, and skips other schools and students who left', function () {
    $left = ($this->student)(['first_name' => 'Chande', 'admission_number' => 'C-001', 'status' => 'left'], $this->form1);
    $foreign = ($this->student)(['first_name' => 'Foreign', 'admission_number' => 'F-001'], null, null, $this->other);

    $response = $this->actingAs($this->owner)->post("{$this->url}/students/id-cards", ['ids' => [$this->baraka->id, $this->amina->id, $left->id, $foreign->id]])->assertOk();

    $response->assertSeeInOrder(['Amina', 'Baraka'])->assertDontSee('Chande')->assertDontSee('Foreign')->assertDontSee('Zawadi');
    ($this->inSchool)(fn () => expect(AuditLog::where('action', 'student.id_card_printed')->count())->toBe(2));
});

it('answers 404 when nothing printable was chosen', function () {
    $left = ($this->student)(['admission_number' => 'C-002', 'status' => 'left']);
    $foreign = ($this->student)(['admission_number' => 'F-002'], null, null, $this->other);

    $this->actingAs($this->owner)->post("{$this->url}/students/id-cards", ['ids' => [$left->id, $foreign->id]])->assertNotFound();
    $this->actingAs($this->owner)->get("{$this->url}/students/{$left->id}/id-card")->assertNotFound();
    $this->actingAs($this->owner)->get("{$this->url}/students/{$foreign->id}/id-card")->assertNotFound();
});

it('refuses a bad or too long selection', function (array $input) {
    config()->set('students.id_card_max', 2);

    $this->actingAs($this->owner)->post("{$this->url}/students/id-cards", $input + ['ids' => [$this->amina->id]])->assertSessionHasErrors();
})->with([
    'no ids' => [['ids' => []]],
    'not numbers' => [['ids' => ['abc']]],
    'too many' => [['ids' => [1, 2, 3]]],
    'repeated' => [['ids' => [1, 1]]],
]);

it('lists the active students of a class to choose from', function () {
    ($this->student)(['first_name' => 'Chande', 'admission_number' => 'C-003', 'status' => 'left'], $this->form1);

    $this->actingAs($this->owner)->get("{$this->url}/students/id-cards")->assertOk()->assertSee('Form 2')->assertDontSee('Amina');
    $this->actingAs($this->owner)->get("{$this->url}/students/id-cards?class={$this->form1->id}")->assertOk()
        ->assertSee('Amina')->assertSee('Baraka')->assertDontSee('Zawadi')->assertDontSee('Chande');
    $this->actingAs($this->owner)->get("{$this->url}/students/id-cards?class=all")->assertOk()->assertSee('Zawadi')->assertSee('Amina');
});

it('does not list the students of another school', function () {
    $foreign = ($this->student)(['first_name' => 'Foreign', 'admission_number' => 'F-003'], null, null, $this->other);

    $this->actingAs($this->owner)->get("{$this->url}/students/id-cards?class=all")->assertOk()->assertDontSee('Foreign');
});

it('lets owner and head teacher print, nobody else', function (string $role, int $status) {
    $user = userWithRole($this->school, $role);

    $this->actingAs($user)->get("{$this->url}/students/id-cards")->assertStatus($status);
    $this->actingAs($user)->get("{$this->url}/students/{$this->amina->id}/id-card")->assertStatus($status);
    $this->actingAs($user)->post("{$this->url}/students/id-cards", ['ids' => [$this->amina->id]])->assertStatus($status);

    ($this->inSchool)(fn () => expect(AuditLog::where('action', 'student.id_card_printed')->count())->toBe($status === 200 ? 2 : 0));
})->with([
    'owner' => ['owner', 200],
    'head teacher' => ['head_teacher', 200],
    'teacher' => ['teacher', 403],
    'accountant' => ['accountant', 403],
    'parent' => ['parent_student', 403],
]);

it('still prints in a read-only school', function () {
    $this->school->update(['subscription_status' => SubscriptionStatus::ReadOnly]);
    $owner = userWithRole($this->school, 'owner');

    $this->actingAs($owner)->get("{$this->url}/students/{$this->amina->id}/id-card")->assertOk();
    $this->actingAs($owner)->post("{$this->url}/students/id-cards", ['ids' => [$this->amina->id]])->assertOk();
});

it('prints a card without class when the student has no placement, and without barcode when the number is not plain ASCII', function () {
    $loose = ($this->student)(['first_name' => 'Loose', 'last_name' => 'End', 'admission_number' => 'Ñ-77']);

    $response = $this->actingAs($this->owner)->get("{$this->url}/students/{$loose->id}/id-card")->assertOk()->assertSee('Loose')->assertSee('Ñ-77');

    expect($response->getContent())->not->toContain('<svg')->not->toContain('Form 1');
});

it('escapes names', function () {
    $odd = ($this->student)(['first_name' => '<b>Bold</b>', 'last_name' => 'Name', 'admission_number' => 'X-1']);

    $this->actingAs($this->owner)->get("{$this->url}/students/{$odd->id}/id-card")->assertOk()->assertDontSee('<b>Bold</b>', false)->assertSee('&lt;b&gt;Bold', false);
});

it('speaks Swahili and links the pages from the menu, the student page and the list', function () {
    $this->actingAs($this->owner)->withSession(['locale' => 'sw'])->get("{$this->url}/students/{$this->amina->id}/id-card")->assertOk()->assertSee('Kitambulisho');
    $this->actingAs($this->owner)->withSession(['locale' => 'sw'])->get("{$this->url}/students/id-cards")->assertOk()->assertSee('Chapa vitambulisho');

    $this->actingAs($this->owner)->get($this->url)->assertSee('/students/id-cards', false);
    $this->actingAs($this->owner)->get("{$this->url}/students/{$this->amina->id}")->assertSee("/students/{$this->amina->id}/id-card", false);

    $teacher = userWithRole($this->school, 'teacher');
    $this->actingAs($teacher)->get("{$this->url}/students/{$this->amina->id}")->assertOk()->assertDontSee("/students/{$this->amina->id}/id-card", false);
});

it('warns when there is no current academic year', function () {
    ($this->inSchool)(fn () => AcademicYear::query()->update(['is_current' => false]));

    $this->actingAs($this->owner)->get("{$this->url}/students/id-cards")->assertOk()->assertSee('no current academic year', false);
});
