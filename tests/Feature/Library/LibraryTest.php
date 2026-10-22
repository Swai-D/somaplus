<?php

use App\Enums\SubscriptionStatus;
use App\Models\AuditLog;
use App\Models\Book;
use App\Models\BookIssue;
use App\Models\Enrollment;
use App\Models\Guardian;
use App\Models\LibraryMember;
use App\Models\Student;
use App\Models\Tenant;
use App\Tenancy\TenantContext;

beforeEach(function () {
    useTestHosts();

    $this->school = Tenant::factory()->create(['slug' => 'acme', 'name' => 'Acme Secondary']);
    $this->url = 'http://acme.somaplus.test';
    $this->owner = userWithRole($this->school, 'owner');
    $this->head = userWithRole($this->school, 'head_teacher');
    $this->teacher = userWithRole($this->school, 'teacher');
    $this->accountant = userWithRole($this->school, 'accountant');
    $this->parent = userWithRole($this->school, 'parent_student');
    $this->inSchool = fn (Closure $work, ?Tenant $school = null) => TenantContext::run($school ?? $this->school, $work);
    $this->w = makeExamWorld($this->school);

    // A pupil of the school with a guardian, placed in Form 1 of this year. The phone of the
    // guardian is unique inside the school, so the tests make it from the pupil's name.
    $this->aPupil = fn (string $first = 'Ashery', string $last = 'Mwakalinga') => ($this->inSchool)(function () use ($first, $last) {
        $student = Student::factory()->create(['first_name' => $first, 'last_name' => $last]);
        Enrollment::factory()->create([
            'student_id' => $student->id,
            'academic_year_id' => $this->w['year']->id,
            'school_class_id' => $this->w['form1']->id,
        ]);
        $guardian = Guardian::factory()->create([
            'name' => 'The guardian of '.$first,
            'phone' => '07'.str_pad((string) (crc32($first.' '.$last) % 100000000), 8, '0', STR_PAD_LEFT),
        ]);
        $student->guardians()->attach($guardian->id, ['relationship' => 'father', 'is_primary' => true]);

        return $student;
    });
});

/** A book of the school: two copies on the shelf when nothing is said. */
function aBook(Tenant $school, string $name = 'Kiswahili Fasaha', int $quantity = 2): Book
{
    return TenantContext::run($school, fn () => Book::factory()->create(['name' => $name, 'quantity' => $quantity]));
}

/** A member of the library: one pupil with their card. */
function aMember(Tenant $school, Student $student, string $card = 'LIB-0001'): LibraryMember
{
    return TenantContext::run($school, fn () => LibraryMember::factory()->create([
        'student_id' => $student->id,
        'card_number' => $card,
        'joined_on' => '2026-01-10',
    ]));
}

/** One copy of a book that went out to a member, on the shelf when it is not returned. */
function anIssue(Tenant $school, Book $book, LibraryMember $member, ?string $dueOn = null): BookIssue
{
    return TenantContext::run($school, fn () => $book->issues()->create([
        'member_id' => $member->id,
        'issued_on' => '2026-02-01',
        'due_on' => $dueOn,
    ]));
}

it('lists the books of the library with what is on the shelf', function () {
    $book = aBook($this->school, 'Kiswahili Fasaha', 3);

    $member = aMember($this->school, ($this->aPupil)());
    anIssue($this->school, $book, $member);

    $this->actingAs($this->owner)->get($this->url.'/library/books')->assertOk()
        ->assertSee('Kiswahili Fasaha')
        ->assertSee('3')
        ->assertSee('2');
});

it('gives the book list the search box of the datatable of the template', function () {
    aBook($this->school);

    $html = $this->actingAs($this->owner)->get($this->url.'/library/books')->assertOk()->getContent();

    expect($html)->toContain('class="datatable table')
        ->toContain('class="search-input"');
});

it('shows the form of a new book in a pop-up of the list', function () {
    $html = $this->actingAs($this->owner)->get($this->url.'/library/books')->assertOk()->getContent();

    expect($html)->toContain('id="book-modal"')
        ->toContain('data-bs-target="#book-modal"')
        ->toContain('name="form" type="hidden" value="book-modal"');
});

it('opens the book list with the pop-up ready instead of a page of its own', function () {
    $this->actingAs($this->owner)->get($this->url.'/library/books/create')
        ->assertRedirect($this->url.'/library/books?add=book-modal');
});

it('adds a book and turns its price into the money of the school', function () {
    $subject = $this->w['math'];

    $this->actingAs($this->owner)->post($this->url.'/library/books', [
        'subject_id' => $subject->id,
        'name' => 'Hisabati Kwa Shule',
        'publisher' => 'Longman',
        'author' => 'N. Wawa',
        'book_number' => 'BK-001',
        'rack_number' => 'A1',
        'quantity' => 10,
        'price' => '5500.50',
    ])->assertRedirect($this->url.'/library/books')->assertSessionHasNoErrors();

    $book = ($this->inSchool)(fn () => Book::query()->sole());

    expect($book->name)->toBe('Hisabati Kwa Shule')
        ->and($book->quantity)->toBe(10)
        ->and($book->price)->toBe(550050)
        ->and($book->availableCount())->toBe(10)
        ->and($book->priceMoney()->format())->toContain('5,500');
});

it('refuses a book without a name or copies', function () {
    $this->actingAs($this->owner)->post($this->url.'/library/books', ['name' => '', 'quantity' => ''])
        ->assertSessionHasErrors(['name', 'quantity']);

    expect(($this->inSchool)(fn () => Book::query()->count()))->toBe(0);
});

it('refuses a book number another book of the school has', function () {
    aBook($this->school, 'Kitabu cha Kwanza', 1)->update(['book_number' => 'BK-001']);

    $this->actingAs($this->owner)->post($this->url.'/library/books', [
        'name' => 'Kitabu cha Pili', 'quantity' => 1, 'book_number' => 'BK-001',
    ])->assertSessionHasErrors('book_number');
});

it('changes a book on its own page', function () {
    $book = aBook($this->school, 'Jina la Zamani', 1);

    $html = $this->actingAs($this->owner)->get($this->url."/library/books/{$book->id}/edit")->assertOk()->getContent();
    expect($html)->toContain('Jina la Zamani');

    $this->actingAs($this->owner)->put($this->url."/library/books/{$book->id}", [
        'name' => 'Jina Jipya', 'quantity' => 4, 'price' => '2000',
    ])->assertRedirect($this->url.'/library/books')->assertSessionHasNoErrors();

    $book->refresh();
    expect($book->name)->toBe('Jina Jipya')->and($book->quantity)->toBe(4)->and($book->price)->toBe(200000);
});

it('takes a book the library never lent out of the list', function () {
    $book = aBook($this->school);

    $this->actingAs($this->owner)->delete($this->url."/library/books/{$book->id}")
        ->assertRedirect($this->url.'/library/books')->assertSessionHasNoErrors();

    expect(($this->inSchool)(fn () => Book::query()->count()))->toBe(0)
        ->and(($this->inSchool)(fn () => AuditLog::where('action', 'book.deleted')->where('subject_id', $book->id)->exists()))->toBeTrue();
});

it('keeps a book the school ever lent, because the record of the issue says it is this book', function () {
    $book = aBook($this->school);
    $member = aMember($this->school, ($this->aPupil)());
    anIssue($this->school, $book, $member);

    $this->actingAs($this->owner)->delete($this->url."/library/books/{$book->id}")
        ->assertSessionHasErrors('delete');

    expect(($this->inSchool)(fn () => Book::query()->count()))->toBe(1)->and($book->refresh()->exists())->toBeTrue();
});

it('searches for a book with a POST, so what is typed never lands in a URL', function () {
    aBook($this->school, 'Kiswahili Fasaha');
    aBook($this->school, 'Hisabati Kwa Shule');

    $this->actingAs($this->owner)->post($this->url.'/library/books/search', ['term' => 'Kiswahili'])
        ->assertOk()->assertSee('Kiswahili Fasaha')->assertDontSee('Hisabati Kwa Shule');

    $this->actingAs($this->owner)->post($this->url.'/library/books/search', ['term' => 'a'])
        ->assertSessionHasErrors('term');
});

it('makes a pupil of the school a member with a card', function () {
    $student = ($this->aPupil)();

    $this->actingAs($this->owner)->post($this->url.'/library/members', [
        'student_id' => $student->id,
        'card_number' => 'LIB-0001',
        'joined_on' => '2026-01-10',
    ])->assertRedirect($this->url.'/library/members')->assertSessionHasNoErrors();

    $member = ($this->inSchool)(fn () => LibraryMember::query()->sole());
    expect($member->card_number)->toBe('LIB-0001')->and($member->student_id)->toBe($student->id);
});

it('gives a pupil of the school one card at most', function () {
    $student = ($this->aPupil)();
    aMember($this->school, $student, 'LIB-0001');

    $this->actingAs($this->owner)->post($this->url.'/library/members', [
        'student_id' => $student->id, 'card_number' => 'LIB-0002', 'joined_on' => '2026-01-10',
    ])->assertSessionHasErrors('student_id');
});

it('refuses a card number another member of the school has', function () {
    $other = ($this->aPupil)('Baraka', 'Mushi');
    aMember($this->school, $other, 'LIB-0001');
    $student = ($this->aPupil)('Chacha', 'Mwita');

    $this->actingAs($this->owner)->post($this->url.'/library/members', [
        'student_id' => $student->id, 'card_number' => 'LIB-0001', 'joined_on' => '2026-01-10',
    ])->assertSessionHasErrors('card_number');
});

it('shows the members with the class of the pupil and the phone of the guardian', function () {
    $student = ($this->aPupil)();
    aMember($this->school, $student, 'LIB-0001');
    $phone = ($this->inSchool)(fn () => $student->guardians()->first()->phone);

    $this->actingAs($this->owner)->get($this->url.'/library/members')->assertOk()
        ->assertSee('LIB-0001')
        ->assertSee('Ashery')
        ->assertSee('Form 1')
        ->assertSee($phone);
});

it('shows the page of a member with their borrowed books', function () {
    $book = aBook($this->school, 'Kiswahili Fasaha');
    $member = aMember($this->school, ($this->aPupil)(), 'LIB-0001');
    $issue = anIssue($this->school, $book, $member);
    anIssue($this->school, aBook($this->school, 'Hisabati'), $member)->update(['returned_on' => '2026-02-05']);

    $this->actingAs($this->owner)->get($this->url."/library/members/{$member->id}")->assertOk()
        ->assertSee('LIB-0001')
        ->assertSee('Kiswahili Fasaha');
});

it('changes whose card it is', function () {
    $student = ($this->aPupil)('Doto', 'Mkinga');
    $member = aMember($this->school, ($this->aPupil)('Zuri', 'Mwana'), 'LIB-0001');

    $this->actingAs($this->owner)->put($this->url."/library/members/{$member->id}", [
        'student_id' => $student->id, 'card_number' => 'LIB-0009', 'joined_on' => '2026-01-10',
    ])->assertSessionHasNoErrors();

    $member->refresh();
    expect($member->student_id)->toBe($student->id)->and($member->card_number)->toBe('LIB-0009');
});

it('takes a member who never borrowed out of the library', function () {
    $member = aMember($this->school, ($this->aPupil)());

    $this->actingAs($this->owner)->delete($this->url."/library/members/{$member->id}")
        ->assertRedirect($this->url.'/library/members')->assertSessionHasNoErrors();

    expect(($this->inSchool)(fn () => LibraryMember::query()->count()))->toBe(0);
});

it('keeps a member the library ever lent a book to', function () {
    $book = aBook($this->school);
    $member = aMember($this->school, ($this->aPupil)());
    $issue = anIssue($this->school, $book, $member);
    ($this->inSchool)(fn () => $issue->update(['returned_on' => '2026-02-05']));

    $this->actingAs($this->owner)->delete($this->url."/library/members/{$member->id}")
        ->assertSessionHasErrors('delete');

    expect(($this->inSchool)(fn () => LibraryMember::query()->count()))->toBe(1);
});

it('searches for a member with a POST, so what is typed never lands in a URL', function () {
    aMember($this->school, ($this->aPupil)('Ashery', 'Mwakalinga'), 'LIB-0001');
    aMember($this->school, ($this->aPupil)('Zuhura', 'Mushi'), 'LIB-0002');

    $this->actingAs($this->owner)->post($this->url.'/library/members/search', ['term' => 'LIB-0001'])
        ->assertOk()->assertSee('Ashery')->assertDontSee('Zuhura');

    $this->actingAs($this->owner)->post($this->url.'/library/members/search', ['term' => 'Zuhura'])
        ->assertOk()->assertSee('Zuhura')->assertDontSee('LIB-0001');
});

it('shows the issues with the copies out first and handles the filter', function () {
    $first = aMember($this->school, ($this->aPupil)('Ashery', 'Mwakalinga'), 'LIB-0001');
    $second = aMember($this->school, ($this->aPupil)('Zuhura', 'Mushi'), 'LIB-0002');

    $book = aBook($this->school, 'Kiswahili Fasaha', 2);
    anIssue($this->school, $book, $first);
    $old = anIssue($this->school, $book, $second);
    ($this->inSchool)(fn () => $old->update(['returned_on' => '2026-02-05']));

    $this->actingAs($this->owner)->get($this->url.'/library/issues')->assertOk()->assertOk()
        ->assertSee('Ashery')->assertSee('Zuhura')
        ->assertSeeInOrder(['Ashery', 'Zuhura']);

    $this->actingAs($this->owner)->get($this->url.'/library/issues?filter=open')->assertOk()
        ->assertSee('Ashery');
});

it('shows the issue form in a pop-up of the list with the copies that are on the shelf', function () {
    aBook($this->school, 'Kiswahili Fasaha', 2);
    $member = aMember($this->school, ($this->aPupil)());

    $html = $this->actingAs($this->owner)->get($this->url.'/library/issues')->assertOk()->getContent();

    expect($html)->toContain('id="issue-modal"')
        ->toContain('Kiswahili Fasaha (On the shelf: 2)')
        ->toContain($member->card_number);
});

it('opens the issue list with the pop-up ready instead of a page of its own', function () {
    $this->actingAs($this->owner)->get($this->url.'/library/issues/create')
        ->assertRedirect($this->url.'/library/issues?add=issue-modal');
});

it('puts a copy out and counts it off the shelf', function () {
    $book = aBook($this->school, 'Kiswahili Fasaha', 2);
    $member = aMember($this->school, ($this->aPupil)());

    $this->actingAs($this->owner)->post($this->url.'/library/issues', [
        'member_id' => $member->id,
        'book_id' => $book->id,
        'issued_on' => today()->toDateString(),
        'due_on' => now()->addDays(14)->toDateString(),
    ])->assertRedirect($this->url.'/library/issues')->assertSessionHasNoErrors();

    $issue = ($this->inSchool)(fn () => BookIssue::query()->sole());
    expect($issue->member_id)->toBe($member->id)->and($issue->isReturned())->toBeFalse()
        ->and($issue->due_on->toDateString())->toBe(now()->addDays(14)->toDateString());

    $book->refresh();
    expect($book->availableCount())->toBe(1)->and($book->outCount())->toBe(1)
        ->and(($this->inSchool)(fn () => AuditLog::where('action', 'issue.created')->where('subject_id', $issue->id)->exists()))->toBeTrue();
});

it('refuses an issue of the last copy more than once or a day in the future', function () {
    $book = aBook($this->school, 'Kiswahili Fasaha', 1);
    $member = aMember($this->school, ($this->aPupil)());
    $other = aMember($this->school, ($this->aPupil)('Zuhura', 'Mushi'), 'LIB-0002');

    $this->actingAs($this->owner)->post($this->url.'/library/issues', [
        'member_id' => $member->id, 'book_id' => $book->id,
        'issued_on' => now()->addDay()->toDateString(),
        'due_on' => now()->addDays(14)->toDateString(),
    ])->assertSessionHasErrors('issued_on');

    anIssue($this->school, $book, $other);

    $this->actingAs($this->owner)->post($this->url.'/library/issues', [
        'member_id' => $member->id, 'book_id' => $book->id, 'issued_on' => today()->toDateString(),
        'due_on' => now()->addDays(14)->toDateString(),
    ])->assertSessionHasErrors('book_id');

    expect(($this->inSchool)(fn () => BookIssue::query()->count()))->toBe(1);
});

it('takes a copy back today and refuses to take it back twice', function () {
    $book = aBook($this->school);
    $issue = anIssue($this->school, $book, aMember($this->school, ($this->aPupil)()));

    $this->actingAs($this->owner)->post($this->url."/library/issues/{$issue->id}/return")
        ->assertRedirect($this->url.'/library/issues')->assertSessionHasNoErrors();

    $issue->refresh();
    expect($issue->returned_on->toDateString())->toBe(today()->toDateString())
        ->and($book->refresh()->availableCount())->toBe(2)
        ->and(($this->inSchool)(fn () => AuditLog::where('action', 'issue.returned')->where('subject_id', $issue->id)->exists()))->toBeTrue();

    $this->actingAs($this->owner)->post($this->url."/library/issues/{$issue->id}/return")
        ->assertSessionHasErrors('returned_on');
});

it('searches for an issue with a POST, so what is typed never lands in a URL', function () {
    $book = aBook($this->school, 'Kiswahili Fasaha');
    $member = aMember($this->school, ($this->aPupil)('Ashery', 'Mwakalinga'), 'LIB-0001');
    anIssue($this->school, $book, $member);

    $this->actingAs($this->owner)->post($this->url.'/library/issues/search', ['term' => 'Kiswahili'])
        ->assertOk()->assertSee('Ashery');

    $this->actingAs($this->owner)->post($this->url.'/library/issues/search', ['term' => 'LIB-0001'])
        ->assertOk()->assertSee('Kiswahili Fasaha');
});

it('keeps the library away from the accounts that may not see it', function (string $account) {
    $this->actingAs($this->{$account})->get($this->url.'/library/books')->assertForbidden();
})->with(['accountant', 'parent']);

it('lets a teacher look at the library without changing it', function () {
    $book = aBook($this->school, 'Kiswahili Fasaha');
    aMember($this->school, ($this->aPupil)());

    $this->actingAs($this->teacher)->get($this->url.'/library/books')->assertOk()->assertSee('Kiswahili Fasaha');
    $this->actingAs($this->teacher)->get($this->url.'/library/members')->assertOk();

    $page = $this->actingAs($this->teacher)->get($this->url.'/library/issues')->assertOk();
    $html = $page->getContent();
    expect($html)->not->toContain('data-bs-target="#issue-modal"')
        ->and($html)->not->toContain('id="issue-modal"');

    $this->actingAs($this->teacher)->post($this->url.'/library/books', ['name' => 'Changanja', 'quantity' => 1])
        ->assertForbidden();
});

it('lets a head teacher work in the library', function () {
    aBook($this->school);
    aMember($this->school, ($this->aPupil)());

    $this->actingAs($this->head)->get($this->url.'/library/books')->assertOk();
    $this->actingAs($this->head)->get($this->url.'/library/members')->assertOk();
});

it('does not reach a member of another school', function () {
    $other = Tenant::factory()->create(['slug' => 'beta', 'name' => 'Beta Academy']);
    $otherMember = TenantContext::run($other, function () {
        $student = Student::factory()->create(['first_name' => 'Mpishano']);

        return LibraryMember::factory()->create(['student_id' => $student->id, 'card_number' => 'B-0001']);
    });

    $this->actingAs($this->owner)->get($this->url."/library/members/{$otherMember->id}")->assertNotFound();
});

it('lets a read-only school look but not save', function () {
    $book = aBook($this->school);
    $member = aMember($this->school, ($this->aPupil)());
    $this->school->update(['subscription_status' => SubscriptionStatus::ReadOnly]);

    $this->actingAs($this->owner)->get($this->url.'/library/books')->assertOk();

    $this->actingAs($this->owner)->post($this->url.'/library/books', ['name' => 'X', 'quantity' => 1])
        ->assertForbidden();
    $this->actingAs($this->owner)->post($this->url.'/library/issues', [
        'member_id' => $member->id, 'book_id' => $book->id, 'issued_on' => today()->toDateString(),
    ])->assertForbidden();
});

it('shows the library in the menu of the school', function () {
    aBook($this->school);

    $html = $this->actingAs($this->owner)->get($this->url.'/')->assertOk()->getContent();

    expect($html)->toContain(route('library.books.index'))
        ->toContain(route('library.members.index'))
        ->toContain(route('library.issues.index'))
        ->toContain(__('menu.book_list'))
        ->toContain(__('menu.issue_return'));
});

it('refuses a day of return before the day the copy went out', function () {
    $book = aBook($this->school, 'Kiswahili Fasaha');
    $member = aMember($this->school, ($this->aPupil)());

    $this->actingAs($this->owner)->post($this->url.'/library/issues', [
        'member_id' => $member->id, 'book_id' => $book->id,
        'issued_on' => today()->toDateString(),
        'due_on' => now()->subDay()->toDateString(),
    ])->assertSessionHasErrors('due_on');

    expect(($this->inSchool)(fn () => BookIssue::query()->count()))->toBe(0);
});

it('marks a copy whose return day has passed as overdue', function () {
    $book = aBook($this->school, 'Kiswahili Fasaha');
    $member = aMember($this->school, ($this->aPupil)());
    anIssue($this->school, $book, $member, now()->subDay()->toDateString());

    $this->actingAs($this->owner)->get($this->url.'/library/issues')->assertOk()
        ->assertSee(__('library.issues.list.overdue'));
    $this->actingAs($this->owner)->get($this->url.'/library/members/'.$member->id)->assertOk()
        ->assertSee(__('library.issues.list.overdue'));
});

it('does not mark a copy that is still within its return day', function () {
    $book = aBook($this->school, 'Kiswahili Fasaha');
    $member = aMember($this->school, ($this->aPupil)());
    anIssue($this->school, $book, $member, now()->addDay()->toDateString());

    $this->actingAs($this->owner)->get($this->url.'/library/issues')->assertOk()
        ->assertDontSee(__('library.issues.list.overdue'));
});

it('prints the library card of a member', function () {
    $member = aMember($this->school, ($this->aPupil)());

    $this->actingAs($this->owner)->get($this->url.'/library/members/'.$member->id.'/card')
        ->assertOk()
        ->assertSee($member->card_number)
        ->assertSee('Acme Secondary')
        ->assertSee(__('library.members.card.tag'));

    expect(($this->inSchool)(fn () => AuditLog::where('action', 'library.card_printed')->where('subject_id', $member->id)->exists()))->toBeTrue();
});

it('sends an account that may not use the library away from the card', function () {
    $member = aMember($this->school, ($this->aPupil)());

    $this->actingAs($this->accountant)->get($this->url.'/library/members/'.$member->id.'/card')->assertForbidden();
});
