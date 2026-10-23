<?php

use App\Models\AuditLog;
use App\Models\Notice;
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

    $this->noticeFor = fn (?Tenant $school = null, ?string $title = 'Opening day') => ($this->inSchool)(function () use ($title) {
        $notice = new Notice;
        $notice->forceFill(['day' => now()->toDateString(), 'title' => $title ?? 'Opening day', 'description' => 'School opens at 8:00.'])->save();

        return $notice;
    }, $school);
});

it('shows the board to everybody who may look at communication', function () {
    $notice = ($this->noticeFor)();

    foreach ([$this->owner, $this->accountant, $this->teacher] as $user) {
        $this->actingAs($user)->get($this->url.'/communication/notices')->assertOk();
    }

    $this->actingAs($this->owner)->get($this->url.'/communication/notices')
        ->assertOk()
        ->assertSee($notice->title)
        ->assertSee($notice->day->toDateString(), false);
});

it('puts a notice up and writes what it did into the audit log', function () {
    $response = $this->actingAs($this->headTeacher)->post($this->url.'/communication/notices', [
        'day' => '2026-10-26', 'title' => 'Sports day', 'description' => 'All classes at the grounds.',
    ])->assertRedirect($this->url.'/communication/notices')->assertSessionHasNoErrors();

    $notice = ($this->inSchool)(fn () => Notice::query()->where('title', 'Sports day')->sole());
    expect($notice->day->toDateString())->toBe('2026-10-26');

    ($this->inSchool)(fn () => expect(AuditLog::query()->where('action', 'notice.created')->where('subject_id', $notice->id)->exists())->toBeTrue());
});

it('refuses to change the board to someone who may only look at it', function () {
    $notice = ($this->noticeFor)();

    $payload = ['day' => '2026-10-26', 'title' => 'Changed'];
    $this->actingAs($this->teacher)->post($this->url.'/communication/notices', $payload)->assertForbidden();
    $this->actingAs($this->accountant)->put($this->url.'/communication/notices/'.$notice->id, $payload)->assertForbidden();
    $this->actingAs($this->accountant)->delete($this->url.'/communication/notices/'.$notice->id)->assertForbidden();

    ($this->inSchool)(fn () => expect(Notice::query()->where('title', 'Changed')->exists())->toBeFalse());
    ($this->inSchool)(fn () => expect($notice->fresh()?->description)->toBe('School opens at 8:00.'));
});

it('refuses the board to the parent student', function () {
    $user = userWithRole($this->school, 'parent_student');

    $this->actingAs($user)->get($this->url.'/communication/notices')->assertForbidden();
});

it('opens the edit page, changes a notice and takes one off the board', function () {
    $notice = ($this->noticeFor)();

    $this->actingAs($this->owner)->get($this->url.'/communication/notices/'.$notice->id.'/edit')->assertOk()->assertSee($notice->title, false);

    $this->actingAs($this->owner)->put($this->url.'/communication/notices/'.$notice->id, [
        'day' => $notice->day->toDateString(), 'title' => 'Opening day moved', 'description' => null,
    ])->assertRedirect()->assertSessionHasNoErrors();
    expect($notice->fresh()?->description)->toBeNull()->and($notice->fresh()?->title)->toBe('Opening day moved');

    $this->actingAs($this->owner)->delete($this->url.'/communication/notices/'.$notice->id)->assertRedirect();
    ($this->inSchool)(fn () => expect(Notice::query()->whereKey($notice->id)->exists())->toBeFalse());
});

it('refuses a notice that answers no form rule', function () {
    $this->actingAs($this->owner)->post($this->url.'/communication/notices', ['day' => 'nope', 'title' => ''])
        ->assertSessionHasErrors(['day', 'title']);
    $this->actingAs($this->owner)->post($this->url.'/communication/notices', ['day' => now()->toDateString(), 'title' => str_repeat('x', 201)])
        ->assertSessionHasErrors('title');
});

it('keeps each school to its own board', function () {
    $otherNotice = ($this->noticeFor)($this->other);

    $this->actingAs($this->owner)->get($this->url.'/communication/notices')->assertOk()->assertDontSee($otherNotice->title);
    $this->actingAs($this->owner)->get($this->url.'/communication/notices/'.$otherNotice->id.'/edit')->assertNotFound();
    $this->actingAs($this->owner)->put($this->url.'/communication/notices/'.$otherNotice->id, [
        'day' => now()->toDateString(), 'title' => 'Hijacked',
    ])->assertNotFound();
    $this->actingAs($this->owner)->delete($this->url.'/communication/notices/'.$otherNotice->id)->assertNotFound();
});
