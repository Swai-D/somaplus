<?php

use App\Domain\Fees\GenerateInvoices;
use App\Domain\Fees\RecordPayment;
use App\Enums\SubscriptionStatus;
use App\Models\AuditLog;
use App\Models\Enrollment;
use App\Models\FeeItem;
use App\Models\FeeStructure;
use App\Models\Guardian;
use App\Models\Invoice;
use App\Models\SmsMessage;
use App\Models\Student;
use App\Models\Tenant;
use App\Models\TenantIntegration;
use App\Tenancy\TenantContext;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

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

    $this->smsOn = fn (bool $on = true) => ($this->inSchool)(fn () => (new TenantIntegration)->forceFill([
        'channel' => 'sms', 'driver' => 'nextsms', 'enabled' => $on,
        'settings' => ['sender_id' => 'ACME'], 'secrets' => ['username' => 'u', 'password' => 'p'],
    ])->save());

    $this->fakeSms = function (?array $body = null) {
        Http::swap(new HttpFactory);
        Http::fake(['*' => Http::response($body ?? ['messages' => [['messageId' => 'M-1', 'status' => ['groupId' => 1]]]])]);
    };

    // One invoice of TZS 100,000.00 for a student with one guardian (phone given or none).
    $this->invoiceFor = fn (string $name, ?string $phone = 'auto', ?string $guardianName = 'Zxqparent') => ($this->inSchool)(function () use ($name, $phone, $guardianName) {
        // A guardian's phone is unique in a school: Zxqalpha gets the number the tests look for, others one made from the name.
        $phone = $phone === 'auto' ? ($name === 'Zxqalpha' ? '0712 345 678' : '07'.str_pad((string) (crc32($name) % 100000000), 8, '0', STR_PAD_LEFT)) : $phone;
        $structure = FeeStructure::factory()->create(['academic_year_id' => $this->w['year']->id, 'term_id' => $this->w['term']->id, 'school_class_id' => $this->w['form1']->id]);
        $item = new FeeItem(['name' => 'Tuition', 'amount' => 10000000]);
        $item->fee_structure_id = $structure->id;
        $item->save();
        $student = Student::factory()->create(['first_name' => $name]);
        Enrollment::factory()->create(['student_id' => $student->id, 'academic_year_id' => $this->w['year']->id, 'school_class_id' => $this->w['form1']->id]);
        if ($phone !== null) {
            $guardian = Guardian::factory()->create(['name' => $guardianName, 'phone' => $phone]);
            $student->guardians()->attach($guardian->id, ['relationship' => 'mother', 'is_primary' => true]);
        }
        app(GenerateInvoices::class)->handle($structure);

        return Invoice::query()->where('student_id', $student->id)->firstOrFail();
    });

    $this->send = fn (array $ids, array $data = [], ?object $user = null) => $this->actingAs($user ?? $this->accountant)
        ->post($this->url.'/communication/reminders', $data + ['invoices' => $ids, 'locale' => 'en']);
    $this->messages = fn () => ($this->inSchool)(fn () => SmsMessage::query()->orderBy('id')->get());
});

it('sends an SMS to the guardian and keeps no phone number or text', function () {
    ($this->smsOn)();
    ($this->fakeSms)();
    $invoice = ($this->invoiceFor)('Zxqalpha');

    ($this->send)([$invoice->id])->assertSessionHasNoErrors()->assertRedirect();

    $message = ($this->messages)()->sole();
    expect($message->status)->toBe('sent')->and($message->provider_reference)->toBe('M-1')->and($message->sent_at)->not->toBeNull()
        ->and(json_encode($message->getAttributes()))->not->toContain('712345678')->not->toContain('Zxqalpha');

    Http::assertSent(fn (HttpRequest $sent) => $sent['to'] === '255712345678' && $sent['from'] === 'ACME'
        && str_contains($sent['text'], 'Acme Secondary') && str_contains($sent['text'], 'Zxqalpha') && str_contains($sent['text'], 'TZS 100,000.00')
        && $sent->hasHeader('Authorization'));
});

it('writes the message in Swahili when asked', function () {
    ($this->smsOn)();
    ($this->fakeSms)();
    $invoice = ($this->invoiceFor)('Zxqalpha');

    ($this->send)([$invoice->id], ['locale' => 'sw']);

    Http::assertSent(fn (HttpRequest $sent) => str_contains($sent['text'], 'ana deni la'));
});

it('reminds a guardian once per invoice per day', function () {
    ($this->smsOn)();
    ($this->fakeSms)();
    $invoice = ($this->invoiceFor)('Zxqalpha');

    ($this->send)([$invoice->id]);
    ($this->send)([$invoice->id])->assertSessionHas('status');

    expect(($this->messages)())->toHaveCount(1);
    Http::assertSentCount(1);

    $this->travel(1)->days();
    ($this->send)([$invoice->id]);
    expect(($this->messages)())->toHaveCount(2);
});

it('counts a guardian without a valid phone and sends nothing for it', function () {
    ($this->smsOn)();
    ($this->fakeSms)();
    $bad = ($this->invoiceFor)('Zxqbeta', '12345');
    $none = ($this->invoiceFor)('Zxqgamma', null);

    ($this->send)([$bad->id, $none->id]);

    expect(($this->messages)())->toHaveCount(0);
    Http::assertNothingSent();
});

it('cancels the reminder when the invoice was paid before it was sent', function () {
    ($this->smsOn)();
    ($this->fakeSms)();
    $invoice = ($this->invoiceFor)('Zxqalpha');
    ($this->inSchool)(fn () => app(RecordPayment::class)->handle($invoice, ['amount' => 10000000, 'method' => 'cash', 'paid_on' => now()->toDateString(), 'idempotency_key' => 'k-'.$invoice->id]));

    ($this->send)([$invoice->id])->assertRedirect();

    expect(($this->messages)())->toHaveCount(0);
    Http::assertNothingSent();
});

it('refuses to send when SMS is not switched on', function () {
    ($this->fakeSms)();
    $invoice = ($this->invoiceFor)('Zxqalpha');

    ($this->send)([$invoice->id])->assertSessionHasErrors('invoices');
    ($this->smsOn)(false);
    ($this->send)([$invoice->id])->assertSessionHasErrors('invoices');

    expect(($this->messages)())->toHaveCount(0);
    Http::assertNothingSent();
});

it('records a rejected message as failed without personal data in the reason', function () {
    ($this->smsOn)();
    ($this->fakeSms)(['messages' => [['status' => ['groupId' => 5, 'description' => 'Rejected 255712345678']]]]);
    $invoice = ($this->invoiceFor)('Zxqalpha');

    ($this->send)([$invoice->id]);

    $message = ($this->messages)()->sole();
    expect($message->status)->toBe('failed')->and($message->error)->not->toContain('712345678');
});

it('keeps a message that could not reach the provider as failed after retries', function () {
    ($this->smsOn)();
    Http::swap(new HttpFactory);
    Http::fake(['*' => Http::response('down', 503)]);
    $invoice = ($this->invoiceFor)('Zxqalpha');

    ($this->send)([$invoice->id]);

    expect(($this->messages)()->sole()->status)->toBeIn(['queued', 'failed']);
});

it('audits the queueing with ids only', function () {
    ($this->smsOn)();
    ($this->fakeSms)();
    $invoice = ($this->invoiceFor)('Zxqalpha');

    ($this->send)([$invoice->id]);

    $log = ($this->inSchool)(fn () => AuditLog::query()->where('action', 'reminder.queued')->sole());
    expect($log->user_id)->toBe($this->accountant->id)->and(json_encode($log->getAttributes()))->not->toContain('712345678')->not->toContain('Zxqparent');
});

it('lets only the owner and accountant use fee reminders', function () {
    ($this->smsOn)();
    ($this->fakeSms)();
    $invoice = ($this->invoiceFor)('Zxqalpha');

    foreach ([$this->owner, $this->accountant] as $user) {
        $this->app['auth']->forgetGuards();
        $this->actingAs($user)->get($this->url.'/communication/reminders')->assertOk();
    }
    foreach (['teacher' => $this->teacher, 'head_teacher' => userWithRole($this->school, 'head_teacher')] as $user) {
        $this->app['auth']->forgetGuards();
        $this->actingAs($user)->get($this->url.'/communication/reminders')->assertForbidden();
        $this->app['auth']->forgetGuards();
        ($this->send)([$invoice->id], [], $user)->assertForbidden();
    }
    expect(($this->messages)())->toHaveCount(0);
});

it('shows only invoices that still owe, with the guardian count', function () {
    ($this->smsOn)();
    $owing = ($this->invoiceFor)('Zxqowing');
    $paid = ($this->invoiceFor)('Zxqpaid');
    ($this->inSchool)(fn () => app(RecordPayment::class)->handle($paid, ['amount' => 10000000, 'method' => 'cash', 'paid_on' => now()->toDateString(), 'idempotency_key' => 'k-'.$paid->id]));

    $this->actingAs($this->accountant)->get($this->url.'/communication/reminders')
        ->assertOk()->assertSee('Zxqowing')->assertDontSee('Zxqpaid');
});

it('never reminds for an invoice of another school', function () {
    ($this->smsOn)();
    ($this->fakeSms)();
    $w = ($this->inSchool)(fn () => makeExamWorld($this->other), $this->other);
    $foreign = ($this->inSchool)(function () use ($w) {
        $structure = FeeStructure::factory()->create(['academic_year_id' => $w['year']->id, 'term_id' => $w['term']->id, 'school_class_id' => $w['form1']->id]);
        $item = new FeeItem(['name' => 'Tuition', 'amount' => 100]);
        $item->fee_structure_id = $structure->id;
        $item->save();
        $student = Student::factory()->create();
        Enrollment::factory()->create(['student_id' => $student->id, 'academic_year_id' => $w['year']->id, 'school_class_id' => $w['form1']->id]);
        app(GenerateInvoices::class)->handle($structure);

        return Invoice::query()->firstOrFail();
    }, $this->other);

    ($this->send)([$foreign->id])->assertSessionHasErrors('invoices.0');

    expect(($this->messages)())->toHaveCount(0);
    Http::assertNothingSent();
});

it('refuses an empty choice, a bad language and too many invoices', function () {
    ($this->smsOn)();
    $invoice = ($this->invoiceFor)('Zxqalpha');

    ($this->send)([])->assertSessionHasErrors('invoices');
    ($this->send)([$invoice->id], ['locale' => 'fr'])->assertSessionHasErrors('locale');
    ($this->send)(range(1, 101))->assertSessionHasErrors('invoices');
});

it('blocks sending for a read-only school but still shows the page', function () {
    ($this->smsOn)();
    ($this->fakeSms)();
    $invoice = ($this->invoiceFor)('Zxqalpha');
    $this->school->update(['subscription_status' => SubscriptionStatus::ReadOnly]);

    $this->actingAs($this->accountant)->get($this->url.'/communication/reminders')->assertOk();
    ($this->send)([$invoice->id])->assertStatus(403);
    expect(($this->messages)())->toHaveCount(0);
});

it('lists the NextSMS driver under Settings > Integrations and keeps its password hidden', function () {
    $pw = 'zxq'.Str::random(12);
    $this->actingAs($this->owner)->get($this->url.'/settings/integrations')->assertOk()->assertSee('NextSMS');

    $this->actingAs($this->owner)->put($this->url.'/settings/integrations/sms', [
        'driver' => 'nextsms', 'enabled' => '1', 'config' => ['username' => 'u', 'password' => $pw, 'sender_id' => 'ACME'],
    ])->assertSessionHasNoErrors();

    $this->app['auth']->forgetGuards();
    $this->actingAs($this->owner)->get($this->url.'/settings/integrations')->assertDontSee($pw);
});
