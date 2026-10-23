<?php

use App\Models\AuditLog;
use App\Models\Event;
use App\Models\Tenant;
use App\Tenancy\TenantContext;

beforeEach(function () {
    useTestHosts();

    $this->school = Tenant::factory()->create(['slug' => 'acme', 'name' => 'Acme Secondary']);
    $this->other = Tenant::factory()->create(['slug' => 'beta']);
    $this->url = 'http://acme.somaplus.test';
    $this->owner = userWithRole($this->school, 'owner');
    $this->headTeacher = userWithRole($this->school, 'head_teacher');
    $this->teacher = userWithRole($this->school, 'teacher');
    $this->accountant = userWithRole($this->school, 'accountant');
    $this->inSchool = fn (Closure $work, ?Tenant $school = null) => TenantContext::run($school ?? $this->school, $work);

    $this->eventFor = fn (string $startsAt = '2026-11-05 09:00:00', ?Tenant $school = null, ?string $title = 'Parents meeting') => ($this->inSchool)(function () use ($title, $startsAt) {
        $event = new Event;
        $event->forceFill(['starts_at' => $startsAt, 'ends_at' => null, 'title' => $title ?? 'Parents meeting', 'description' => 'Form one parents.'])->save();

        return $event;
    }, $school);
});

it('shows the month grid with its events and this month first', function () {
    $event = ($this->eventFor)();

    $this->actingAs($this->teacher)->get($this->url.'/communication/events')
        ->assertOk()
        ->assertSee($event->title, false);

    $this->travel(-1)->months();
    $older = ($this->eventFor)('2026-09-10 09:00:00', null, 'Old meeting');
    $this->travelBack();

    $this->actingAs($this->teacher)->get($this->url.'/communication/events?month=2026-09')
        ->assertOk()->assertSee($older->title, false);
});

it('lists what is coming next in the calendar', function () {
    ($this->eventFor)('2026-10-30 09:00:00', null, 'Soon event');
    ($this->eventFor)('2020-01-10 09:00:00', null, 'Long past event');

    $page = $this->actingAs($this->teacher)->get($this->url.'/communication/events')->assertOk();

    $page->assertSee('Soon event', false)->assertDontSee('Long past event', false);
});

it('puts an event on the calendar and writes what it did into the audit log', function () {
    $this->actingAs($this->headTeacher)->post($this->url.'/communication/events', [
        'starts_at' => '2026-11-05T09:00', 'ends_at' => '2026-11-05T12:00', 'title' => 'Sports day', 'description' => 'At the grounds.',
    ])->assertRedirect($this->url.'/communication/events?month=2026-11')->assertSessionHasNoErrors();

    $event = ($this->inSchool)(fn () => Event::query()->where('title', 'Sports day')->sole());
    expect($event->starts_at->format('Y-m-d H:i'))->toBe('2026-11-05 09:00')->and($event->ends_at?->format('H:i'))->toBe('12:00');

    ($this->inSchool)(fn () => expect(AuditLog::query()->where('action', 'event.created')->where('subject_id', $event->id)->exists())->toBeTrue());
});

it('refuses the person who may only look at the calendar to change it', function () {
    $event = ($this->eventFor)();

    $payload = ['starts_at' => '2026-11-05T09:00', 'title' => 'Changed'];
    $this->actingAs($this->teacher)->post($this->url.'/communication/events', $payload)->assertForbidden();
    $this->actingAs($this->accountant)->put($this->url.'/communication/events/'.$event->id, $payload)->assertForbidden();
    $this->actingAs($this->accountant)->delete($this->url.'/communication/events/'.$event->id)->assertForbidden();

    ($this->inSchool)(fn () => expect($event->fresh()?->title)->toBe('Parents meeting'));
});

it('refuses the calendar to the parent student', function () {
    $user = userWithRole($this->school, 'parent_student');

    $this->actingAs($user)->get($this->url.'/communication/events')->assertForbidden();
});

it('refuses an end that does not follow the start', function () {
    $this->actingAs($this->owner)->post($this->url.'/communication/events', [
        'starts_at' => '2026-11-05T12:00', 'ends_at' => '2026-11-05T09:00', 'title' => 'Backwards',
    ])->assertSessionHasErrors('ends_at');
});

it('opens the edit page, changes an event and takes one off the calendar', function () {
    $event = ($this->eventFor)();

    $this->actingAs($this->owner)->get($this->url.'/communication/events/'.$event->id.'/edit')->assertOk()->assertSee($event->title, false);

    $this->actingAs($this->owner)->put($this->url.'/communication/events/'.$event->id, [
        'starts_at' => '2026-11-06T10:00', 'title' => 'Parents meeting moved', 'description' => null,
    ])->assertRedirect($this->url.'/communication/events?month=2026-11')->assertSessionHasNoErrors();
    expect($event->fresh()?->starts_at->format('d H:i'))->toBe('06 10:00')->and($event->fresh()?->description)->toBeNull();

    $this->actingAs($this->owner)->delete($this->url.'/communication/events/'.$event->id)->assertRedirect();
    ($this->inSchool)(fn () => expect(Event::query()->whereKey($event->id)->exists())->toBeFalse());
});

it('keeps each school to its own calendar', function () {
    $otherEvent = ($this->eventFor)('2026-11-05 09:00:00', $this->other);

    $this->actingAs($this->owner)->get($this->url.'/communication/events')->assertOk()->assertDontSee($otherEvent->title, false);
    $this->actingAs($this->owner)->get($this->url.'/communication/events/'.$otherEvent->id.'/edit')->assertNotFound();
    $this->actingAs($this->owner)->put($this->url.'/communication/events/'.$otherEvent->id, [
        'starts_at' => '2026-11-05T09:00', 'title' => 'Hijacked',
    ])->assertNotFound();
    $this->actingAs($this->owner)->delete($this->url.'/communication/events/'.$otherEvent->id)->assertNotFound();
});
