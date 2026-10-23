<?php

use App\Domain\Fees\GenerateInvoices;
use App\Models\Enrollment;
use App\Models\FeeItem;
use App\Models\FeeStructure;
use App\Models\Guardian;
use App\Models\Invoice;
use App\Models\SmsMessage;
use App\Models\Student;
use App\Models\Tenant;
use App\Tenancy\TenantContext;

beforeEach(function () {
    useTestHosts();

    $this->school = Tenant::factory()->create(['slug' => 'acme', 'name' => 'Acme Secondary']);
    $this->other = Tenant::factory()->create(['slug' => 'beta']);
    $this->url = 'http://acme.somaplus.test';
    $this->owner = userWithRole($this->school, 'owner');
    $this->accountant = userWithRole($this->school, 'accountant');
    $this->teacher = userWithRole($this->school, 'teacher');
    $this->inSchool = fn (Closure $work, ?Tenant $school = null) => TenantContext::run($school ?? $this->school, $work);
    $this->w = makeExamWorld($this->school);

    // One student with one guardian and one invoice, both of the school.
    $this->invoice = ($this->inSchool)(function () {
        $structure = FeeStructure::factory()->create([
            'academic_year_id' => $this->w['year']->id, 'term_id' => $this->w['term']->id, 'school_class_id' => $this->w['form1']->id,
        ]);
        $item = new FeeItem(['name' => 'Tuition', 'amount' => 10000000]);
        $item->fee_structure_id = $structure->id;
        $item->save();
        $student = Student::factory()->create(['first_name' => 'Zxqstudent']);
        Enrollment::factory()->create(['student_id' => $student->id, 'academic_year_id' => $this->w['year']->id, 'school_class_id' => $this->w['form1']->id]);
        $guardian = Guardian::factory()->create(['name' => 'Zxqguardian', 'phone' => '0712 345 678']);
        $student->guardians()->attach($guardian->id, ['relationship' => 'mother', 'is_primary' => true]);
        app(GenerateInvoices::class)->handle($structure);

        return Invoice::query()->where('student_id', $student->id)->firstOrFail();
    });
    $this->guardian = ($this->inSchool)(fn () => Guardian::query()->where('name', 'Zxqguardian')->firstOrFail());

    $this->messageFor = fn (array $overrides = []) => ($this->inSchool)(function () use ($overrides) {
        $message = new SmsMessage;
        $message->forceFill($overrides + [
            'invoice_id' => $this->invoice->id, 'guardian_id' => $this->guardian->id,
            'kind' => 'reminder', 'locale' => 'en', 'day' => now()->toDateString(), 'status' => 'sent',
        ]);
        $message->save();

        return $message;
    });
});

it('shows every message the school sent and how each went', function () {
    $sent = ($this->messageFor)(['status' => 'sent', 'sent_at' => now(), 'day' => now()->toDateString()]);
    $failed = ($this->messageFor)(['status' => 'failed', 'error' => 'bad_phone', 'day' => now()->subDay()->toDateString()]);
    $queued = ($this->messageFor)(['status' => 'queued', 'day' => now()->subDays(2)->toDateString()]);

    $page = $this->actingAs($this->owner)->get($this->url.'/communication/messages')->assertOk();

    $page->assertSee($sent->day->toDateString(), false)
        ->assertSee('Zxqstudent')->assertSee('Zxqguardian')
        ->assertSee(__('communication.statuses.sent'))
        ->assertSee(__('communication.reasons.bad_phone'));
});

it('lets only the owner and the accountant look at the messages', function () {
    foreach ([$this->owner, $this->accountant] as $user) {
        $this->actingAs($user)->get($this->url.'/communication/messages')->assertOk();
    }

    $this->actingAs($this->teacher)->get($this->url.'/communication/messages')->assertForbidden();
    $this->actingAs(userWithRole($this->school, 'parent_student'))->get($this->url.'/communication/messages')->assertForbidden();
});

it('filters the messages by status', function () {
    ($this->messageFor)(['status' => 'sent', 'day' => now()->subDay()->toDateString()]);
    ($this->messageFor)(['status' => 'failed', 'error' => 'rejected']);

    $page = $this->actingAs($this->accountant)->get($this->url.'/communication/messages?status=failed')->assertOk();

    // The status row of the failed message shows, the one of the sent message does not:
    // the reasons travel with the rows, not with the filter form.
    $page->assertSee(__('communication.reasons.rejected'))
        ->assertDontSee(__('communication.reasons.bad_phone'));
});

it('keeps each school to its own messages', function () {
    $w = ($this->inSchool)(fn () => makeExamWorld($this->other), $this->other);
    TenantContext::run($this->other, function () use ($w) {
        $structure = FeeStructure::factory()->create(['academic_year_id' => $w['year']->id, 'term_id' => $w['term']->id, 'school_class_id' => $w['form1']->id]);
        $item = new FeeItem(['name' => 'Tuition', 'amount' => 5000000]);
        $item->fee_structure_id = $structure->id;
        $item->save();
        $student = Student::factory()->create(['first_name' => 'Zxqotherstudent']);
        Enrollment::factory()->create(['student_id' => $student->id, 'academic_year_id' => $w['year']->id, 'school_class_id' => $w['form1']->id]);
        $guardian = Guardian::factory()->create(['name' => 'Zxqotherguardian']);
        $student->guardians()->attach($guardian->id, ['relationship' => 'mother', 'is_primary' => true]);
        app(GenerateInvoices::class)->handle($structure);

        $message = new SmsMessage;
        $message->forceFill([
            'invoice_id' => Invoice::query()->where('student_id', $student->id)->firstOrFail()->id,
            'guardian_id' => $guardian->id,
            'kind' => 'reminder', 'locale' => 'en', 'day' => now()->toDateString(), 'status' => 'sent',
        ])->save();
    });

    $page = $this->actingAs($this->owner)->get($this->url.'/communication/messages')->assertOk();

    $page->assertOk()->assertDontSee('Zxqotherstudent')->assertDontSee('Zxqotherguardian');
});

it('marks a reminder that was cancelled instead of sent', function () {
    ($this->messageFor)(['status' => 'cancelled']);

    $this->actingAs($this->owner)->get($this->url.'/communication/messages')->assertOk()
        ->assertSee(__('communication.statuses.cancelled'));
});
