<?php

use App\Domain\Fees\GenerateInvoices;
use App\Domain\Fees\RecordPayment;
use App\Enums\SubscriptionStatus;
use App\Models\AuditLog;
use App\Models\Enrollment;
use App\Models\FeeItem;
use App\Models\FeeStructure;
use App\Models\Invoice;
use App\Models\Receipt;
use App\Models\SchoolProfile;
use App\Models\Student;
use App\Models\Tenant;
use App\Tenancy\TenantContext;

beforeEach(function () {
    useTestHosts();

    $this->school = Tenant::factory()->create(['slug' => 'acme', 'name' => 'Zxqacme Secondary']);
    $this->other = Tenant::factory()->create(['slug' => 'beta', 'name' => 'Zxqbeta Primary']);
    $this->url = 'http://acme.somaplus.test';
    $this->owner = userWithRole($this->school, 'owner');
    $this->inSchool = fn (Closure $work, ?Tenant $school = null) => TenantContext::run($school ?? $this->school, $work);

    $this->save = fn (array $data = [], ?object $user = null) => $this->actingAs($user ?? $this->owner)->put($this->url.'/settings/school', $data + [
        'phone' => '+255 712 345 678', 'email' => 'office@zxqacme.test', 'address' => 'Zxqstreet 5, Dar es Salaam',
    ]);
    $this->profile = fn (?Tenant $school = null) => ($this->inSchool)(fn () => SchoolProfile::query()->first(), $school);

    $this->receiptUrl = function () {
        return ($this->inSchool)(function () {
            $w = makeExamWorld($this->school);
            $structure = FeeStructure::factory()->create(['academic_year_id' => $w['year']->id, 'term_id' => $w['term']->id, 'school_class_id' => $w['form1']->id]);
            $item = new FeeItem(['name' => 'Tuition', 'amount' => 10000000]);
            $item->fee_structure_id = $structure->id;
            $item->save();
            $student = Student::factory()->create();
            Enrollment::factory()->create(['student_id' => $student->id, 'academic_year_id' => $w['year']->id, 'school_class_id' => $w['form1']->id]);
            app(GenerateInvoices::class)->handle($structure);
            $invoice = Invoice::query()->where('student_id', $student->id)->firstOrFail();
            app(RecordPayment::class)->handle($invoice, ['amount' => 100000, 'method' => 'cash', 'paid_on' => now()->toDateString(), 'idempotency_key' => 'k1']);

            return $this->url.'/fees/receipts/'.Receipt::query()->firstOrFail()->id;
        });
    };
});

it('saves the contact details and audits the change with ids only', function () {
    ($this->save)()->assertSessionHasNoErrors()->assertRedirect();

    expect(($this->profile)()->phone)->toBe('+255 712 345 678')->and(($this->profile)()->address)->toBe('Zxqstreet 5, Dar es Salaam');

    $log = ($this->inSchool)(fn () => AuditLog::query()->where('action', 'settings.profile_updated')->sole());
    expect(json_encode($log->getAttributes()))->not->toContain('Zxqstreet')->not->toContain('712');
});

it('clears a field that is left empty', function () {
    ($this->save)();
    ($this->save)(['phone' => '', 'address' => '  ']);

    expect(($this->profile)()->phone)->toBeNull()->and(($this->profile)()->address)->toBeNull()->and(($this->profile)()->email)->toBe('office@zxqacme.test');
    expect(($this->inSchool)(fn () => SchoolProfile::query()->count()))->toBe(1);
});

it('refuses a bad phone number or email', function (array $over, string $field) {
    ($this->save)($over)->assertSessionHasErrors($field);

    expect(($this->profile)())->toBeNull();
})->with([
    'letters in phone' => [['phone' => 'call me'], 'phone'],
    'bad email' => [['email' => 'nope'], 'email'],
    'long address' => [['address' => str_repeat('a', 251)], 'address'],
]);

it('lets only the owner open and change the school profile', function (string $role, bool $allowed) {
    $user = userWithRole($this->school, $role);

    $get = $this->actingAs($user)->get($this->url.'/settings/school');
    $allowed ? $get->assertOk() : $get->assertForbidden();
    $this->app['auth']->forgetGuards();
    $put = ($this->save)([], $user);
    $allowed ? $put->assertRedirect() : $put->assertForbidden();
})->with([
    'owner' => ['owner', true],
    'head teacher' => ['head_teacher', false],
    'teacher' => ['teacher', false],
    'accountant' => ['accountant', false],
]);

it('prints the name and contact details of the school on its receipts', function () {
    $url = ($this->receiptUrl)();
    $this->actingAs(userWithRole($this->school, 'accountant'))->get($url)->assertOk()->assertSee('Zxqacme Secondary')->assertDontSee('Zxqstreet');

    ($this->save)();
    $this->app['auth']->forgetGuards();
    $this->actingAs(userWithRole($this->school, 'accountant'))->get($url)->assertOk()
        ->assertSee('Zxqacme Secondary')->assertSee('Zxqstreet 5, Dar es Salaam')->assertSee('+255 712 345 678')->assertSee('office@zxqacme.test');
});

it('keeps one school details out of another school', function () {
    ($this->save)();

    expect(($this->profile)($this->other))->toBeNull();
    $html = ($this->inSchool)(fn () => view('partials.letterhead')->render(), $this->other);
    expect($html)->toContain('Zxqbeta Primary')->not->toContain('Zxqstreet')->not->toContain('Zxqacme');
});

it('blocks saving for a read-only school but still shows the page', function () {
    $this->school->update(['subscription_status' => SubscriptionStatus::ReadOnly]);

    $this->actingAs($this->owner)->get($this->url.'/settings/school')->assertOk();
    $this->app['auth']->forgetGuards();
    ($this->save)()->assertForbidden();
    expect(($this->profile)())->toBeNull();
});

it('is in Swahili and in the settings menu', function () {
    $this->actingAs($this->owner)->withSession(['locale' => 'sw'])->get($this->url.'/settings/school')->assertOk()
        ->assertSee(__('school.intro', [], 'sw'))->assertSee('/settings/school');
});
