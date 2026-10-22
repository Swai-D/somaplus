<?php

use App\Domain\Academics\DeleteAcademicRecord;
use App\Domain\Fees\GenerateInvoices;
use App\Domain\Fees\NextNumber;
use App\Domain\Fees\SetControlNumber;
use App\Enums\SubscriptionStatus;
use App\Models\AuditLog;
use App\Models\Enrollment;
use App\Models\FeeItem;
use App\Models\FeeStructure;
use App\Models\Guardian;
use App\Models\Invoice;
use App\Models\SchoolClass;
use App\Models\Stream;
use App\Models\Student;
use App\Models\Tenant;
use App\Models\Term;
use App\Tenancy\TenantContext;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\TestResponse;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    useTestHosts();

    $this->school = Tenant::factory()->create(['slug' => 'acme']);
    $this->other = Tenant::factory()->create(['slug' => 'beta']);
    $this->url = 'http://acme.somaplus.test';
    $this->accountant = userWithRole($this->school, 'accountant');
    $this->owner = userWithRole($this->school, 'owner');
    $this->w = makeExamWorld($this->school);

    $this->inSchool = fn (Closure $work, ?Tenant $school = null) => TenantContext::run($school ?? $this->school, $work);

    $this->items = fn () => [['name' => 'Tuition', 'amount' => '150000'], ['name' => 'Uniform', 'amount' => '25000.50']];
    $this->payload = fn (array $extra = []) => $extra + [
        'name' => 'Day scholar', 'term_id' => $this->w['term']->id, 'school_class_id' => $this->w['form1']->id, 'items' => ($this->items)(),
    ];
    $this->create = fn (array $extra = [], ?object $user = null) => $this->actingAs($user ?? $this->accountant)->post("{$this->url}/fees/structures", ($this->payload)($extra));

    $this->structure = fn (array $attributes = [], ?array $items = null) => ($this->inSchool)(function () use ($attributes, $items) {
        $structure = FeeStructure::factory()->create($attributes + [
            'academic_year_id' => $this->w['year']->id, 'term_id' => $this->w['term']->id, 'school_class_id' => $this->w['form1']->id,
        ]);
        foreach ($items ?? [['Tuition', 15000000], ['Uniform', 2500050]] as [$name, $amount]) {
            $item = new FeeItem(['name' => $name, 'amount' => $amount]);
            $item->fee_structure_id = $structure->id;
            $item->save();
        }

        return $structure;
    });

    $this->place = fn (string $name, ?SchoolClass $class = null, ?Stream $stream = null) => ($this->inSchool)(function () use ($name, $class, $stream) {
        $student = Student::factory()->create(['first_name' => $name]);
        Enrollment::factory()->create([
            'student_id' => $student->id, 'academic_year_id' => $this->w['year']->id,
            'school_class_id' => ($class ?? $this->w['form1'])->id, 'stream_id' => $stream?->id,
        ]);

        return $student;
    });

    $this->generate = fn (FeeStructure $structure, array $data = [], ?object $user = null) => $this->actingAs($user ?? $this->accountant)
        ->post("{$this->url}/fees/structures/{$structure->id}/invoices", $data);
    $this->invoices = fn () => ($this->inSchool)(fn () => Invoice::query()->orderBy('number')->get());
});

it('saves a fee structure in minor units and records it in the audit log', function () {
    ($this->create)()->assertSessionHasNoErrors()->assertRedirect();

    $structure = ($this->inSchool)(fn () => FeeStructure::query()->with('items')->firstOrFail());
    expect($structure->items->pluck('amount', 'name')->all())->toBe(['Tuition' => 15000000, 'Uniform' => 2500050])
        ->and($structure->total())->toBe(17500050)
        ->and($structure->tenant_id)->toBe($this->school->id)
        ->and($structure->academic_year_id)->toBe($this->w['year']->id)
        ->and(($this->inSchool)(fn () => AuditLog::where('action', 'fee_structure.created')->where('subject_id', $structure->id)->count()))->toBe(1);
});

it('refuses amounts that are not plain positive numbers and saves nothing', function (string $amount) {
    ($this->create)(['items' => [['name' => 'Tuition', 'amount' => '100'], ['name' => 'Bad', 'amount' => $amount]]])
        ->assertSessionHasErrors('items');

    expect(($this->inSchool)(fn () => FeeStructure::query()->count()))->toBe(0);
})->with(['abc', '-5', '0', '0.00', '1e3', '12.345', '1 000', '1,000.00', '+5', '99999999999']);

it('refuses a fee without a name, a repeated name, and an empty list', function () {
    ($this->create)(['items' => [['name' => '', 'amount' => '100']]])->assertSessionHasErrors();
    ($this->create)(['items' => [['name' => 'Tuition', 'amount' => '1'], ['name' => 'tuition', 'amount' => '2']]])->assertSessionHasErrors('items');
    ($this->create)(['items' => []])->assertSessionHasErrors('items');
    ($this->create)(['items' => [['name' => '', 'amount' => '']]])->assertSessionHasErrors('items');

    expect(($this->inSchool)(fn () => FeeStructure::query()->count()))->toBe(0);
});

it('ignores spare empty lines of the form', function () {
    ($this->create)(['items' => [['name' => 'Tuition', 'amount' => '100'], ['name' => '', 'amount' => ''], ['name' => 'Bus', 'amount' => '50']]])
        ->assertSessionHasNoErrors();

    expect(($this->inSchool)(fn () => FeeStructure::query()->firstOrFail()->items()->count()))->toBe(2);
});

it('keeps a structure name unique per term and class', function () {
    ($this->create)()->assertSessionHasNoErrors();
    ($this->create)()->assertSessionHasErrors('name');
    ($this->create)(['school_class_id' => $this->w['form2']->id])->assertSessionHasNoErrors();
    ($this->create)(['term_id' => $this->w['term2']->id])->assertSessionHasNoErrors();

    expect(($this->inSchool)(fn () => FeeStructure::query()->count()))->toBe(3);
});

it('refuses a term or class of another school', function () {
    $foreign = makeExamWorld($this->other);

    ($this->create)(['term_id' => $foreign['term']->id])->assertSessionHasErrors('term_id');
    ($this->create)(['school_class_id' => $foreign['form1']->id])->assertSessionHasErrors('school_class_id');

    expect(($this->inSchool)(fn () => FeeStructure::query()->count()))->toBe(0);
});

it('ignores tenant and other fields a client adds', function () {
    ($this->create)(['tenant_id' => $this->other->id, 'academic_year_id' => 999, 'id' => 77])->assertSessionHasNoErrors();

    $structure = ($this->inSchool)(fn () => FeeStructure::query()->firstOrFail());
    expect($structure->tenant_id)->toBe($this->school->id)->and($structure->academic_year_id)->toBe($this->w['year']->id)->and($structure->id)->not->toBe(77);
});

it('replaces the items of a structure no invoice was made from', function () {
    $structure = ($this->structure)();

    $this->actingAs($this->accountant)->put("{$this->url}/fees/structures/{$structure->id}", ($this->payload)(['name' => 'Renamed', 'items' => [['name' => 'Only', 'amount' => '10']]]))
        ->assertSessionHasNoErrors()->assertRedirect();

    $fresh = ($this->inSchool)(fn () => FeeStructure::query()->with('items')->findOrFail($structure->id));
    expect($fresh->name)->toBe('Renamed')->and($fresh->items->pluck('amount', 'name')->all())->toBe(['Only' => 1000]);
});

it('never changes or deletes a structure that invoices were made from', function () {
    $structure = ($this->structure)();
    ($this->place)('Locked');
    ($this->generate)($structure)->assertSessionHasNoErrors();

    $this->actingAs($this->accountant)->put("{$this->url}/fees/structures/{$structure->id}", ($this->payload)(['items' => [['name' => 'Cheaper', 'amount' => '1']]]))
        ->assertSessionHasErrors('name');
    $this->actingAs($this->accountant)->delete("{$this->url}/fees/structures/{$structure->id}")->assertSessionHasErrors('delete');

    $fresh = ($this->inSchool)(fn () => FeeStructure::query()->with('items')->findOrFail($structure->id));
    expect($fresh->items->pluck('amount', 'name')->all())->toBe(['Tuition' => 15000000, 'Uniform' => 2500050]);
});

it('deletes a structure without invoices', function () {
    $structure = ($this->structure)();

    $this->actingAs($this->accountant)->delete("{$this->url}/fees/structures/{$structure->id}")->assertRedirect("{$this->url}/fees/structures");

    expect(($this->inSchool)(fn () => FeeStructure::query()->count()))->toBe(0)
        ->and(($this->inSchool)(fn () => AuditLog::where('action', 'fee_structure.deleted')->count()))->toBe(1);
});

it('makes an invoice for each active student of the class, with a copy of the lines', function () {
    $structure = ($this->structure)();
    $a = ($this->place)('Aone');
    $b = ($this->place)('Btwo');
    ($this->place)('Elsewhere', $this->w['form2']);
    $left = ($this->place)('Gone');
    ($this->inSchool)(fn () => $left->forceFill(['status' => 'left'])->save());

    ($this->generate)($structure)->assertSessionHasNoErrors()->assertRedirect();

    $invoices = ($this->invoices)();
    expect($invoices)->toHaveCount(2)
        ->and($invoices->pluck('student_id')->sort()->values()->all())->toBe(collect([$a->id, $b->id])->sort()->values()->all())
        ->and($invoices->pluck('number')->all())->toBe([1, 2])
        ->and($invoices->every(fn ($i) => $i->total === 17500050 && $i->currency === 'TZS' && $i->tenant_id === $this->school->id && $i->term_id === $this->w['term']->id && $i->school_class_id === $this->w['form1']->id))->toBeTrue()
        ->and($invoices[0]->reference())->toBe('INV-000001');

    $lines = ($this->inSchool)(fn () => $invoices[0]->lines()->get());
    expect($lines->pluck('amount', 'name')->all())->toBe(['Tuition' => 15000000, 'Uniform' => 2500050])
        ->and($lines->sum('amount'))->toBe($invoices[0]->total);
});

it('never makes a second invoice when generation is repeated, and numbers a latecomer next', function () {
    $structure = ($this->structure)();
    ($this->place)('First');
    ($this->place)('Second');

    ($this->generate)($structure)->assertSessionHasNoErrors();
    ($this->generate)($structure)->assertSessionHasNoErrors();
    ($this->generate)($structure)->assertSessionHasNoErrors();
    expect(($this->invoices)())->toHaveCount(2);

    ($this->place)('Latecomer');
    ($this->generate)($structure)->assertSessionHasNoErrors();

    expect(($this->invoices)()->pluck('number')->all())->toBe([1, 2, 3]);
});

it('invoices only the stream asked for', function () {
    $structure = ($this->structure)();
    $a = ($this->place)('Sa', null, $this->w['streamA']);
    ($this->place)('Sb', null, $this->w['streamB']);

    ($this->generate)($structure, ['stream_id' => $this->w['streamA']->id])->assertSessionHasNoErrors();

    $invoices = ($this->invoices)();
    expect($invoices)->toHaveCount(1)->and($invoices[0]->student_id)->toBe($a->id)->and($invoices[0]->stream_id)->toBe($this->w['streamA']->id);
});

it('refuses a stream of another class or school when making invoices', function () {
    $structure = ($this->structure)();
    $foreign = makeExamWorld($this->other);
    $form2Stream = ($this->inSchool)(fn () => Stream::factory()->create(['school_class_id' => $this->w['form2']->id, 'name' => 'Z']));
    ($this->place)('Nobody');

    ($this->generate)($structure, ['stream_id' => $foreign['streamA']->id])->assertSessionHasErrors('stream_id');
    ($this->generate)($structure, ['stream_id' => $form2Stream->id])->assertSessionHasErrors('stream_id');

    expect(($this->invoices)())->toHaveCount(0);
});

it('numbers invoices per school, each starting at 1', function () {
    $structure = ($this->structure)();
    ($this->place)('Mine');
    ($this->generate)($structure)->assertSessionHasNoErrors();

    $foreign = makeExamWorld($this->other);
    TenantContext::run($this->other, function () use ($foreign) {
        $theirs = FeeStructure::factory()->create(['academic_year_id' => $foreign['year']->id, 'term_id' => $foreign['term']->id, 'school_class_id' => $foreign['form1']->id]);
        $item = new FeeItem(['name' => 'Fee', 'amount' => 100]);
        $item->fee_structure_id = $theirs->id;
        $item->save();
        $student = Student::factory()->create();
        Enrollment::factory()->create(['student_id' => $student->id, 'academic_year_id' => $foreign['year']->id, 'school_class_id' => $foreign['form1']->id]);
        app(GenerateInvoices::class)->handle($theirs);
    });

    expect(($this->invoices)()->pluck('number')->all())->toBe([1])
        ->and(TenantContext::run($this->other, fn () => Invoice::query()->pluck('number')->all()))->toBe([1]);
});

it('gives numbers without gaps, and a rolled back number is given again', function () {
    $numbers = fn () => ($this->inSchool)(function () {
        $next = app(NextNumber::class);
        $first = $next->handle('invoice');
        try {
            DB::transaction(function () use ($next) {
                $next->handle('invoice');
                throw new RuntimeException('fails after taking a number');
            });
        } catch (RuntimeException) {
        }

        return [$first, $next->handle('invoice'), $next->handle('invoice')];
    });

    expect($numbers())->toBe([1, 2, 3]);
});

it('keeps kinds of numbers apart', function () {
    expect(($this->inSchool)(function () {
        $next = app(NextNumber::class);

        return [$next->handle('invoice'), $next->handle('receipt'), $next->handle('invoice'), $next->handle('receipt')];
    }))->toBe([1, 1, 2, 2]);
});

it('stops a second invoice of the same structure for a student at the database too', function () {
    $structure = ($this->structure)();
    $student = ($this->place)('Dup');
    ($this->generate)($structure);
    $invoice = ($this->invoices)()->first();

    expect(fn () => ($this->inSchool)(function () use ($invoice, $structure, $student) {
        $copy = new Invoice;
        $copy->forceFill([
            'number' => 99, 'student_id' => $student->id, 'academic_year_id' => $invoice->academic_year_id, 'term_id' => $invoice->term_id,
            'fee_structure_id' => $structure->id, 'school_class_id' => $invoice->school_class_id, 'currency' => 'TZS', 'total' => 1, 'issued_on' => now(),
        ])->save();
    }))->toThrow(QueryException::class);
});

it('uses the school currency and its decimals', function () {
    $this->school->forceFill(['currency' => 'UGX'])->save();

    ($this->create)(['items' => [['name' => 'Tuition', 'amount' => '1500.50']]])->assertSessionHasErrors('items');
    ($this->create)(['items' => [['name' => 'Tuition', 'amount' => '1500']]])->assertSessionHasNoErrors();
    ($this->place)('Ugandan');
    $structure = ($this->inSchool)(fn () => FeeStructure::query()->firstOrFail());
    ($this->generate)($structure)->assertSessionHasNoErrors();

    $invoice = ($this->invoices)()->first();
    expect($invoice->currency)->toBe('UGX')->and($invoice->total)->toBe(1500);
    $this->actingAs($this->accountant)->get("{$this->url}/fees/invoices/{$invoice->id}")->assertOk()->assertSee('UGX 1,500');
});

it('shows structures and invoices with formatted amounts', function () {
    $structure = ($this->structure)(['name' => 'Showme']);
    ($this->place)('Shown');
    ($this->generate)($structure);
    $invoice = ($this->invoices)()->first();

    $this->actingAs($this->accountant)->get("{$this->url}/fees/structures")->assertOk()->assertSee('Showme')->assertSee('TZS 175,000.50');
    $this->actingAs($this->accountant)->get("{$this->url}/fees/structures/{$structure->id}")->assertOk()->assertSee('TZS 150,000.00')->assertSee('TZS 25,000.50');
    $this->actingAs($this->accountant)->get("{$this->url}/fees/invoices")->assertOk()->assertSee('INV-000001')->assertSee('Shown');
    $this->actingAs($this->accountant)->get("{$this->url}/fees/invoices/{$invoice->id}")->assertOk()->assertSee('INV-000001')->assertSee('Tuition')->assertSee('TZS 175,000.50');
});

it('filters the invoice list by term and class', function () {
    $s1 = ($this->structure)(['name' => 'T1F1']);
    $s2 = ($this->structure)(['name' => 'T1F2', 'school_class_id' => $this->w['form2']->id]);
    ($this->place)('Inform1');
    ($this->place)('Inform2', $this->w['form2']);
    ($this->generate)($s1);
    ($this->generate)($s2);

    $this->actingAs($this->accountant)->get("{$this->url}/fees/invoices?school_class_id={$this->w['form2']->id}")
        ->assertOk()->assertSee('Inform2')->assertDontSee('Inform1');
    $this->actingAs($this->accountant)->get("{$this->url}/fees/invoices?term_id={$this->w['term2']->id}")
        ->assertOk()->assertDontSee('Inform1')->assertDontSee('Inform2')->assertSee(__('fees.invoices.empty'));
});

it('refuses a filter that names a term or class of another school', function () {
    $foreign = makeExamWorld($this->other);

    $this->actingAs($this->accountant)->get("{$this->url}/fees/invoices?term_id={$foreign['term']->id}")->assertSessionHasErrors('term_id');
    $this->actingAs($this->accountant)->get("{$this->url}/fees/invoices?school_class_id={$foreign['form1']->id}")->assertSessionHasErrors('school_class_id');
});

it('answers 404 for the structure or invoice of another school and shows none of its invoices', function () {
    $foreign = makeExamWorld($this->other);
    [$theirs, $invoice] = TenantContext::run($this->other, function () use ($foreign) {
        $structure = FeeStructure::factory()->create(['name' => 'Theirs', 'academic_year_id' => $foreign['year']->id, 'term_id' => $foreign['term']->id, 'school_class_id' => $foreign['form1']->id]);
        $item = new FeeItem(['name' => 'Fee', 'amount' => 100]);
        $item->fee_structure_id = $structure->id;
        $item->save();
        $student = Student::factory()->create(['first_name' => 'Theirstudent']);
        Enrollment::factory()->create(['student_id' => $student->id, 'academic_year_id' => $foreign['year']->id, 'school_class_id' => $foreign['form1']->id]);
        app(GenerateInvoices::class)->handle($structure);

        return [$structure, Invoice::query()->firstOrFail()];
    });

    $this->actingAs($this->accountant);
    $this->get("{$this->url}/fees/structures/{$theirs->id}")->assertNotFound();
    $this->get("{$this->url}/fees/structures/{$theirs->id}/edit")->assertNotFound();
    $this->get("{$this->url}/fees/invoices/{$invoice->id}")->assertNotFound();
    $this->post("{$this->url}/fees/structures/{$theirs->id}/invoices")->assertNotFound();
    $this->put("{$this->url}/fees/invoices/{$invoice->id}/control-number", ['control_number' => '123456789012'])->assertNotFound();
    $this->get("{$this->url}/fees/invoices")->assertOk()->assertDontSee('Theirstudent');
    $this->get("{$this->url}/fees/structures")->assertOk()->assertDontSee('Theirs');
});

it('sets, changes and clears a control number', function () {
    $structure = ($this->structure)();
    ($this->place)('Pay');
    ($this->generate)($structure);
    $invoice = ($this->invoices)()->first();
    $put = fn (?string $number, ?object $user = null) => $this->actingAs($user ?? $this->accountant)
        ->put("{$this->url}/fees/invoices/{$invoice->id}/control-number", ['control_number' => $number]);
    $current = fn () => ($this->inSchool)(fn () => Invoice::query()->findOrFail($invoice->id)->control_number);

    $put('991234567890')->assertSessionHasNoErrors();
    expect($current())->toBe('991234567890');
    $put(' 12345678 ')->assertSessionHasNoErrors();
    expect($current())->toBe('12345678');
    $put('')->assertSessionHasNoErrors();
    expect($current())->toBeNull()
        ->and(($this->inSchool)(fn () => AuditLog::where('action', 'invoice.control_number_set')->where('subject_id', $invoice->id)->count()))->toBe(3);
});

it('refuses control numbers that are not 8 to 20 digits', function (string $number) {
    $structure = ($this->structure)();
    ($this->place)('Bad');
    ($this->generate)($structure);
    $invoice = ($this->invoices)()->first();

    $this->actingAs($this->accountant)->put("{$this->url}/fees/invoices/{$invoice->id}/control-number", ['control_number' => $number])
        ->assertSessionHasErrors('control_number');

    expect(($this->invoices)()->first()->control_number)->toBeNull();
})->with(['1234567', '123456789012345678901', '12345678a', '1234 5678', '-12345678', '1e1234567', 'abcdefgh']);

it('keeps a control number to one invoice in a school but allows it in another school', function () {
    $structure = ($this->structure)();
    ($this->place)('One');
    ($this->place)('Two');
    ($this->generate)($structure);
    [$first, $second] = ($this->invoices)();
    $put = fn ($invoice, string $number) => $this->actingAs($this->accountant)->put("{$this->url}/fees/invoices/{$invoice->id}/control-number", ['control_number' => $number]);

    $put($first, '991234567890')->assertSessionHasNoErrors();
    $put($second, '991234567890')->assertSessionHasErrors('control_number');
    $put($first, '991234567890')->assertSessionHasNoErrors(); // the same invoice may keep its own

    $foreign = makeExamWorld($this->other);
    $ok = TenantContext::run($this->other, function () use ($foreign) {
        $structure = FeeStructure::factory()->create(['academic_year_id' => $foreign['year']->id, 'term_id' => $foreign['term']->id, 'school_class_id' => $foreign['form1']->id]);
        $item = new FeeItem(['name' => 'Fee', 'amount' => 100]);
        $item->fee_structure_id = $structure->id;
        $item->save();
        $student = Student::factory()->create();
        Enrollment::factory()->create(['student_id' => $student->id, 'academic_year_id' => $foreign['year']->id, 'school_class_id' => $foreign['form1']->id]);
        app(GenerateInvoices::class)->handle($structure);

        return app(SetControlNumber::class)->handle(Invoice::query()->firstOrFail(), '991234567890')->control_number;
    });

    expect($ok)->toBe('991234567890')->and(($this->invoices)()[1]->control_number)->toBeNull();
});

it('lets only the right roles see and change fees', function (string $role, bool $view, bool $manage) {
    $user = userWithRole($this->school, $role);
    $structure = ($this->structure)();
    ($this->place)('Roleone');
    ($this->generate)($structure);
    $invoice = ($this->invoices)()->first();
    $this->actingAs($user);

    $check = fn (TestResponse $r, bool $allowed) => $allowed ? $r->assertSuccessful() : $r->assertForbidden();

    $check($this->get("{$this->url}/fees/structures"), $view);
    $check($this->get("{$this->url}/fees/structures/{$structure->id}"), $view);
    $check($this->get("{$this->url}/fees/invoices"), $view);
    $check($this->get("{$this->url}/fees/invoices/{$invoice->id}"), $view);
    $manage ? $this->get("{$this->url}/fees/structures/create")->assertRedirect() : $this->get("{$this->url}/fees/structures/create")->assertForbidden();
    $check($this->get("{$this->url}/fees/structures/{$structure->id}/edit"), $manage);

    $manage ? $this->post("{$this->url}/fees/structures", ($this->payload)(['name' => 'ByRole']))->assertRedirect() : $this->post("{$this->url}/fees/structures", ($this->payload)(['name' => 'ByRole']))->assertForbidden();
    $manage ? $this->post("{$this->url}/fees/structures/{$structure->id}/invoices")->assertRedirect() : $this->post("{$this->url}/fees/structures/{$structure->id}/invoices")->assertForbidden();
    $manage ? $this->put("{$this->url}/fees/invoices/{$invoice->id}/control-number", ['control_number' => '12345678'])->assertRedirect()
        : $this->put("{$this->url}/fees/invoices/{$invoice->id}/control-number", ['control_number' => '12345678'])->assertForbidden();
    $this->delete("{$this->url}/fees/structures/{$structure->id}")->assertStatus($manage ? 302 : 403);
})->with([
    'owner' => ['owner', true, true],
    'accountant' => ['accountant', true, true],
    'head teacher' => ['head_teacher', true, false],
    'teacher' => ['teacher', false, false],
    'parent' => ['parent_student', false, false],
]);

it('keeps guests out', function () {
    $structure = ($this->structure)();

    $this->get("{$this->url}/fees/structures")->assertRedirect("{$this->url}/login");
    $this->get("{$this->url}/fees/invoices")->assertRedirect("{$this->url}/login");
    $this->post("{$this->url}/fees/structures/{$structure->id}/invoices")->assertRedirect("{$this->url}/login");
    $this->post("{$this->url}/fees/structures", [])->assertRedirect("{$this->url}/login");
});

it('lets a read-only school look at fees but not change them', function () {
    $structure = ($this->structure)(['name' => 'Readable']);
    ($this->place)('Readonlyone');
    ($this->generate)($structure);
    $invoice = ($this->invoices)()->first();
    $this->school->update(['subscription_status' => SubscriptionStatus::ReadOnly]);
    $accountant = userWithRole($this->school, 'accountant');

    $this->actingAs($accountant)->get("{$this->url}/fees/structures")->assertOk()->assertSee('Readable');
    $this->actingAs($accountant)->get("{$this->url}/fees/invoices/{$invoice->id}")->assertOk()->assertSee('INV-000001');
    $this->actingAs($accountant)->post("{$this->url}/fees/structures", ($this->payload)())->assertForbidden();
    ($this->generate)($structure, [], $accountant)->assertForbidden();
    $this->actingAs($accountant)->put("{$this->url}/fees/invoices/{$invoice->id}/control-number", ['control_number' => '12345678'])->assertForbidden();
    $this->actingAs($accountant)->delete("{$this->url}/fees/structures/{$structure->id}")->assertForbidden();

    expect(($this->invoices)())->toHaveCount(1)->and(($this->invoices)()->first()->control_number)->toBeNull();
});

it('writes fee actions to the audit log with ids only', function () {
    ($this->create)(['name' => 'Audited fees'])->assertSessionHasNoErrors();
    $structure = ($this->inSchool)(fn () => FeeStructure::query()->firstOrFail());
    ($this->place)('Auditedkid');
    ($this->generate)($structure);
    ($this->generate)($structure); // creates nothing, so logs nothing
    $invoice = ($this->invoices)()->first();
    $this->actingAs($this->accountant)->get("{$this->url}/fees/invoices/{$invoice->id}")->assertOk();

    $logs = ($this->inSchool)(fn () => AuditLog::query()->whereIn('action', ['fee_structure.created', 'invoices.generated', 'invoice.viewed'])->orderBy('id')->get());
    expect($logs->groupBy('action')->map->count()->all())->toBe(['fee_structure.created' => 1, 'invoices.generated' => 1, 'invoice.viewed' => 1])
        ->and($logs->firstWhere('action', 'invoice.viewed')->subject_id)->toBe($invoice->id)
        ->and($logs->every(fn ($l) => $l->user_id === $this->accountant->id))->toBeTrue()
        ->and(json_encode($logs->toArray()))->not->toContain('Auditedkid')->not->toContain('Audited fees');
});

it('does not log a refused viewing of an invoice', function () {
    $structure = ($this->structure)();
    ($this->place)('Refusedkid');
    ($this->generate)($structure);
    $invoice = ($this->invoices)()->first();

    $this->actingAs(userWithRole($this->school, 'teacher'))->get("{$this->url}/fees/invoices/{$invoice->id}")->assertForbidden();

    expect(($this->inSchool)(fn () => AuditLog::where('action', 'invoice.viewed')->count()))->toBe(0);
});

it('shows no guardian data on an invoice', function () {
    $structure = ($this->structure)();
    $student = ($this->place)('Privatekid');
    ($this->inSchool)(function () use ($student) {
        $guardian = Guardian::factory()->create(['name' => 'Secretguardian', 'phone' => '+255700111222']);
        $student->guardians()->attach($guardian->id, ['relationship' => 'mother', 'is_primary' => true]);
    });
    ($this->generate)($structure);
    $invoice = ($this->invoices)()->first();

    $this->actingAs($this->accountant)->get("{$this->url}/fees/invoices/{$invoice->id}")->assertOk()->assertDontSee('Secretguardian')->assertDontSee('255700111222');
    $this->actingAs($this->accountant)->get("{$this->url}/fees/invoices")->assertOk()->assertDontSee('Secretguardian');
});

it('refuses to delete a term or class that has a fee structure', function () {
    ($this->structure)();

    foreach ([$this->w['term'], $this->w['form1']] as $record) {
        expect(fn () => ($this->inSchool)(fn () => app(DeleteAcademicRecord::class)->handle($record->fresh())))->toThrow(ValidationException::class);
    }
    expect(($this->inSchool)(fn () => Term::query()->whereKey($this->w['term']->id)->exists()))->toBeTrue();
});

it('keeps invoices when the structure rows could be changed behind the scenes', function () {
    $structure = ($this->structure)();
    ($this->place)('Snapshot');
    ($this->generate)($structure);

    // Even if an item were changed directly, the invoice keeps its own copy of lines and total.
    ($this->inSchool)(fn () => FeeItem::query()->where('fee_structure_id', $structure->id)->update(['amount' => 1]));

    $invoice = ($this->invoices)()->first();
    expect($invoice->total)->toBe(17500050)
        ->and(($this->inSchool)(fn () => $invoice->lines()->sum('amount')))->toBe(17500050);
});

it('shows the pages in Swahili', function () {
    ($this->structure)();

    $this->actingAs($this->accountant)->withSession(['locale' => 'sw'])->get("{$this->url}/fees/structures")
        ->assertOk()->assertSee('Miundo ya Ada')->assertSee('Muundo wa ada');
});

it('lists the fee menu only for those who may see it', function () {
    $this->actingAs($this->accountant)->get("{$this->url}/")->assertOk()->assertSee(route('fees.invoices.index', [], false), false);
    $this->actingAs(userWithRole($this->school, 'teacher'))->get("{$this->url}/")->assertOk()->assertDontSee('/fees/invoices', false);
});

it('gives the new permissions to the right roles', function () {
    $can = function (string $role, string $permission) {
        $user = userWithRole($this->school, $role);

        return ($this->inSchool)(fn () => $user->can($permission));
    };

    expect($can('owner', 'fees.view'))->toBeTrue()->and($can('owner', 'fees.manage'))->toBeTrue()
        ->and($can('accountant', 'fees.manage'))->toBeTrue()
        ->and($can('head_teacher', 'fees.view'))->toBeTrue()->and($can('head_teacher', 'fees.manage'))->toBeFalse()
        ->and($can('teacher', 'fees.view'))->toBeFalse()
        ->and($can('parent_student', 'fees.view'))->toBeFalse();
});
