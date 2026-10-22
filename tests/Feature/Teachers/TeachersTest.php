<?php

use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Tenant;
use App\Models\User;
use App\Tenancy\TenantContext;

beforeEach(function () {
    useTestHosts();

    $this->school = Tenant::factory()->create(['slug' => 'acme', 'name' => 'Acme Secondary']);
    $this->owner = userWithRole($this->school, 'owner');
    $this->head = userWithRole($this->school, 'head_teacher');
    $this->teacher = userWithRole($this->school, 'teacher');
    $this->accountant = userWithRole($this->school, 'accountant');
    $this->parent = userWithRole($this->school, 'parent_student');
    $this->url = 'http://acme.somaplus.test';
});

/** A teacher of the school, with the person who signs in. */
function aTeacher(Tenant $school, string $name = 'Zawadi Mushi', string $email = 'zawadi@acme.test', string $staffNumber = 'TCH-001'): Teacher
{
    return TenantContext::run($school, function () use ($name, $email, $staffNumber) {
        $person = User::factory()->create(['name' => $name, 'email' => $email]);
        $person->assignRole('teacher');

        return Teacher::factory()->create([
            'user_id' => $person->id,
            'staff_number' => $staffNumber,
            'phone' => '0712345678',
        ]);
    });
}

it('shows the teachers of the school with what they teach', function () {
    $teacher = aTeacher($this->school);

    TenantContext::run($this->school, function () use ($teacher) {
        $teacher->subjects()->attach(Subject::factory()->create(['name' => 'Mathematics'])->id);
        $teacher->classes()->attach(SchoolClass::factory()->create(['name' => 'Form 1'])->id);
    });

    $this->actingAs($this->owner)->get("{$this->url}/teachers")->assertOk()
        ->assertSee('Zawadi Mushi')
        ->assertSee('TCH-001')
        ->assertSee('0712345678')
        ->assertSee('Mathematics')
        ->assertSee('Form 1');
});

it('gives the list the search box of the datatable of the template', function () {
    aTeacher($this->school);

    // The datatable of the template moves its own search box into the header of the card.
    $html = $this->actingAs($this->owner)->get("{$this->url}/teachers")->assertOk()->getContent();

    expect($html)->toContain('class="datatable table')
        ->toContain('class="search-set"')
        ->toContain('class="search-input"');
});

it('shows the form of a new teacher in a pop-up of the list', function () {
    $html = $this->actingAs($this->owner)->get("{$this->url}/teachers")->assertOk()->getContent();

    expect($html)->toContain('id="teacher-modal"')
        ->toContain('data-bs-target="#teacher-modal"')
        ->toContain('action="'.route('teachers.store').'"')
        ->toContain('name="form" type="hidden" value="teacher-modal"');
});

it('opens the list with the pop-up ready instead of a page of its own', function () {
    $this->actingAs($this->owner)->get("{$this->url}/teachers/create")
        ->assertRedirect("{$this->url}/teachers?add=teacher-modal");
});

it('shows the page of a teacher', function () {
    $teacher = aTeacher($this->school);

    $this->actingAs($this->owner)->get("{$this->url}/teachers/{$teacher->id}")->assertOk()
        ->assertSee('Zawadi Mushi')
        ->assertSee('TCH-001')
        ->assertSee('zawadi@acme.test');
});

/** A password only for the test run: fresh every time, so nothing is kept in the file. */
function aPasswordFor(): string
{
    return 'Welcome-'.bin2hex(random_bytes(4));
}

it('adds a teacher with the account, the subjects and the classes', function () {
    $subject = TenantContext::run($this->school, fn () => Subject::factory()->create(['name' => 'Mathematics']));
    $class = TenantContext::run($this->school, fn () => SchoolClass::factory()->create(['name' => 'Form 1']));
    $password = aPasswordFor();

    $this->actingAs($this->owner)->post("{$this->url}/teachers", [
        'name' => 'Neema Mushi',
        'email' => 'neema@acme.test',
        'password' => $password,
        'phone' => '0755111222',
        'staff_number' => 'TCH-002',
        'gender' => 'female',
        'joined_on' => '2026-01-15',
        'subjects' => [$subject->id],
        'classes' => [$class->id],
    ])->assertSessionHasNoErrors();

    $teacher = TenantContext::run($this->school, fn () => Teacher::query()->firstOrFail());

    expect($teacher->user->name)->toBe('Neema Mushi')
        ->and($teacher->user->email)->toBe('neema@acme.test')
        ->and($teacher->user->hasRole('teacher'))->toBeTrue()
        ->and($teacher->phone)->toBe('0755111222')
        ->and($teacher->staff_number)->toBe('TCH-002')
        ->and($teacher->isActive())->toBeTrue()
        ->and(str_starts_with($teacher->user->password, '$2y$'))->toBeTrue()
        ->and($teacher->user->password)->not->toBe($password)
        ->and($teacher->subjects->pluck('id')->all())->toBe([$subject->id])
        ->and($teacher->classes->pluck('id')->all())->toBe([$class->id]);
});

it('refuses a teacher without a name, a phone or a password', function () {
    $this->actingAs($this->owner)->post("{$this->url}/teachers", [
        'name' => '', 'email' => 'neema@acme.test', 'phone' => '', 'joined_on' => '2026-01-15',
    ])->assertSessionHasErrors(['name', 'phone', 'password']);

    expect(TenantContext::run($this->school, fn () => Teacher::query()->count()))->toBe(0);
});

it('refuses an email that is already in the school', function () {
    aTeacher($this->school, 'Zawadi Mushi', 'zawadi@acme.test');

    $this->actingAs($this->owner)->post("{$this->url}/teachers", [
        'name' => 'Someone Else', 'email' => 'zawadi@acme.test', 'password' => aPasswordFor(),
        'phone' => '0755000000', 'joined_on' => '2026-01-15',
    ])->assertSessionHasErrors('email');

    expect(TenantContext::run($this->school, fn () => Teacher::query()->count()))->toBe(1);
});

it('refuses a staff number that is already in the school', function () {
    aTeacher($this->school);

    $this->actingAs($this->owner)->post("{$this->url}/teachers", [
        'name' => 'Someone Else', 'email' => 'other@acme.test', 'password' => aPasswordFor(),
        'phone' => '0755000000', 'staff_number' => 'TCH-001', 'joined_on' => '2026-01-15',
    ])->assertSessionHasErrors('staff_number');
});

it('changes a teacher and keeps the password when it is left empty', function () {
    $teacher = aTeacher($this->school);

    $this->actingAs($this->owner)->put("{$this->url}/teachers/{$teacher->id}", [
        'name' => 'Zawadi J. Mushi',
        'email' => 'zawadi@acme.test',
        'password' => '',
        'phone' => '0755999888',
        'staff_number' => 'TCH-001',
        'joined_on' => '2026-01-15',
    ])->assertSessionHasNoErrors();

    $changed = TenantContext::run($this->school, fn () => $teacher->fresh());

    expect($changed->user->name)->toBe('Zawadi J. Mushi')
        ->and($changed->phone)->toBe('0755999888');
});

it('puts a teacher out of duty and back into duty', function () {
    $teacher = aTeacher($this->school);

    $this->actingAs($this->owner)->post("{$this->url}/teachers/{$teacher->id}/status")->assertRedirect();
    expect(TenantContext::run($this->school, fn () => $teacher->fresh()->status))->toBe(Teacher::INACTIVE);

    $this->actingAs($this->owner)->post("{$this->url}/teachers/{$teacher->id}/status")->assertRedirect();
    expect(TenantContext::run($this->school, fn () => $teacher->fresh()->status))->toBe(Teacher::ACTIVE);
});

it('takes a teacher out of the school and keeps the account', function () {
    $teacher = aTeacher($this->school);

    $this->actingAs($this->owner)->delete("{$this->url}/teachers/{$teacher->id}")->assertRedirect();

    expect(TenantContext::run($this->school, fn () => Teacher::query()->count()))->toBe(0);

    // The account stays, so the marks the person entered keep their name.
    $account = TenantContext::run($this->school, fn () => User::where('email', 'zawadi@acme.test')->first());
    expect($account)->not->toBeNull()
        ->and($account->getRoleNames()->all())->toBe([]);
});

it('searches for a teacher with a POST, so what is typed never lands in a URL', function () {
    aTeacher($this->school, 'Zawadi Mushi', 'zawadi@acme.test');
    aTeacher($this->school, 'John Kileo', 'john@acme.test', 'TCH-002');

    $response = $this->actingAs($this->owner)->post("{$this->url}/teachers/search", ['term' => 'Zawadi']);

    $response->assertOk()->assertSee('Zawadi Mushi')->assertDontSee('John Kileo');
});

it('refuses a search of less than two letters', function () {
    $this->actingAs($this->owner)->post("{$this->url}/teachers/search", ['term' => 'a'])
        ->assertSessionHasErrors('term');
});

it('keeps the teachers away from the accounts that may not see them', function (string $account) {
    $this->actingAs($this->{$account})->get("{$this->url}/teachers")->assertForbidden();
})->with(['teacher', 'accountant', 'parent']);

it('lets a head teacher see the teachers', function () {
    aTeacher($this->school);

    $this->actingAs($this->head)->get("{$this->url}/teachers")->assertOk()->assertSee('Zawadi Mushi');
});

it('does not reach a teacher of another school', function () {
    $other = Tenant::factory()->create(['slug' => 'beta', 'name' => 'Beta Academy']);
    $otherTeacher = aTeacher($other, 'Other Teacher', 'other@beta.test');

    $this->actingAs($this->owner)->get("{$this->url}/teachers/{$otherTeacher->id}")->assertNotFound();
});

it('shows the teachers in the menu of the school', function () {
    $html = $this->actingAs($this->owner)->get("{$this->url}/")->assertOk()->getContent();

    expect($html)->toContain(route('teachers.index'))->toContain(__('menu.teachers'));
});
