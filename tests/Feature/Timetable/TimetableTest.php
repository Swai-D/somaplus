<?php

use App\Enums\SubscriptionStatus;
use App\Models\SchoolClass;
use App\Models\Stream;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Tenant;
use App\Models\TimetableEntry;
use App\Models\TimetablePeriod;
use App\Models\User;
use App\Tenancy\TenantContext;
use Illuminate\Support\Arr;

beforeEach(function () {
    useTestHosts();

    $this->school = Tenant::factory()->create(['slug' => 'acme']);
    $this->other = Tenant::factory()->create(['slug' => 'beta']);
    $this->url = 'http://acme.somaplus.test';
    $this->head = userWithRole($this->school, 'head_teacher');

    $this->inSchool = fn (Closure $work, ?Tenant $school = null) => TenantContext::run($school ?? $this->school, $work);

    $this->w = makeExamWorld($this->school);

    [$this->p1, $this->p2, $this->lunch] = ($this->inSchool)(fn () => [
        TimetablePeriod::factory()->between('08:00', '08:40')->create(['name' => 'Period 1']),
        TimetablePeriod::factory()->between('08:40', '09:20')->create(['name' => 'Period 2']),
        TimetablePeriod::factory()->between('09:20', '09:40')->break()->create(['name' => 'Tea']),
    ]);

    // A teacher who signs in, with a record at the school.
    $account = makeSchoolUser($this->school, ['name' => 'Kimaro Mwalimu'], 'teacher');
    $this->kimUser = TenantContext::runAsSystem(fn () => User::findOrFail($account->id));
    $this->kim = ($this->inSchool)(fn () => Teacher::factory()->create(['user_id' => $this->kimUser->id]));

    $otherAccount = makeSchoolUser($this->school, ['name' => 'Juma Mwingine'], 'teacher');
    $this->juma = ($this->inSchool)(fn () => Teacher::factory()->create(['user_id' => $otherAccount->id]));

    $this->lesson = fn (array $override = []) => ($this->inSchool)(fn () => TimetableEntry::factory()->create($override + [
        'term_id' => $this->w['term']->id,
        'school_class_id' => $this->w['form1']->id,
        'subject_id' => $this->w['math']->id,
        'timetable_period_id' => $this->p1->id,
        'weekday' => 1,
    ]));

    $this->payload = fn (array $override = []) => $override + [
        'term_id' => $this->w['term']->id,
        'school_class_id' => $this->w['form1']->id,
        'subject_id' => $this->w['math']->id,
        'timetable_period_id' => $this->p1->id,
        'weekday' => 1,
    ];

    $this->save = fn (array $payload, ?User $user = null) => $this->actingAs($user ?? $this->head)
        ->post("{$this->url}/timetable/entries", $payload);

    $this->count = fn () => ($this->inSchool)(fn () => TimetableEntry::count());
});

it('shows the week of a class with the teacher, the room and the time', function () {
    ($this->lesson)(['teacher_id' => $this->kim->id, 'room' => 'Lab 3', 'weekday' => 2]);

    $this->actingAs($this->head)
        ->get("{$this->url}/timetable?term_id={$this->w['term']->id}&school_class_id={$this->w['form1']->id}")
        ->assertOk()
        ->assertSee('Mathematics')
        ->assertSee('Kimaro Mwalimu')
        ->assertSee('Lab 3')
        ->assertSee('08:00 - 08:40')
        ->assertSee(__('timetable.days.2'));
});

it('shows only the lessons of the stream asked for, plus those of the whole class', function () {
    ($this->lesson)(['stream_id' => $this->w['streamA']->id, 'subject_id' => $this->w['math']->id, 'weekday' => 1]);
    ($this->lesson)(['stream_id' => $this->w['streamB']->id, 'subject_id' => $this->w['english']->id, 'weekday' => 2]);
    ($this->lesson)(['stream_id' => null, 'subject_id' => $this->w['english']->id, 'timetable_period_id' => $this->p2->id, 'weekday' => 3]);

    $page = $this->actingAs($this->head)
        ->get("{$this->url}/timetable?term_id={$this->w['term']->id}&school_class_id={$this->w['form1']->id}&stream_id={$this->w['streamA']->id}")
        ->assertOk();

    $page->assertSee('Mathematics')->assertSee('English');
    // Stream A's Mathematics and the whole class's English: two cards. Stream B's English is not shown.
    expect(substr_count($page->getContent(), 'attendance-card border'))->toBe(2);
});

it('adds a lesson and keeps who made it', function () {
    ($this->save)(($this->payload)(['teacher_id' => $this->kim->id, 'room' => '  Room 5 ']))
        ->assertSessionHasNoErrors()->assertRedirect();

    ($this->inSchool)(function () {
        $lesson = TimetableEntry::first();
        expect($lesson->tenant_id)->toBe($this->school->id)
            ->and($lesson->created_by)->toBe($this->head->id)
            ->and($lesson->teacher_id)->toBe($this->kim->id)
            ->and($lesson->room)->toBe('Room 5')
            ->and($lesson->stream_id)->toBeNull();
    });
});

it('refuses a bad lesson', function (array $override, string $field) {
    ($this->save)(($this->payload)($override))->assertSessionHasErrors($field);
    expect(($this->count)())->toBe(0);
})->with([
    'no term' => [['term_id' => ''], 'term_id'],
    'no class' => [['school_class_id' => ''], 'school_class_id'],
    'no subject' => [['subject_id' => ''], 'subject_id'],
    'no period' => [['timetable_period_id' => ''], 'timetable_period_id'],
    'day 0' => [['weekday' => 0], 'weekday'],
    'day 8' => [['weekday' => 8], 'weekday'],
    'a long room' => [['room' => str_repeat('x', 51)], 'room'],
]);

it('refuses a break as a lesson slot', function () {
    ($this->save)(($this->payload)(['timetable_period_id' => $this->lunch->id]))
        ->assertSessionHasErrors('timetable_period_id');
});

it('refuses a second lesson for the same class in the same slot', function () {
    ($this->lesson)();

    ($this->save)(($this->payload)(['subject_id' => $this->w['english']->id]))
        ->assertSessionHasErrors('timetable_period_id');

    expect(($this->count)())->toBe(1);
});

it('treats a lesson for the whole class and a lesson of a stream as a clash, both ways', function () {
    ($this->lesson)(['stream_id' => $this->w['streamA']->id]);

    // The whole class cannot have a lesson while stream A does.
    ($this->save)(($this->payload)(['stream_id' => '']))->assertSessionHasErrors('timetable_period_id');
    // Stream A itself is taken.
    ($this->save)(($this->payload)(['stream_id' => $this->w['streamA']->id]))->assertSessionHasErrors('timetable_period_id');
    // Stream B is free at the same time.
    ($this->save)(($this->payload)(['stream_id' => $this->w['streamB']->id, 'subject_id' => $this->w['english']->id]))->assertSessionHasNoErrors();

    expect(($this->count)())->toBe(2);

    // The other way round: a stream cannot take a slot the whole class already holds.
    ($this->lesson)(['stream_id' => null, 'timetable_period_id' => $this->p2->id]);
    ($this->save)(($this->payload)(['stream_id' => $this->w['streamA']->id, 'timetable_period_id' => $this->p2->id]))
        ->assertSessionHasErrors('timetable_period_id');
});

it('lets two classes have a lesson in the same slot, and one class in different slots', function () {
    ($this->lesson)();

    ($this->save)(($this->payload)(['school_class_id' => $this->w['form2']->id]))->assertSessionHasNoErrors();
    ($this->save)(($this->payload)(['timetable_period_id' => $this->p2->id]))->assertSessionHasNoErrors();
    ($this->save)(($this->payload)(['weekday' => 2]))->assertSessionHasNoErrors();

    expect(($this->count)())->toBe(4);
});

it('refuses a teacher who already teaches elsewhere in that slot', function () {
    ($this->lesson)(['teacher_id' => $this->kim->id]);

    ($this->save)(($this->payload)(['school_class_id' => $this->w['form2']->id, 'teacher_id' => $this->kim->id]))
        ->assertSessionHasErrors('teacher_id');

    // The same teacher in another slot, and another teacher in this slot, are fine.
    ($this->save)(($this->payload)(['school_class_id' => $this->w['form2']->id, 'teacher_id' => $this->kim->id, 'timetable_period_id' => $this->p2->id]))->assertSessionHasNoErrors();
    ($this->save)(($this->payload)(['school_class_id' => $this->w['form2']->id, 'teacher_id' => $this->juma->id]))->assertSessionHasNoErrors();
});

it('refuses a room that is already used in that slot, whatever the letter case', function () {
    ($this->lesson)(['room' => 'Lab 3']);

    ($this->save)(($this->payload)(['school_class_id' => $this->w['form2']->id, 'room' => 'lab 3']))
        ->assertSessionHasErrors('room');
    ($this->save)(($this->payload)(['school_class_id' => $this->w['form2']->id, 'room' => 'Lab 4']))
        ->assertSessionHasNoErrors();
});

it('keeps a teacher inside what the school assigned them', function () {
    ($this->inSchool)(function () {
        $this->kim->subjects()->attach($this->w['math']->id);
        $this->kim->classes()->attach($this->w['form1']->id);
    });

    // Not assigned English.
    ($this->save)(($this->payload)(['subject_id' => $this->w['english']->id, 'teacher_id' => $this->kim->id]))
        ->assertSessionHasErrors('teacher_id');
    // Not assigned Form 2.
    ($this->save)(($this->payload)(['school_class_id' => $this->w['form2']->id, 'teacher_id' => $this->kim->id]))
        ->assertSessionHasErrors('teacher_id');
    // Assigned Mathematics in Form 1.
    ($this->save)(($this->payload)(['teacher_id' => $this->kim->id]))->assertSessionHasNoErrors();
    // A teacher with nothing assigned yet is not restricted.
    ($this->save)(($this->payload)(['school_class_id' => $this->w['form2']->id, 'subject_id' => $this->w['english']->id, 'teacher_id' => $this->juma->id]))
        ->assertSessionHasNoErrors();
});

it('refuses a teacher who is out of duty', function () {
    ($this->inSchool)(fn () => $this->kim->forceFill(['status' => Teacher::INACTIVE])->save());

    ($this->save)(($this->payload)(['teacher_id' => $this->kim->id]))->assertSessionHasErrors('teacher_id');
});

it('changes a lesson without clashing with itself, and deletes it', function () {
    $lesson = ($this->lesson)(['teacher_id' => $this->kim->id, 'room' => 'Lab 3']);

    $this->actingAs($this->head)->put("{$this->url}/timetable/entries/{$lesson->id}", ($this->payload)([
        'teacher_id' => $this->kim->id, 'room' => 'Lab 3', 'subject_id' => $this->w['english']->id,
    ]))->assertSessionHasNoErrors()->assertRedirect();

    expect(($this->inSchool)(fn () => TimetableEntry::find($lesson->id)->subject_id))->toBe($this->w['english']->id);

    $this->actingAs($this->head)->get("{$this->url}/timetable/entries/{$lesson->id}/edit")->assertOk()->assertSee('Lab 3');

    $this->actingAs($this->head)->delete("{$this->url}/timetable/entries/{$lesson->id}")->assertRedirect();
    expect(($this->count)())->toBe(0);
});

it('does not take a class, subject, teacher or lesson of another school', function () {
    [$foreignClass, $foreignSubject, $foreignPeriod, $foreignStream] = ($this->inSchool)(fn () => [
        SchoolClass::factory()->create(),
        Subject::factory()->create(),
        TimetablePeriod::factory()->create(['name' => 'Theirs']),
        Stream::factory()->create(['school_class_id' => SchoolClass::factory()->create()->id]),
    ], $this->other);
    $foreignTeacher = ($this->inSchool)(fn () => Teacher::factory()->create(['user_id' => User::factory()->create()->id]), $this->other);

    ($this->save)(($this->payload)(['school_class_id' => $foreignClass->id]))->assertSessionHasErrors('school_class_id');
    ($this->save)(($this->payload)(['subject_id' => $foreignSubject->id]))->assertSessionHasErrors('subject_id');
    ($this->save)(($this->payload)(['timetable_period_id' => $foreignPeriod->id]))->assertSessionHasErrors('timetable_period_id');
    ($this->save)(($this->payload)(['teacher_id' => $foreignTeacher->id]))->assertSessionHasErrors('teacher_id');
    ($this->save)(($this->payload)(['stream_id' => $foreignStream->id]))->assertSessionHasErrors('stream_id');

    // A stream of another class of the same school is refused too.
    ($this->save)(($this->payload)(['school_class_id' => $this->w['form2']->id, 'stream_id' => $this->w['streamA']->id]))
        ->assertSessionHasErrors('stream_id');

    expect(($this->count)())->toBe(0);
});

it('shows nothing of another school', function () {
    $theirWorld = makeExamWorld($this->other);
    $theirPeriod = ($this->inSchool)(fn () => TimetablePeriod::factory()->create(['name' => 'Zxqtheirs']), $this->other);
    ($this->inSchool)(fn () => TimetableEntry::factory()->create([
        'term_id' => $theirWorld['term']->id, 'school_class_id' => $theirWorld['form1']->id,
        'subject_id' => $theirWorld['math']->id, 'timetable_period_id' => $theirPeriod->id, 'weekday' => 1, 'room' => 'Zxqroom',
    ]), $this->other);

    $this->actingAs($this->head)->get("{$this->url}/timetable?school_class_id={$theirWorld['form1']->id}")->assertSessionHasErrors('school_class_id');
    $this->actingAs($this->head)->get("{$this->url}/timetable?term_id={$theirWorld['term']->id}")->assertSessionHasErrors('term_id');
    $this->actingAs($this->head)->get("{$this->url}/timetable/periods")->assertOk()->assertDontSee('Zxqtheirs');
});

it('shows a teacher their own week and any teacher\'s week to those who may look', function () {
    ($this->lesson)(['teacher_id' => $this->kim->id, 'room' => 'Lab 3']);
    ($this->lesson)(['teacher_id' => $this->juma->id, 'school_class_id' => $this->w['form2']->id, 'room' => 'Zxqjuma']);

    $this->actingAs($this->kimUser)->get("{$this->url}/timetable/mine?term_id={$this->w['term']->id}")
        ->assertOk()->assertSee('Lab 3')->assertSee('Form 1')->assertDontSee('Zxqjuma');

    $this->actingAs($this->head)->get("{$this->url}/timetable/teachers?term_id={$this->w['term']->id}&teacher_id={$this->juma->id}")
        ->assertOk()->assertSee('Zxqjuma')->assertSee('Form 2')->assertDontSee('Lab 3');
});

it('says so when the person has no teacher record', function () {
    $this->actingAs($this->head)->get("{$this->url}/timetable/mine")->assertOk()->assertSee(__('timetable.not_a_teacher'));
});

it('sets the periods of the day and refuses nonsense', function () {
    $this->actingAs($this->head)->post("{$this->url}/timetable/periods", ['name' => 'Period 3', 'starts_at' => '09:40', 'ends_at' => '10:20'])
        ->assertSessionHasNoErrors()->assertRedirect();
    expect(($this->inSchool)(fn () => TimetablePeriod::where('name', 'Period 3')->exists()))->toBeTrue();

    // Ends before it starts, overlaps, same name.
    $this->actingAs($this->head)->post("{$this->url}/timetable/periods", ['name' => 'Bad', 'starts_at' => '11:00', 'ends_at' => '10:00'])->assertSessionHasErrors('ends_at');
    $this->actingAs($this->head)->post("{$this->url}/timetable/periods", ['name' => 'Overlap', 'starts_at' => '08:30', 'ends_at' => '08:50'])->assertSessionHasErrors('starts_at');
    $this->actingAs($this->head)->post("{$this->url}/timetable/periods", ['name' => 'Period 1', 'starts_at' => '12:00', 'ends_at' => '12:40'])->assertSessionHasErrors('name');
    // A slot right after another is not an overlap.
    $this->actingAs($this->head)->post("{$this->url}/timetable/periods", ['name' => 'Period 4', 'starts_at' => '10:20', 'ends_at' => '11:00'])->assertSessionHasNoErrors();
});

it('keeps a slot that holds lessons from being deleted or turned into a break', function () {
    ($this->lesson)();

    $this->actingAs($this->head)->delete("{$this->url}/timetable/periods/{$this->p1->id}")->assertSessionHasErrors('delete');
    expect(($this->inSchool)(fn () => TimetablePeriod::whereKey($this->p1->id)->exists()))->toBeTrue();

    $this->actingAs($this->head)->put("{$this->url}/timetable/periods/{$this->p1->id}", [
        'name' => 'Period 1', 'starts_at' => '08:00', 'ends_at' => '08:40', 'is_break' => '1',
    ])->assertSessionHasErrors('is_break');

    // An unused slot can go.
    $this->actingAs($this->head)->delete("{$this->url}/timetable/periods/{$this->p2->id}")->assertRedirect();
    expect(($this->inSchool)(fn () => TimetablePeriod::whereKey($this->p2->id)->exists()))->toBeFalse();
});

it('copies a term into another and does nothing the second time', function () {
    ($this->lesson)(['teacher_id' => $this->kim->id]);
    ($this->lesson)(['weekday' => 2, 'subject_id' => $this->w['english']->id]);

    $copy = fn () => $this->actingAs($this->head)->post("{$this->url}/timetable/copy", [
        'from_term_id' => $this->w['term']->id, 'to_term_id' => $this->w['term2']->id,
    ]);

    $copy()->assertSessionHasNoErrors()->assertRedirect();
    expect(($this->inSchool)(fn () => TimetableEntry::where('term_id', $this->w['term2']->id)->count()))->toBe(2);

    $copy()->assertSessionHasNoErrors();
    expect(($this->inSchool)(fn () => TimetableEntry::where('term_id', $this->w['term2']->id)->count()))->toBe(2);

    $this->actingAs($this->head)->post("{$this->url}/timetable/copy", [
        'from_term_id' => $this->w['term']->id, 'to_term_id' => $this->w['term']->id,
    ])->assertSessionHasErrors('to_term_id');
});

it('keeps the lessons of a teacher who is removed, without a teacher', function () {
    $lesson = ($this->lesson)(['teacher_id' => $this->kim->id]);

    ($this->inSchool)(fn () => $this->kim->delete());

    expect(($this->inSchool)(fn () => TimetableEntry::find($lesson->id)->teacher_id))->toBeNull();
});

it('keeps a term, class, stream, subject or period that holds lessons from being deleted', function (string $url) {
    ($this->lesson)(['stream_id' => $this->w['streamA']->id]);

    $this->actingAs($this->head)->delete($this->url.str_replace(
        ['{term}', '{class}', '{stream}', '{subject}'],
        [$this->w['term']->id, $this->w['form1']->id, $this->w['streamA']->id, $this->w['math']->id],
        $url,
    ))->assertSessionHasErrors('delete');
})->with([
    'term' => ['/academics/terms/{term}'],
    'class' => ['/academics/classes/{class}'],
    'stream' => ['/academics/streams/{stream}'],
    'subject' => ['/academics/subjects/{subject}'],
]);

it('lets the right roles look and change', function (string $role, bool $looks, bool $changes) {
    $user = userWithRole($this->school, $role);

    $page = $this->actingAs($user)->get("{$this->url}/timetable");
    $looks ? $page->assertOk() : $page->assertForbidden();

    $post = ($this->save)(($this->payload)(), $user);
    $changes ? $post->assertRedirect() : $post->assertForbidden();

    $periods = $this->actingAs($user)->get("{$this->url}/timetable/periods");
    $changes ? $periods->assertOk() : $periods->assertForbidden();
})->with([
    'owner' => ['owner', true, true],
    'head teacher' => ['head_teacher', true, true],
    'teacher' => ['teacher', true, false],
    'accountant' => ['accountant', false, false],
    'parent' => ['parent_student', false, false],
]);

it('lets a read-only school look but not change', function () {
    $lesson = ($this->lesson)();
    $this->school->update(['subscription_status' => SubscriptionStatus::ReadOnly]);
    $head = userWithRole($this->school, 'head_teacher');

    $this->actingAs($head)->get("{$this->url}/timetable?term_id={$this->w['term']->id}&school_class_id={$this->w['form1']->id}")->assertOk();
    ($this->save)(($this->payload)(['weekday' => 2]), $head)->assertForbidden();
    $this->actingAs($head)->delete("{$this->url}/timetable/entries/{$lesson->id}")->assertForbidden();
    $this->actingAs($head)->post("{$this->url}/timetable/periods", ['name' => 'X', 'starts_at' => '15:00', 'ends_at' => '15:40'])->assertForbidden();
});

it('sends a guest to sign in', function () {
    $this->get("{$this->url}/timetable")->assertRedirect("{$this->url}/login");
});

it('gives the central address no timetable', function () {
    $this->actingAs(User::factory()->superAdmin()->create())->get('http://somaplus.test/timetable')->assertForbidden();
});

it('shows the Timetable entries in the sidebar only to people who may use them', function () {
    $this->actingAs($this->head)->get($this->url)
        ->assertSee('/timetable"', false)->assertSee('/timetable/teachers"', false)->assertSee('/timetable/periods"', false);

    $this->actingAs($this->kimUser)->get($this->url)
        ->assertSee('/timetable/mine"', false)->assertDontSee('/timetable/periods"', false);

    $this->actingAs(userWithRole($this->school, 'accountant'))->get($this->url)->assertDontSee('/timetable"', false);
});

it('gives a parent only the timetable of their own child', function () {
    $parent = userWithRole($this->school, 'parent_student');

    // A child that is not linked to this parent's account.
    $this->actingAs($parent)->get("{$this->url}/family/children/999999/timetable")->assertNotFound();
    // A teacher is not a parent.
    $this->actingAs($this->kimUser)->get("{$this->url}/family/children/1/timetable")->assertForbidden();
});

it('shows the page in Swahili when that language is chosen', function () {
    $this->actingAs($this->head)->withSession(['locale' => 'sw'])->get("{$this->url}/timetable")
        ->assertOk()
        ->assertSee(__('timetable.intro', [], 'sw'))
        ->assertDontSee(__('timetable.intro', [], 'en'));
});

it('has every timetable text in English and Swahili', function () {
    $keys = fn (string $locale) => array_keys(Arr::dot(require base_path("lang/{$locale}/timetable.php")));

    expect($keys('sw'))->toBe($keys('en'));
});
