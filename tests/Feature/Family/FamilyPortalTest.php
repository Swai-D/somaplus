<?php

use App\Domain\Fees\GenerateInvoices;
use App\Domain\Fees\RecordPayment;
use App\Enums\SubscriptionStatus;
use App\Models\AuditLog;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\FeeItem;
use App\Models\FeeStructure;
use App\Models\Guardian;
use App\Models\Invoice;
use App\Models\Student;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use App\Tenancy\TenantContext;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    useTestHosts();

    $this->school = Tenant::factory()->create(['slug' => 'acme', 'name' => 'Zxqacme Secondary']);
    $this->other = Tenant::factory()->create(['slug' => 'beta']);
    $this->url = 'http://acme.somaplus.test';
    $this->owner = userWithRole($this->school, 'owner');
    $this->w = makeExamWorld($this->school);
    $this->inSchool = fn (Closure $work, ?Tenant $school = null) => TenantContext::run($school ?? $this->school, $work);

    // A pupil with an invoice of TZS 100,000.00, placed in form 1 and linked to a guardian.
    $this->child = fn (string $name, ?Guardian $guardian = null, string $email = 'mama@zxq.test') => ($this->inSchool)(function () use ($name, $guardian, $email) {
        $structure = FeeStructure::factory()->create(['academic_year_id' => $this->w['year']->id, 'term_id' => $this->w['term']->id, 'school_class_id' => $this->w['form1']->id]);
        $item = new FeeItem(['name' => 'Tuition', 'amount' => 10000000]);
        $item->fee_structure_id = $structure->id;
        $item->save();
        $student = Student::factory()->create(['first_name' => $name]);
        Enrollment::factory()->create(['student_id' => $student->id, 'academic_year_id' => $this->w['year']->id, 'school_class_id' => $this->w['form1']->id]);
        app(GenerateInvoices::class)->handle($structure);
        $guardian ??= Guardian::factory()->create(['name' => 'Zxqmama', 'email' => $email]);
        $student->guardians()->attach($guardian->id, ['relationship' => 'mother', 'is_primary' => true]);

        return $student;
    });
    $this->guardianOf = fn (Student $student) => ($this->inSchool)(fn () => $student->guardians()->first());
    $this->grant = fn (Guardian $guardian, ?object $user = null) => $this->actingAs($user ?? $this->owner)->post($this->url."/guardians/{$guardian->id}/portal");
    $this->parentOf = fn (Guardian $guardian) => TenantContext::runAsSystem(fn () => User::findOrFail($guardian->fresh()->user_id));
});

it('gives a guardian an account with the parent role and sends a link on the school address', function () {
    Notification::fake();
    $guardian = ($this->guardianOf)(($this->child)('Zxqalpha'));

    ($this->grant)($guardian)->assertSessionHasNoErrors()->assertRedirect();

    $user = ($this->parentOf)($guardian);
    expect($user->email)->toBe('mama@zxq.test')->and(TenantContext::run($this->school, fn () => $user->fresh()->hasRole('parent_student')))->toBeTrue();
    Notification::assertSentTo($user, ResetPasswordNotification::class);
    expect(($this->inSchool)(fn () => AuditLog::where('action', 'portal.granted')->count()))->toBe(1);
});

it('refuses a guardian without an email, one who already has access, and an email of another account', function () {
    Notification::fake();
    $noMail = ($this->guardianOf)(($this->child)('Zxqnomail', null, ''));
    ($this->inSchool)(fn () => $noMail->forceFill(['email' => null])->save());
    ($this->grant)($noMail)->assertSessionHasErrors('portal');

    $guardian = ($this->guardianOf)(($this->child)('Zxqalpha'));
    ($this->grant)($guardian)->assertSessionHasNoErrors();
    ($this->grant)($guardian)->assertSessionHasErrors('portal');

    $taken = ($this->guardianOf)(($this->child)('Zxqbeta', null, $this->owner->email));
    ($this->grant)($taken)->assertSessionHasErrors('portal');
    expect(($this->inSchool)(fn () => $taken->fresh()->user_id))->toBeNull();
});

it('lets only the owner give or take away access', function (string $role) {
    Notification::fake();
    $guardian = ($this->guardianOf)(($this->child)('Zxqalpha'));

    ($this->grant)($guardian, userWithRole($this->school, $role))->assertForbidden();
    expect(($this->inSchool)(fn () => $guardian->fresh()->user_id))->toBeNull();
})->with(['head_teacher', 'teacher', 'accountant', 'parent_student']);

it('shows a parent only the children of their own guardian record', function () {
    Notification::fake();
    $mine = ($this->child)('Zxqmine');
    $theirs = ($this->child)('Zxqtheirs', null, 'other@zxq.test');
    ($this->grant)(($this->guardianOf)($mine));
    $parent = ($this->parentOf)(($this->guardianOf)($mine));
    $this->app['auth']->forgetGuards();

    $this->actingAs($parent)->get($this->url.'/family')->assertOk()->assertSee('Zxqmine')->assertDontSee('Zxqtheirs');
    $this->actingAs($parent)->get($this->url.'/family/children/'.$mine->id)->assertOk()->assertSee('INV-')->assertSee('TZS 100,000.00');
    $this->actingAs($parent)->get($this->url.'/family/children/'.$theirs->id)->assertNotFound();
    $this->actingAs($parent)->get($this->url.'/family/children/'.$theirs->id.'/report/'.$this->w['term']->id)->assertNotFound();
});

it('sends a parent to their children instead of the school dashboard', function () {
    Notification::fake();
    $mine = ($this->child)('Zxqmine');
    ($this->grant)(($this->guardianOf)($mine));
    $parent = ($this->parentOf)(($this->guardianOf)($mine));
    $this->app['auth']->forgetGuards();

    $this->actingAs($parent)->get($this->url.'/')->assertRedirect(route('family.index', [], false));
});

it('keeps a parent out of every staff page', function (string $path) {
    Notification::fake();
    $mine = ($this->child)('Zxqmine');
    ($this->grant)(($this->guardianOf)($mine));
    $parent = ($this->parentOf)(($this->guardianOf)($mine));
    $this->app['auth']->forgetGuards();

    $this->actingAs($parent)->get($this->url.$path)->assertForbidden();
})->with(['/students', '/guardians', '/fees/invoices', '/fees/payments', '/exams', '/settings/users', '/communication/reminders', '/attendance']);

it('shows paid and owed amounts and the receipts, with reversed payments marked', function () {
    Notification::fake();
    $mine = ($this->child)('Zxqmine');
    ($this->grant)(($this->guardianOf)($mine));
    $parent = ($this->parentOf)(($this->guardianOf)($mine));
    ($this->inSchool)(function () use ($mine) {
        $invoice = Invoice::query()->where('student_id', $mine->id)->firstOrFail();
        app(RecordPayment::class)->handle($invoice, ['amount' => 4000000, 'method' => 'mobile_money', 'paid_on' => now()->toDateString(), 'idempotency_key' => 'fam-1']);
    });
    $this->app['auth']->forgetGuards();

    $this->actingAs($parent)->get($this->url.'/family')->assertSee('TZS 60,000.00');
    $this->actingAs($parent)->get($this->url.'/family/children/'.$mine->id)->assertSee('TZS 40,000.00')->assertSee('REC-000001');
});

it('shows a report card only for locked exams', function () {
    Notification::fake();
    $mine = ($this->child)('Zxqmine');
    ($this->grant)(($this->guardianOf)($mine));
    $parent = ($this->parentOf)(($this->guardianOf)($mine));
    ($this->inSchool)(function () use ($mine) {
        foreach (['Zxqlocked' => now(), 'Zxqopen' => null] as $name => $lockedAt) {
            $exam = Exam::factory()->create(['name' => $name, 'academic_year_id' => $this->w['year']->id, 'term_id' => $this->w['term']->id, 'grading_scale_id' => $this->w['scale']->id, 'max_mark' => 100, 'locked_at' => $lockedAt, 'locked_by' => $lockedAt ? $this->owner->id : null]);
            $exam->marks()->create(['subject_id' => $this->w['math']->id, 'student_id' => $mine->id, 'score' => '85', 'is_absent' => false, 'school_class_id' => $this->w['form1']->id, 'stream_id' => null]);
        }
    });
    $this->app['auth']->forgetGuards();

    $this->actingAs($parent)->get($this->url.'/family/children/'.$mine->id.'/report/'.$this->w['term']->id)
        ->assertOk()->assertSee('Zxqlocked')->assertDontSee('Zxqopen')->assertSee('Zxqacme Secondary');
    expect(($this->inSchool)(fn () => AuditLog::where('action', 'report.viewed')->where('user_id', $parent->id)->count()))->toBe(1);
});

it('ends the access when it is taken away', function () {
    Notification::fake();
    $mine = ($this->child)('Zxqmine');
    $guardian = ($this->guardianOf)($mine);
    ($this->grant)($guardian);
    $parent = ($this->parentOf)($guardian);

    $this->app['auth']->forgetGuards();
    $this->actingAs($this->owner)->delete($this->url."/guardians/{$guardian->id}/portal")->assertSessionHasNoErrors()->assertRedirect();

    expect(($this->inSchool)(fn () => $guardian->fresh()->user_id))->toBeNull();
    $this->app['auth']->forgetGuards();
    $this->actingAs(TenantContext::runAsSystem(fn () => User::findOrFail($parent->id)))->get($this->url.'/family')->assertForbidden();
    expect(($this->inSchool)(fn () => AuditLog::where('action', 'portal.revoked')->count()))->toBe(1);
});

it('does not delete a guardian who still has access', function () {
    Notification::fake();
    $guardian = ($this->guardianOf)(($this->child)('Zxqmine'));
    ($this->inSchool)(fn () => $guardian->students()->detach());
    ($this->grant)($guardian);

    $this->actingAs($this->owner)->delete($this->url."/guardians/{$guardian->id}")->assertRedirect();

    expect(($this->inSchool)(fn () => Guardian::query()->whereKey($guardian->id)->exists()))->toBeTrue();
});

it('lets a read-only school look at the family pages', function () {
    Notification::fake();
    $mine = ($this->child)('Zxqmine');
    ($this->grant)(($this->guardianOf)($mine));
    $parent = ($this->parentOf)(($this->guardianOf)($mine));
    $this->school->update(['subscription_status' => SubscriptionStatus::ReadOnly]);
    $this->app['auth']->forgetGuards();

    $this->actingAs($parent)->get($this->url.'/family')->assertOk()->assertSee('Zxqmine');
});

it('is in Swahili', function () {
    Notification::fake();
    $mine = ($this->child)('Zxqmine');
    ($this->grant)(($this->guardianOf)($mine));
    $parent = ($this->parentOf)(($this->guardianOf)($mine));
    $this->app['auth']->forgetGuards();

    $this->actingAs($parent)->withSession(['locale' => 'sw'])->get($this->url.'/family')->assertSee(__('family.intro', [], 'sw'));
});
