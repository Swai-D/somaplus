<?php

use App\Domain\Students\AdmissionNumbers;
use App\Enums\SubscriptionStatus;
use App\Models\AdmissionSetting;
use App\Models\AuditLog;
use App\Models\DocumentSequence;
use App\Models\Student;
use App\Models\Tenant;
use App\Tenancy\TenantContext;
use Illuminate\Http\UploadedFile;

beforeEach(function () {
    useTestHosts();

    $this->school = Tenant::factory()->create(['slug' => 'acme']);
    $this->other = Tenant::factory()->create(['slug' => 'beta']);
    $this->url = 'http://acme.somaplus.test';
    $this->owner = userWithRole($this->school, 'owner');

    $this->inSchool = fn (Closure $work, ?Tenant $school = null) => TenantContext::run($school ?? $this->school, $work);

    $this->useAuto = fn (array $override = [], ?Tenant $school = null) => ($this->inSchool)(
        fn () => AdmissionSetting::create($override + ['mode' => 'auto', 'prefix' => 'SCH', 'separator' => '-', 'year_format' => 'YYYY', 'digits' => 4]),
        $school
    );

    $this->valid = fn (array $override = []) => $override + [
        'first_name' => 'Amina', 'last_name' => 'Juma', 'gender' => 'female',
        'date_of_birth' => '2012-05-06', 'admitted_on' => '2026-01-10',
    ];

    $this->numbers = fn (?Tenant $school = null) => ($this->inSchool)(fn () => Student::orderBy('id')->pluck('admission_number')->all(), $school);
});

it('renders every format', function (array $setting, string $expected) {
    expect(AdmissionNumbers::render(new AdmissionSetting($setting + ['prefix' => 'SCH', 'separator' => '-', 'year_format' => 'YYYY', 'digits' => 4]), 2026, 7))->toBe($expected);
})->with([
    'prefix, year and four digits' => [[], 'SCH-2026-0007'],
    'short year with slash' => [['year_format' => 'YY', 'separator' => '/'], 'SCH/26/0007'],
    'no year' => [['year_format' => 'none'], 'SCH-0007'],
    'no prefix' => [['prefix' => ''], '2026-0007'],
    'no separator' => [['separator' => ''], 'SCH20260007'],
    'only a running number' => [['prefix' => '', 'year_format' => 'none', 'digits' => 3], '007'],
    'number longer than the digits' => [['digits' => 1], 'SCH-2026-7'],
]);

it('keeps typing by hand for a school that never chose, and shows the field', function () {
    // The form of a new student is a pop-up of the list, so the field is on that page.
    $this->actingAs($this->owner)->get("{$this->url}/students")->assertOk()->assertSee('name="admission_number"', false);

    $this->actingAs($this->owner)->post("{$this->url}/students", ($this->valid)())->assertSessionHasErrors('admission_number');
    $this->actingAs($this->owner)->post("{$this->url}/students", ($this->valid)(['admission_number' => 'OLD-1']))->assertSessionHasNoErrors();

    expect(($this->numbers)())->toBe(['OLD-1']);
});

it('lets the owner choose the format and writes an audit line', function () {
    $this->actingAs($this->owner)->get("{$this->url}/settings/admission-numbers")->assertOk()->assertSee('Admission numbers');

    $this->actingAs($this->owner)->put("{$this->url}/settings/admission-numbers", [
        'mode' => 'auto', 'prefix' => 'sch', 'separator' => 'none', 'year_format' => 'YY', 'digits' => 5,
    ])->assertSessionHasNoErrors()->assertRedirect();

    ($this->inSchool)(function () {
        $setting = AdmissionSetting::firstOrFail();
        expect($setting->mode)->toBe('auto')->and($setting->prefix)->toBe('SCH')->and($setting->separator)->toBe('')
            ->and($setting->year_format)->toBe('YY')->and($setting->digits)->toBe(5)
            ->and($setting->tenant_id)->toBe($this->school->id)
            ->and(AuditLog::where('action', 'settings.admission_updated')->where('user_id', $this->owner->id)->exists())->toBeTrue();
    });

    $this->actingAs($this->owner)->put("{$this->url}/settings/admission-numbers", ['mode' => 'manual'])->assertSessionHasNoErrors();
    expect(($this->inSchool)(fn () => AdmissionSetting::count()))->toBe(1)
        ->and(($this->inSchool)(fn () => AdmissionSetting::first()->mode))->toBe('manual');
});

it('refuses bad format settings', function (array $input, string $field) {
    $this->actingAs($this->owner)->put("{$this->url}/settings/admission-numbers", $input + [
        'mode' => 'auto', 'prefix' => 'SCH', 'separator' => '-', 'year_format' => 'YYYY', 'digits' => 4,
    ])->assertSessionHasErrors($field);

    expect(($this->inSchool)(fn () => AdmissionSetting::count()))->toBe(0);
})->with([
    'unknown mode' => [['mode' => 'random'], 'mode'],
    'prefix with a space' => [['prefix' => 'S C'], 'prefix'],
    'prefix too long' => [['prefix' => 'ABCDEFGHIJK'], 'prefix'],
    'unknown separator' => [['separator' => '#'], 'separator'],
    'unknown year' => [['year_format' => '2026'], 'year_format'],
    'too many digits' => [['digits' => 9], 'digits'],
    'no digits' => [['digits' => 0], 'digits'],
    'start below one' => [['start_at' => 0], 'start_at'],
]);

it('lets only the owner open or change the settings', function (string $role) {
    $user = userWithRole($this->school, $role);

    $this->actingAs($user)->get("{$this->url}/settings/admission-numbers")->assertForbidden();
    $this->actingAs($user)->put("{$this->url}/settings/admission-numbers", ['mode' => 'manual'])->assertForbidden();
    expect(($this->inSchool)(fn () => AdmissionSetting::count()))->toBe(0);
})->with(['head_teacher', 'teacher', 'accountant', 'parent_student']);

it('shows the page in Swahili and lists it in the owner menu only', function () {
    $this->actingAs($this->owner)->withSession(['locale' => 'sw'])->get("{$this->url}/settings/admission-numbers")->assertOk()->assertSee('Namba za usajili');
    $this->actingAs($this->owner)->get($this->url)->assertSee(route('settings.admission.index', [], false));
    $this->actingAs(userWithRole($this->school, 'head_teacher'))->get($this->url)->assertDontSee(route('settings.admission.index', [], false));
});

it('makes the number when a student is added, ignores a typed one, and counts one by one', function () {
    ($this->useAuto)();

    $this->actingAs($this->owner)->get("{$this->url}/students")->assertOk()->assertSee('SCH-'.now()->year.'-0001')->assertDontSee('name="admission_number"', false);

    $this->actingAs($this->owner)->post("{$this->url}/students", ($this->valid)(['admission_number' => 'HACK-1']))->assertSessionHasNoErrors();
    $this->actingAs($this->owner)->post("{$this->url}/students", ($this->valid)(['first_name' => 'Baraka']))->assertSessionHasNoErrors();

    expect(($this->numbers)())->toBe(['SCH-2026-0001', 'SCH-2026-0002']);
});

it('starts again from 1 each year when the format holds the year', function () {
    ($this->useAuto)();

    $this->actingAs($this->owner)->post("{$this->url}/students", ($this->valid)(['admitted_on' => '2026-03-01']));
    $this->actingAs($this->owner)->post("{$this->url}/students", ($this->valid)(['admitted_on' => '2027-01-05']));
    $this->actingAs($this->owner)->post("{$this->url}/students", ($this->valid)(['admitted_on' => '2026-09-01']));

    expect(($this->numbers)())->toBe(['SCH-2026-0001', 'SCH-2027-0001', 'SCH-2026-0002']);
});

it('keeps counting through the years when the format has no year', function () {
    ($this->useAuto)(['year_format' => 'none', 'separator' => '/', 'digits' => 3]);

    $this->actingAs($this->owner)->post("{$this->url}/students", ($this->valid)(['admitted_on' => '2026-03-01']));
    $this->actingAs($this->owner)->post("{$this->url}/students", ($this->valid)(['admitted_on' => '2027-01-05']));

    expect(($this->numbers)())->toBe(['SCH/001', 'SCH/002']);
});

it('skips a number that a student already has', function () {
    ($this->useAuto)();
    ($this->inSchool)(fn () => Student::factory()->create(['admission_number' => 'SCH-2026-0001']));
    ($this->inSchool)(fn () => Student::factory()->create(['admission_number' => 'SCH-2026-0002']));

    $this->actingAs($this->owner)->post("{$this->url}/students", ($this->valid)())->assertSessionHasNoErrors();

    expect(($this->numbers)())->toBe(['SCH-2026-0001', 'SCH-2026-0002', 'SCH-2026-0003']);
});

it('does not change the number of a student who is edited', function () {
    ($this->useAuto)();
    $this->actingAs($this->owner)->post("{$this->url}/students", ($this->valid)());
    $student = ($this->inSchool)(fn () => Student::firstOrFail());

    $this->actingAs($this->owner)->get("{$this->url}/students/{$student->id}/edit")->assertOk()->assertSee('readonly', false);
    $this->actingAs($this->owner)->put("{$this->url}/students/{$student->id}", ($this->valid)(['first_name' => 'Changed', 'admission_number' => 'HACK-2']))->assertSessionHasNoErrors();

    ($this->inSchool)(fn () => expect(Student::firstOrFail()->only(['first_name', 'admission_number']))->toBe(['first_name' => 'Changed', 'admission_number' => 'SCH-2026-0001']));
});

it('moves the count forward with "start at" but never back', function () {
    $save = fn (array $input) => $this->actingAs($this->owner)->put("{$this->url}/settings/admission-numbers", $input + [
        'mode' => 'auto', 'prefix' => 'SCH', 'separator' => '-', 'year_format' => 'none', 'digits' => 4,
    ])->assertSessionHasNoErrors();

    $save(['start_at' => 250]);
    $this->actingAs($this->owner)->post("{$this->url}/students", ($this->valid)());
    $save(['start_at' => 10]);
    $this->actingAs($this->owner)->post("{$this->url}/students", ($this->valid)());

    expect(($this->numbers)())->toBe(['SCH-0250', 'SCH-0251'])
        ->and(($this->inSchool)(fn () => DocumentSequence::where('type', 'admission')->value('last_number')))->toBe(251);
});

it('keeps schools apart: each has its own format and its own count', function () {
    ($this->useAuto)();
    ($this->useAuto)(['prefix' => 'BETA', 'separator' => '/'], $this->other);
    $ownerB = userWithRole($this->other, 'owner');

    $this->actingAs($this->owner)->post("{$this->url}/students", ($this->valid)());
    $this->actingAs($ownerB)->post('http://beta.somaplus.test/students', ($this->valid)());
    $this->actingAs($ownerB)->post('http://beta.somaplus.test/students', ($this->valid)());

    expect(($this->numbers)())->toBe(['SCH-2026-0001'])->and(($this->numbers)($this->other))->toBe(['BETA/2026/0001', 'BETA/2026/0002']);
});

it('is read-only for a lapsed school: the page opens, saving is refused', function () {
    $this->school->update(['subscription_status' => SubscriptionStatus::ReadOnly]);
    $owner = userWithRole($this->school, 'owner');

    $this->actingAs($owner)->get("{$this->url}/settings/admission-numbers")->assertOk();
    $this->actingAs($owner)->put("{$this->url}/settings/admission-numbers", ['mode' => 'manual'])->assertForbidden();
});

describe('bulk import', function () {
    $header = 'admission_number,first_name,middle_name,last_name,gender,date_of_birth,admitted_on,class,stream,guardian_name,guardian_phone,guardian_email,guardian_relationship';

    it('makes the numbers for empty cells, keeps typed ones and never repeats them', function () use ($header) {
        ($this->useAuto)(['year_format' => 'none']);
        $file = UploadedFile::fake()->createWithContent('s.csv', implode("\n", [
            $header,
            ',Amina,,Juma,female,2012-05-06,2026-01-10,,,,,,',
            'SCH-0002,Old,,Register,male,2011-05-06,2026-01-10,,,,,,',
            ',Baraka,,Mushi,male,2012-05-06,2026-01-10,,,,,,',
        ])."\n");

        $this->actingAs($this->owner)->post("{$this->url}/students/import", ['file' => $file])->assertSessionHas('import_result');

        expect(($this->numbers)())->toBe(['SCH-0001', 'SCH-0002', 'SCH-0003']);
    });

    it('accepts a file without the admission number column when Somaplus makes the numbers', function () {
        ($this->useAuto)();
        $file = UploadedFile::fake()->createWithContent('s.csv', "first_name,last_name,gender,date_of_birth,admitted_on\nAmina,Juma,female,2012-05-06,2026-01-10\n");

        $this->actingAs($this->owner)->post("{$this->url}/students/import", ['file' => $file])->assertSessionHas('import_result');

        expect(($this->numbers)())->toBe(['SCH-2026-0001']);
    });

    it('still refuses a typed number that is already used, and adds nothing', function () use ($header) {
        ($this->useAuto)();
        ($this->inSchool)(fn () => Student::factory()->create(['admission_number' => 'OLD-9']));
        $file = UploadedFile::fake()->createWithContent('s.csv', "$header\n,Amina,,Juma,female,2012-05-06,2026-01-10,,,,,,\nOLD-9,Dup,,Number,male,2012-05-06,2026-01-10,,,,,,\n");

        $this->actingAs($this->owner)->post("{$this->url}/students/import", ['file' => $file])->assertSessionHas('import_total', 1);

        expect(($this->numbers)())->toBe(['OLD-9'])->and(($this->inSchool)(fn () => DocumentSequence::count()))->toBe(0);
    });

    it('still needs the number in every row for a school that types them', function () use ($header) {
        $file = UploadedFile::fake()->createWithContent('s.csv', "$header\n,Amina,,Juma,female,2012-05-06,2026-01-10,,,,,,\n");

        $this->actingAs($this->owner)->post("{$this->url}/students/import", ['file' => $file])->assertSessionHas('import_total', 1);

        expect(($this->numbers)())->toBe([]);
    });

    it('leaves the example numbers out of the template when numbers are made', function () {
        ($this->useAuto)();

        $body = $this->actingAs($this->owner)->get("{$this->url}/students/import/template")->streamedContent();

        expect($body)->not->toContain('A-001')->and($body)->toContain(',Amina,');
    });
});
