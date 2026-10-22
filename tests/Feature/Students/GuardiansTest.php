<?php

use App\Models\Guardian;
use App\Models\Student;
use App\Models\Tenant;
use App\Tenancy\TenantContext;

beforeEach(function () {
    useTestHosts();

    $this->school = Tenant::factory()->create(['slug' => 'acme', 'name' => 'Acme Secondary']);
    $this->owner = userWithRole($this->school, 'owner');
    $this->teacher = userWithRole($this->school, 'teacher');
    $this->parent = userWithRole($this->school, 'parent_student');
    $this->url = 'http://acme.somaplus.test';
});

it('shows the guardians of the school with their pupils', function () {
    $student = TenantContext::run($this->school, fn () => Student::factory()->create([
        'first_name' => 'Zuhura', 'last_name' => 'Mwakyusa',
    ]));

    $guardian = TenantContext::run($this->school, function () use ($student) {
        $guardian = Guardian::factory()->create(['name' => 'Fatuma Juma', 'phone' => '0712345678']);
        $student->guardians()->attach($guardian->id, ['relationship' => 'mother', 'is_primary' => true]);

        return $guardian;
    });

    $this->actingAs($this->owner)->get("{$this->url}/guardians")->assertOk()
        ->assertSee('Fatuma Juma')
        ->assertSee('0712345678')
        ->assertSee('Zuhura Mwakyusa');
});

it('gives the list the search box of the datatable of the template', function () {
    TenantContext::run($this->school, fn () => Guardian::factory()->create(['name' => 'Fatuma Juma', 'phone' => '0712345678']));
    TenantContext::run($this->school, fn () => Guardian::factory()->create(['name' => 'Joseph Mushi', 'phone' => '0755123456']));

    // The datatable of the template moves its own search box into the header of the card.
    $html = $this->actingAs($this->owner)->get("{$this->url}/guardians")->assertOk()->getContent();

    expect($html)->toContain('class="datatable table')
        ->toContain('class="search-set"')
        ->toContain('class="search-input"');
});

it('shows the guardian and the pupils they are the parent of', function () {
    $student = TenantContext::run($this->school, fn () => Student::factory()->create(['first_name' => 'Zuhura', 'last_name' => 'Mwakyusa']));

    $guardian = TenantContext::run($this->school, function () use ($student) {
        $guardian = Guardian::factory()->create(['name' => 'Fatuma Juma', 'phone' => '0712345678', 'email' => 'fatuma@example.com']);
        $student->guardians()->attach($guardian->id, ['relationship' => 'mother', 'is_primary' => true]);

        return $guardian;
    });

    $this->actingAs($this->owner)->get("{$this->url}/guardians/{$guardian->id}")->assertOk()
        ->assertSee('Fatuma Juma')
        ->assertSee('fatuma@example.com')
        ->assertSee('Zuhura Mwakyusa')
        ->assertSee(__('students.relationships.mother'));
});

it('adds a guardian and links a pupil to them', function () {
    $student = TenantContext::run($this->school, fn () => Student::factory()->create(['first_name' => 'Zuhura', 'last_name' => 'Mwakyusa']));

    $this->actingAs($this->owner)
        ->post("{$this->url}/guardians", ['name' => 'Fatuma Juma', 'phone' => '0712 345 678', 'email' => 'fatuma@example.com'])
        ->assertSessionHasNoErrors();

    $guardian = TenantContext::run($this->school, fn () => Guardian::query()->firstOrFail());
    expect($guardian->phone)->toBe('0712345678');

    $this->actingAs($this->owner)
        ->post("{$this->url}/guardians/{$guardian->id}/students", [
            'student_id' => $student->id, 'relationship' => 'mother', 'is_primary' => '1',
        ])->assertSessionHasNoErrors();

    expect(TenantContext::run($this->school, fn () => $student->fresh()->guardians()->count()))->toBe(1);
});

it('refuses a phone number that is already in the school', function () {
    TenantContext::run($this->school, fn () => Guardian::factory()->create(['phone' => '0712345678']));

    $this->actingAs($this->owner)
        ->post("{$this->url}/guardians", ['name' => 'Someone Else', 'phone' => '0712345678'])
        ->assertSessionHasErrors('phone');

    expect(TenantContext::run($this->school, fn () => Guardian::query()->count()))->toBe(1);
});

it('changes a guardian and takes a pupil off them', function () {
    $student = TenantContext::run($this->school, fn () => Student::factory()->create());

    $guardian = TenantContext::run($this->school, function () use ($student) {
        $guardian = Guardian::factory()->create(['name' => 'Fatuma Juma', 'phone' => '0712345678']);
        $student->guardians()->attach($guardian->id, ['relationship' => 'mother']);

        return $guardian;
    });

    $this->actingAs($this->owner)
        ->put("{$this->url}/guardians/{$guardian->id}", ['name' => 'Fatuma Mwambura', 'phone' => '0712345678'])
        ->assertSessionHasNoErrors();

    expect(TenantContext::run($this->school, fn () => $guardian->fresh()->name))->toBe('Fatuma Mwambura');

    $this->actingAs($this->owner)
        ->delete("{$this->url}/guardians/{$guardian->id}/students/{$student->id}")
        ->assertSessionHasNoErrors();

    expect(TenantContext::run($this->school, fn () => $student->fresh()->guardians()->count()))->toBe(0);
    // A guardian left with no pupil is not kept.
    expect(TenantContext::run($this->school, fn () => Guardian::query()->count()))->toBe(0);
});

it('keeps a guardian who is still the parent of a pupil', function () {
    $student = TenantContext::run($this->school, fn () => Student::factory()->create());

    $guardian = TenantContext::run($this->school, function () use ($student) {
        $guardian = Guardian::factory()->create();
        $student->guardians()->attach($guardian->id, ['relationship' => 'father']);

        return $guardian;
    });

    $this->actingAs($this->owner)
        ->delete("{$this->url}/guardians/{$guardian->id}")
        ->assertSessionHas('delete');

    expect(TenantContext::run($this->school, fn () => Guardian::query()->count()))->toBe(1);
});

it('shows the guardians in the sidebar only to people who may look at them', function () {
    $this->actingAs($this->owner)->get($this->url)
        ->assertSee(__('menu.guardians'))
        ->assertSee(route('guardians.index'))
        ->assertSee(route('guardians.add'));

    $this->actingAs($this->teacher)->get($this->url)
        ->assertSee(__('menu.guardians'))
        ->assertSee(route('guardians.index'))
        ->assertDontSee(route('guardians.add'));

    $this->actingAs($this->parent)->get($this->url)
        ->assertDontSee(route('guardians.index'));
});

it('adds and changes a guardian in a pop-up, without loading the page again', function () {
    $guardian = TenantContext::run($this->school, fn () => Guardian::factory()->create(['name' => 'Fatuma Juma', 'phone' => '0712345678']));

    $html = $this->actingAs($this->owner)->get("{$this->url}/guardians")->assertOk()->getContent();

    expect($html)->toContain('id="guardian-modal"')
        ->toContain('data-bs-target="#guardian-modal"')
        ->toContain('action="'.route('guardians.store').'"')
        ->toContain('data-guardian-action="'.route('guardians.update', $guardian).'"')
        ->toContain('class="datatable table')
        ->not->toContain('/guardians/create');

    // The page of a guardian opens the pop-up filled with that guardian.
    $html = $this->actingAs($this->owner)->get("{$this->url}/guardians?edit={$guardian->id}")->assertOk()->getContent();

    expect($html)->toContain('data-guardian-open="1"')
        ->toContain('data-guardian-edit-name="Fatuma Juma"');

    // The entry of the menu opens the list with the pop-up of a new guardian ready.
    $this->actingAs($this->owner)->get("{$this->url}/guardians/add")->assertRedirect(route('guardians.index', ['add' => 1]));
});

it('lets only people who may change students add or change a guardian', function () {
    $guardian = TenantContext::run($this->school, fn () => Guardian::factory()->create());

    $this->actingAs($this->teacher)->get("{$this->url}/guardians/create")->assertNotFound();
    $this->actingAs($this->teacher)->post("{$this->url}/guardians", ['name' => 'X', 'phone' => '0712345678'])->assertForbidden();
    $this->actingAs($this->parent)->get("{$this->url}/guardians")->assertForbidden();
});

it('does not show the guardians of another school', function () {
    $other = Tenant::factory()->create(['slug' => 'beta']);
    TenantContext::run($other, fn () => Guardian::factory()->create(['name' => 'Betaguardian']));

    $this->actingAs($this->owner)->get("{$this->url}/guardians")
        ->assertOk()
        ->assertDontSee('Betaguardian');
});
