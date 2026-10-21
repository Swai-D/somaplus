<?php

use App\Enums\SubscriptionStatus;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\Enrollment;
use App\Models\Guardian;
use App\Models\SchoolClass;
use App\Models\Stream;
use App\Models\Student;
use App\Models\Tenant;
use App\Tenancy\TenantContext;
use Illuminate\Http\UploadedFile;

const IMPORT_HEADER = 'admission_number,first_name,middle_name,last_name,gender,date_of_birth,admitted_on,class,stream,guardian_name,guardian_phone,guardian_email,guardian_relationship';

beforeEach(function () {
    useTestHosts();

    $this->school = Tenant::factory()->create(['slug' => 'acme']);
    $this->other = Tenant::factory()->create(['slug' => 'beta']);
    $this->url = 'http://acme.somaplus.test';
    $this->owner = userWithRole($this->school, 'owner');

    $this->inSchool = fn (Closure $work, ?Tenant $school = null) => TenantContext::run($school ?? $this->school, $work);

    [$this->year, $this->form1, $this->streamA] = ($this->inSchool)(function () {
        $year = AcademicYear::factory()->current()->create(['name' => '2026']);
        $form1 = SchoolClass::factory()->create(['name' => 'Form 1', 'sort_order' => 1]);

        return [$year, $form1, Stream::factory()->create(['school_class_id' => $form1->id, 'name' => 'A'])];
    });

    $this->csv = fn (array $lines) => UploadedFile::fake()->createWithContent('students.csv', implode("\n", [IMPORT_HEADER, ...$lines])."\n");
    $this->upload = fn ($file, $user = null) => $this->actingAs($user ?? $this->owner)->post("{$this->url}/students/import", ['file' => $file]);
    $this->count = fn (?Tenant $school = null) => ($this->inSchool)(fn () => Student::count(), $school);
});

it('adds students with class, stream and guardian from a CSV file and audits it', function () {
    ($this->upload)(($this->csv)([
        'A-001,Amina,,Juma,female,2012-05-06,2026-01-10,Form 1,A,Fatuma Juma,0712345678,fatuma@example.com,mother',
        'A-002,Baraka,Peter,Mushi,male,2011-11-20,,form 1,,,,,',
    ]))->assertSessionHasNoErrors()->assertRedirect(route('students.import', [], false))->assertSessionHas('import_result');

    ($this->inSchool)(function () {
        $amina = Student::where('admission_number', 'A-001')->firstOrFail();
        $baraka = Student::where('admission_number', 'A-002')->firstOrFail();

        expect(Student::count())->toBe(2)
            ->and($amina->tenant_id)->toBe($this->school->id)
            ->and($amina->enrollments()->first()->stream_id)->toBe($this->streamA->id)
            ->and($amina->enrollments()->first()->academic_year_id)->toBe($this->year->id)
            ->and($amina->guardians()->first()->phone)->toBe('0712345678')
            ->and($amina->guardians()->first()->pivot->relationship)->toBe('mother')
            ->and((bool) $amina->guardians()->first()->pivot->is_primary)->toBeTrue()
            ->and($baraka->middle_name)->toBe('Peter')
            ->and($baraka->enrollments()->first()->stream_id)->toBeNull()
            ->and($baraka->admitted_on->toDateString())->toBe(now()->toDateString())
            ->and($baraka->guardians()->count())->toBe(0)
            ->and(AuditLog::where('action', 'student.created')->count())->toBe(2)
            ->and(AuditLog::where('action', 'student.imported')->where('user_id', $this->owner->id)->exists())->toBeTrue();
    });
});

it('reads day-first dates, short genders and Swahili words, and restores a phone that lost its zero', function () {
    ($this->upload)(($this->csv)([
        'A-010,Neema,,Kimaro,ke,06/05/2012,10-01-2026,,,Joseph Kimaro,755123456,,baba',
    ]))->assertSessionHas('import_result');

    ($this->inSchool)(function () {
        $student = Student::firstOrFail();
        expect($student->gender)->toBe('female')
            ->and($student->date_of_birth->toDateString())->toBe('2012-05-06')
            ->and($student->admitted_on->toDateString())->toBe('2026-01-10')
            ->and($student->enrollments()->count())->toBe(0)
            ->and($student->guardians()->first()->phone)->toBe('0755123456')
            ->and($student->guardians()->first()->pivot->relationship)->toBe('father');
    });
});

it('also reads a semicolon file with a byte-order mark', function () {
    $content = "\xEF\xBB\xBF".str_replace(',', ';', IMPORT_HEADER)."\nA-020;Zawadi;;Ally;female;2012-01-02;;;;;;;\n";

    ($this->upload)(UploadedFile::fake()->createWithContent('students.csv', $content))->assertSessionHas('import_result');

    expect(($this->count)())->toBe(1);
});

it('saves a parent with several children once, by phone number', function () {
    ($this->upload)(($this->csv)([
        'A-030,Asha,,Said,female,2013-02-02,,Form 1,,Zuhura Said,0700111222,,mother',
        'A-031,Hamisi,,Said,male,2010-03-03,,Form 1,,Zuhura Said,0700 111 222,,mother',
    ]))->assertSessionHas('import_result');

    ($this->inSchool)(fn () => expect(Guardian::count())->toBe(1)->and(Guardian::first()->students()->count())->toBe(2));
});

it('adds nothing when any row has a problem, and names each line', function () {
    ($this->upload)(($this->csv)([
        'A-040,Good,,Row,female,2012-05-06,,Form 1,,,,,',
        'A-041,,,Nameless,female,2012-05-06,,,,,,,',
        'A-042,Bad,,Class,male,2012-05-06,,Form 9,,,,,',
        'A-043,Bad,,Stream,male,2012-05-06,,Form 1,Z,,,,',
        'A-044,Bad,,Date,male,2099-01-01,,,,,,,',
        'A-045,Bad,,Gender,other,2012-05-06,,,,,,,',
        'A-046,Bad,,Guardian,male,2012-05-06,,,,Only Name,,,',
        'A-040,Dup,,Number,male,2012-05-06,,,,,,,',
    ]))->assertSessionHas('import_total', 7);

    $problems = implode("\n", session('import_problems'));
    expect($problems)->toContain('Line 3:')->toContain('Line 4:')->toContain('Line 5:')->toContain('Line 6:')
        ->toContain('Line 7:')->toContain('Line 8:')->toContain('Line 9:')
        ->and($problems)->not->toContain('Line 2:')
        ->and(($this->count)())->toBe(0)
        ->and(($this->inSchool)(fn () => Enrollment::count()))->toBe(0);
});

it('never changes a student that already exists, so sending the file twice adds nothing', function () {
    $file = fn () => ($this->csv)(['A-050,Once,,Only,female,2012-05-06,,Form 1,,Mama Once,0711000111,,mother']);

    ($this->upload)($file())->assertSessionHas('import_result');
    ($this->upload)($file())->assertSessionHas('import_problems')->assertSessionHas('import_total', 1);

    ($this->inSchool)(fn () => expect(Student::count())->toBe(1)->and(Enrollment::count())->toBe(1)->and(Guardian::count())->toBe(1));
});

it('refuses a class when there is no current academic year, but accepts students without one', function () {
    ($this->inSchool)(fn () => AcademicYear::query()->update(['is_current' => false]));

    ($this->upload)(($this->csv)(['A-060,Needs,,Year,male,2012-05-06,,Form 1,,,,,']))->assertSessionHas('import_total', 1);
    expect(($this->count)())->toBe(0);

    ($this->upload)(($this->csv)(['A-060,Needs,,Year,male,2012-05-06,,,,,,,']))->assertSessionHas('import_result');
    expect(($this->count)())->toBe(1);
});

it('refuses unknown or missing columns and an empty or oversized file', function () {
    $post = fn (string $content) => ($this->upload)(UploadedFile::fake()->createWithContent('students.csv', $content));

    $post("admission_number,first_name,last_name,gender,date_of_birth,colour\nA-1,A,B,male,2012-01-01,red\n")->assertSessionHas('import_total', 1);
    $post("admission_number,first_name\nA-1,A\n")->assertSessionHas('import_total', 1);
    $post(IMPORT_HEADER."\n")->assertSessionHas('import_total', 1);

    config()->set('students.import_max_rows', 1);
    $post(IMPORT_HEADER."\nA-1,A,,B,male,2012-01-01,,,,,,,\nA-2,A,,B,male,2012-01-01,,,,,,,\n")->assertSessionHas('import_total', 1);

    expect(($this->count)())->toBe(0);
});

it('refuses a file that is not a CSV', function () {
    ($this->upload)(UploadedFile::fake()->create('students.pdf', 10, 'application/pdf'))->assertSessionHasErrors('file');
});

it('keeps schools apart: another school has no clash on admission numbers and sees none of the rows', function () {
    ($this->inSchool)(fn () => Student::factory()->create(['admission_number' => 'A-070']), $this->other);

    ($this->upload)(($this->csv)(['A-070,Same,,Number,female,2012-05-06,,Form 1,,,,,']))->assertSessionHas('import_result');

    expect(($this->count)())->toBe(1)->and(($this->count)($this->other))->toBe(1);
});

it('does not match a class of another school', function () {
    ($this->inSchool)(fn () => SchoolClass::factory()->create(['name' => 'Secret Class']), $this->other);

    ($this->upload)(($this->csv)(['A-080,No,,Way,male,2012-05-06,,Secret Class,,,,,']))->assertSessionHas('import_total', 1);

    expect(($this->count)())->toBe(0);
});

it('lets only owner and head teacher import; others get 403', function (string $role, int $status) {
    $user = userWithRole($this->school, $role);

    $this->actingAs($user)->get("{$this->url}/students/import")->assertStatus($status);
    $this->actingAs($user)->get("{$this->url}/students/import/template")->assertStatus($status);
    ($this->upload)(($this->csv)(['A-090,Role,,Check,male,2012-05-06,,,,,,,']), $user)->assertStatus($status === 200 ? 302 : $status);

    expect(($this->count)())->toBe($status === 200 ? 1 : 0);
})->with([
    'owner' => ['owner', 200],
    'head teacher' => ['head_teacher', 200],
    'teacher' => ['teacher', 403],
    'accountant' => ['accountant', 403],
    'parent' => ['parent_student', 403],
]);

it('serves a template that the importer accepts as it is', function () {
    $response = $this->actingAs($this->owner)->get("{$this->url}/students/import/template")->assertOk();
    $body = $response->streamedContent();

    expect($body)->toContain(IMPORT_HEADER)->toContain('Form 1');

    ($this->upload)(UploadedFile::fake()->createWithContent('students.csv', $body))->assertSessionHas('import_result');
    expect(($this->count)())->toBe(2);
});

it('shows the import page in English and Swahili and links it from the list', function () {
    $this->actingAs($this->owner)->get("{$this->url}/students/import")->assertOk()->assertSee('Import students')->assertSee('Form 1');
    $this->actingAs($this->owner)->get("{$this->url}/students")->assertOk()->assertSee(route('students.import', [], false));
    $this->actingAs($this->owner)->withSession(['locale' => 'sw'])->get("{$this->url}/students/import")->assertOk()->assertSee('Pakia wanafunzi');
});

it('is blocked in a read-only school but the page can still be read', function () {
    $this->school->update(['subscription_status' => SubscriptionStatus::ReadOnly]);
    $owner = userWithRole($this->school, 'owner');

    $this->actingAs($owner)->get("{$this->url}/students/import")->assertOk();
    ($this->upload)(($this->csv)(['A-100,Read,,Only,male,2012-05-06,,,,,,,']), $owner)->assertForbidden();
    expect(($this->count)())->toBe(0);
});
