<?php

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Stream;
use App\Models\Subject;
use App\Models\Tenant;
use App\Models\Term;
use App\Tenancy\TenantContext;

beforeEach(function () {
    useTestHosts();

    $this->acme = Tenant::factory()->create(['slug' => 'acme']);
    $this->beta = Tenant::factory()->create(['slug' => 'beta']);
    $this->url = 'http://acme.somaplus.test';
    $this->owner = userWithRole($this->acme, 'owner');

    // Everything below belongs to Beta; a person signed in at Acme must never reach it.
    $this->betaRecords = TenantContext::run($this->beta, function () {
        $year = AcademicYear::factory()->create(['name' => 'Beta year']);
        $class = SchoolClass::factory()->create(['name' => 'Beta class']);

        return [
            'year' => $year,
            'term' => Term::factory()->create(['academic_year_id' => $year->id, 'name' => 'Beta term']),
            'class' => $class,
            'stream' => Stream::factory()->create(['school_class_id' => $class->id, 'name' => 'Beta stream']),
            'subject' => Subject::factory()->create(['name' => 'Beta subject']),
        ];
    });
});

it('shows a school only its own records in the lists', function () {
    TenantContext::run($this->acme, function () {
        AcademicYear::factory()->create(['name' => 'Acme year']);
        SchoolClass::factory()->create(['name' => 'Acme class']);
        Subject::factory()->create(['name' => 'Acme subject']);
    });

    $this->actingAs($this->owner)->get("{$this->url}/academics/years")->assertSee('Acme year')->assertDontSee('Beta year');
    $this->actingAs($this->owner)->get("{$this->url}/academics/classes")->assertSee('Acme class')->assertDontSee('Beta class');
    $this->actingAs($this->owner)->get("{$this->url}/academics/subjects")->assertSee('Acme subject')->assertDontSee('Beta subject');
});

it('answers 404 to pages of another school\'s records', function (string $path) {
    $ids = array_map(fn ($record) => $record->id, $this->betaRecords);
    $path = strtr($path, ['{year}' => $ids['year'], '{term}' => $ids['term'], '{class}' => $ids['class'], '{stream}' => $ids['stream'], '{subject}' => $ids['subject']]);

    $this->actingAs($this->owner)->get("{$this->url}/academics/{$path}")->assertNotFound();
})->with([
    'year page' => 'years/{year}',
    'year edit' => 'years/{year}/edit',
    'term edit' => 'terms/{term}/edit',
    'class page' => 'classes/{class}',
    'class edit' => 'classes/{class}/edit',
    'stream edit' => 'streams/{stream}/edit',
    'subject edit' => 'subjects/{subject}/edit',
]);

it('cannot change or delete another school\'s records', function (string $method, string $path, array $data) {
    $ids = array_map(fn ($record) => $record->id, $this->betaRecords);
    $path = strtr($path, ['{year}' => $ids['year'], '{term}' => $ids['term'], '{class}' => $ids['class'], '{stream}' => $ids['stream'], '{subject}' => $ids['subject']]);

    $this->actingAs($this->owner)->call($method, "{$this->url}/academics/{$path}", $data)->assertNotFound();

    TenantContext::runAsSystem(function () {
        expect(AcademicYear::count())->toBe(1)
            ->and(Term::count())->toBe(1)
            ->and(SchoolClass::count())->toBe(1)
            ->and(Stream::count())->toBe(1)
            ->and(Subject::count())->toBe(1)
            ->and(AcademicYear::firstOrFail()->name)->toBe('Beta year')
            ->and(Subject::firstOrFail()->name)->toBe('Beta subject');
    });
})->with([
    'update year' => ['PUT', 'years/{year}', ['name' => 'Hijacked', 'starts_on' => '2040-01-01', 'ends_on' => '2040-12-31']],
    'delete year' => ['DELETE', 'years/{year}', []],
    'add term to year' => ['POST', 'years/{year}/terms', ['name' => 'Hijacked', 'starts_on' => '2030-01-02', 'ends_on' => '2030-02-02']],
    'update term' => ['PUT', 'terms/{term}', ['name' => 'Hijacked', 'starts_on' => '2030-01-02', 'ends_on' => '2030-02-02']],
    'delete term' => ['DELETE', 'terms/{term}', []],
    'update class' => ['PUT', 'classes/{class}', ['name' => 'Hijacked']],
    'delete class' => ['DELETE', 'classes/{class}', []],
    'add stream to class' => ['POST', 'classes/{class}/streams', ['name' => 'Hijacked']],
    'update stream' => ['PUT', 'streams/{stream}', ['name' => 'Hijacked']],
    'delete stream' => ['DELETE', 'streams/{stream}', []],
    'update subject' => ['PUT', 'subjects/{subject}', ['name' => 'Hijacked']],
    'delete subject' => ['DELETE', 'subjects/{subject}', []],
]);

it('stamps new records with the school of the person who made them', function () {
    $this->actingAs($this->owner)->post("{$this->url}/academics/subjects", ['name' => 'Chemistry', 'tenant_id' => $this->beta->id])->assertRedirect();

    $subject = TenantContext::run($this->acme, fn () => Subject::where('name', 'Chemistry')->firstOrFail());

    expect($subject->tenant_id)->toBe($this->acme->id);
});

it('lets the other school see its own records', function () {
    $betaOwner = userWithRole($this->beta, 'owner');

    $this->actingAs($betaOwner)
        ->get('http://beta.somaplus.test/academics/subjects')
        ->assertOk()
        ->assertSee('Beta subject');
});

it('does not count the roles of one school at another school\'s address', function () {
    // The owner of Acme has no role at Beta, so Beta's pages are forbidden to them.
    $this->actingAs($this->owner)
        ->get('http://beta.somaplus.test/academics/subjects')
        ->assertForbidden();
});
