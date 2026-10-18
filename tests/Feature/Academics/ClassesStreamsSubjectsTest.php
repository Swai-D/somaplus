<?php

use App\Models\SchoolClass;
use App\Models\Stream;
use App\Models\Subject;
use App\Models\Tenant;
use App\Tenancy\TenantContext;

beforeEach(function () {
    useTestHosts();

    $this->school = Tenant::factory()->create(['slug' => 'acme']);
    $this->other = Tenant::factory()->create(['slug' => 'beta']);
    $this->url = 'http://acme.somaplus.test';
    $this->owner = userWithRole($this->school, 'owner');

    $this->inSchool = fn (Closure $work, ?Tenant $school = null) => TenantContext::run($school ?? $this->school, $work);
});

it('adds a class and lists classes in the order given', function () {
    $this->actingAs($this->owner)
        ->post("{$this->url}/academics/classes", ['name' => 'Form 2', 'sort_order' => 2])
        ->assertRedirect()
        ->assertSessionHasNoErrors();
    $this->actingAs($this->owner)
        ->post("{$this->url}/academics/classes", ['name' => 'Form 1', 'sort_order' => 1])
        ->assertSessionHasNoErrors();

    expect(($this->inSchool)(fn () => SchoolClass::firstOrFail()->tenant_id))->toBe($this->school->id);

    $this->actingAs($this->owner)
        ->get("{$this->url}/academics/classes")
        ->assertOk()
        ->assertSeeInOrder(['Form 1', 'Form 2']);
});

it('refuses a class without a name, with a repeated name or a bad order', function () {
    ($this->inSchool)(fn () => SchoolClass::factory()->create(['name' => 'Form 1']));

    $this->actingAs($this->owner)->post("{$this->url}/academics/classes", ['name' => ''])->assertSessionHasErrors('name');
    $this->actingAs($this->owner)->post("{$this->url}/academics/classes", ['name' => 'Form 1'])->assertSessionHasErrors('name');
    $this->actingAs($this->owner)->post("{$this->url}/academics/classes", ['name' => 'Form 9', 'sort_order' => -1])->assertSessionHasErrors('sort_order');
    $this->actingAs($this->owner)->post("{$this->url}/academics/classes", ['name' => 'Form 9', 'sort_order' => 'first'])->assertSessionHasErrors('sort_order');
});

it('allows the same class name at another school', function () {
    ($this->inSchool)(fn () => SchoolClass::factory()->create(['name' => 'Form 1']), $this->other);

    $this->actingAs($this->owner)
        ->post("{$this->url}/academics/classes", ['name' => 'Form 1'])
        ->assertSessionHasNoErrors();
});

it('changes a class and keeps its own name valid', function () {
    $class = ($this->inSchool)(fn () => SchoolClass::factory()->create(['name' => 'Form 1', 'sort_order' => 1]));

    $this->actingAs($this->owner)
        ->put("{$this->url}/academics/classes/{$class->id}", ['name' => 'Form 1', 'sort_order' => 5])
        ->assertSessionHasNoErrors();

    expect(($this->inSchool)(fn () => $class->fresh()->sort_order))->toBe(5);
});

it('adds streams to a class and refuses a repeated stream name in that class', function () {
    $class = ($this->inSchool)(fn () => SchoolClass::factory()->create(['name' => 'Form 1']));

    $this->actingAs($this->owner)
        ->post("{$this->url}/academics/classes/{$class->id}/streams", ['name' => 'A'])
        ->assertRedirect("{$this->url}/academics/classes/{$class->id}")
        ->assertSessionHasNoErrors();

    $this->actingAs($this->owner)
        ->post("{$this->url}/academics/classes/{$class->id}/streams", ['name' => 'A'])
        ->assertSessionHasErrors('name');

    $stream = ($this->inSchool)(fn () => Stream::firstOrFail());

    expect($stream->school_class_id)->toBe($class->id)->and($stream->tenant_id)->toBe($this->school->id);
});

it('allows the same stream name in another class', function () {
    $form1 = ($this->inSchool)(fn () => SchoolClass::factory()->create(['name' => 'Form 1']));
    $form2 = ($this->inSchool)(fn () => SchoolClass::factory()->create(['name' => 'Form 2']));
    ($this->inSchool)(fn () => Stream::factory()->create(['school_class_id' => $form1->id, 'name' => 'A']));

    $this->actingAs($this->owner)
        ->post("{$this->url}/academics/classes/{$form2->id}/streams", ['name' => 'A'])
        ->assertSessionHasNoErrors();
});

it('shows the streams of a class on its page', function () {
    $class = ($this->inSchool)(fn () => SchoolClass::factory()->create(['name' => 'Form 1']));
    ($this->inSchool)(fn () => Stream::factory()->create(['school_class_id' => $class->id, 'name' => 'Blue stream']));

    $this->actingAs($this->owner)
        ->get("{$this->url}/academics/classes/{$class->id}")
        ->assertOk()
        ->assertSee('Blue stream');
});

it('changes and deletes a stream', function () {
    $class = ($this->inSchool)(fn () => SchoolClass::factory()->create());
    $stream = ($this->inSchool)(fn () => Stream::factory()->create(['school_class_id' => $class->id, 'name' => 'A']));

    $this->actingAs($this->owner)
        ->put("{$this->url}/academics/streams/{$stream->id}", ['name' => 'B'])
        ->assertRedirect("{$this->url}/academics/classes/{$class->id}")
        ->assertSessionHasNoErrors();

    expect(($this->inSchool)(fn () => $stream->fresh()->name))->toBe('B');

    $this->actingAs($this->owner)->delete("{$this->url}/academics/streams/{$stream->id}")->assertRedirect();

    expect(($this->inSchool)(fn () => Stream::count()))->toBe(0);
});

it('does not delete a class that still has streams', function () {
    $class = ($this->inSchool)(fn () => SchoolClass::factory()->create());
    ($this->inSchool)(fn () => Stream::factory()->create(['school_class_id' => $class->id]));

    $this->actingAs($this->owner)
        ->delete("{$this->url}/academics/classes/{$class->id}")
        ->assertSessionHasErrors(['delete' => __('academics.classes.has_streams')]);

    expect(($this->inSchool)(fn () => SchoolClass::count()))->toBe(1);
});

it('deletes a class without streams', function () {
    $class = ($this->inSchool)(fn () => SchoolClass::factory()->create());

    $this->actingAs($this->owner)->delete("{$this->url}/academics/classes/{$class->id}")->assertRedirect("{$this->url}/academics/classes");

    expect(($this->inSchool)(fn () => SchoolClass::count()))->toBe(0);
});

it('adds a subject with and without a code', function () {
    $this->actingAs($this->owner)
        ->post("{$this->url}/academics/subjects", ['name' => 'Mathematics', 'code' => 'MATH'])
        ->assertSessionHasNoErrors();
    $this->actingAs($this->owner)
        ->post("{$this->url}/academics/subjects", ['name' => 'History', 'code' => ''])
        ->assertSessionHasNoErrors();
    $this->actingAs($this->owner)
        ->post("{$this->url}/academics/subjects", ['name' => 'Geography'])
        ->assertSessionHasNoErrors();

    $codes = ($this->inSchool)(fn () => Subject::orderBy('name')->pluck('code', 'name')->all());

    expect($codes)->toBe(['Geography' => null, 'History' => null, 'Mathematics' => 'MATH']);
});

it('refuses a repeated subject name or code in one school but not across schools', function () {
    ($this->inSchool)(fn () => Subject::factory()->create(['name' => 'Mathematics', 'code' => 'MATH']));
    ($this->inSchool)(fn () => Subject::factory()->create(['name' => 'Physics', 'code' => 'PHY']), $this->other);

    $this->actingAs($this->owner)->post("{$this->url}/academics/subjects", ['name' => 'Mathematics'])->assertSessionHasErrors('name');
    $this->actingAs($this->owner)->post("{$this->url}/academics/subjects", ['name' => 'Maths', 'code' => 'MATH'])->assertSessionHasErrors('code');
    $this->actingAs($this->owner)->post("{$this->url}/academics/subjects", ['name' => 'Physics', 'code' => 'PHY'])->assertSessionHasNoErrors();
});

it('changes and deletes a subject', function () {
    $subject = ($this->inSchool)(fn () => Subject::factory()->create(['name' => 'Maths', 'code' => 'MATH']));

    $this->actingAs($this->owner)
        ->put("{$this->url}/academics/subjects/{$subject->id}", ['name' => 'Mathematics', 'code' => 'MATH'])
        ->assertRedirect("{$this->url}/academics/subjects")
        ->assertSessionHasNoErrors();

    expect(($this->inSchool)(fn () => $subject->fresh()->name))->toBe('Mathematics');

    $this->actingAs($this->owner)->delete("{$this->url}/academics/subjects/{$subject->id}")->assertRedirect();

    expect(($this->inSchool)(fn () => Subject::count()))->toBe(0);
});

it('shows the subjects of the school, with their codes', function () {
    ($this->inSchool)(fn () => Subject::factory()->create(['name' => 'Kiswahili', 'code' => 'KIS']));

    $this->actingAs($this->owner)
        ->get("{$this->url}/academics/subjects")
        ->assertOk()
        ->assertSee('Kiswahili')
        ->assertSee('KIS');
});
