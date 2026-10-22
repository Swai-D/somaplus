<?php

namespace App\Http\Controllers\Library;

use App\Domain\Library\BuildLibraryCards;
use App\Domain\Library\RemoveLibraryMember;
use App\Domain\Library\SaveLibraryMember;
use App\Domain\Settings\RecordAudit;
use App\Domain\Students\CurrentPlacements;
use App\Http\Controllers\Controller;
use App\Http\Requests\Library\MemberRequest;
use App\Models\AcademicYear;
use App\Models\LibraryMember;
use App\Models\Student;
use App\Tenancy\TenantContext;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * The members of the library: their list, their page, one more member, changing
 * whose card it is and taking a member out. The class of a member comes from the
 * pupil's current placement, and the phone from the pupil's guardian, never from here.
 */
class MemberController extends Controller
{
    public function index(Request $request, CurrentPlacements $placements): View
    {
        Gate::authorize('viewAny', LibraryMember::class);

        $members = LibraryMember::query()
            ->with('student')
            ->withCount(['issues as open_issues_count' => fn ($query) => $query->whereNull('returned_on')])
            ->join('students', 'students.id', '=', 'library_members.student_id')
            ->select('library_members.*')
            ->orderBy('students.first_name')
            ->orderBy('students.last_name')
            ->limit(500)
            ->get();

        $year = AcademicYear::query()->where('is_current', true)->first();

        return view('library.members.index', [
            'members' => $members,
            'year' => $year,
            'placements' => $placements->for($members->map(fn (LibraryMember $member) => $member->student), $year),
            'candidates' => $this->candidates(),
        ]);
    }

    /**
     * Searching is a POST so the name or the card number typed never ends up in a URL, a log or the browser history.
     */
    public function search(Request $request, CurrentPlacements $placements, RecordAudit $audit): View
    {
        Gate::authorize('viewAny', LibraryMember::class);

        $term = trim((string) $request->validate(['term' => ['required', 'string', 'min:2', 'max:100']])['term']);
        $like = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], mb_strtolower($term)).'%';

        $members = LibraryMember::query()
            ->with('student')
            ->withCount(['issues as open_issues_count' => fn ($query) => $query->whereNull('returned_on')])
            ->join('students', 'students.id', '=', 'library_members.student_id')
            ->select('library_members.*')
            ->where(function ($query) use ($like): void {
                $query->whereRaw("lower(students.first_name || ' ' || students.last_name) like ?", [$like])
                    ->orWhereRaw('lower(library_members.card_number) like ?', [$like]);
            })
            ->orderBy('students.first_name')
            ->limit(100)
            ->get();

        $audit->handle('member.searched', new LibraryMember);

        $year = AcademicYear::query()->where('is_current', true)->first();

        return view('library.members.index', [
            'members' => $members,
            'term' => $term,
            'year' => $year,
            'placements' => $placements->for($members->map(fn (LibraryMember $member) => $member->student), $year),
            'candidates' => $this->candidates(),
        ]);
    }

    /**
     * The form of a new member is a pop-up of the list, as in the template, so this address
     * only opens the list with the pop-up ready.
     */
    public function create(): RedirectResponse
    {
        Gate::authorize('create', LibraryMember::class);

        return redirect()->route('library.members.index', ['add' => 'member-modal']);
    }

    public function store(MemberRequest $request, SaveLibraryMember $save): RedirectResponse
    {
        /** @var array{student_id: int, card_number: string, joined_on: string} $data */
        $data = $request->validated();

        $save->handle(null, $data);

        return redirect()->route('library.members.index')->with('status', __('library.members.saved'));
    }

    public function show(LibraryMember $member, CurrentPlacements $placements, RecordAudit $audit): View
    {
        Gate::authorize('view', $member);

        $member->load(['student.guardians', 'issues.book']);

        $audit->handle('member.viewed', $member);

        $year = AcademicYear::query()->where('is_current', true)->first();

        return view('library.members.show', [
            'member' => $member,
            'placements' => $placements->for([$member->student], $year),
        ]);
    }

    public function edit(LibraryMember $member): View
    {
        Gate::authorize('update', $member);

        return view('library.members.edit', [
            'member' => $member->load('student'),
            'candidates' => $this->candidates($member->student_id),
        ]);
    }

    /** The printable card of one member, opened in a new tab like the ID card of a pupil. */
    public function card(LibraryMember $member, BuildLibraryCards $cards): View
    {
        Gate::authorize('view', $member);

        $member->load('student');

        return view('library.members.card', [
            'cards' => collect([$cards->handle(collect([$member]))[0]]),
            'school' => app(TenantContext::class)->current()?->name,
        ]);
    }

    public function update(MemberRequest $request, LibraryMember $member, SaveLibraryMember $save): RedirectResponse
    {
        Gate::authorize('update', $member);

        /** @var array{student_id: int, card_number: string, joined_on: string} $data */
        $data = $request->validated();

        $save->handle($member, $data);

        return redirect()->route('library.members.show', $member)->with('status', __('library.members.saved'));
    }

    /**
     * Takes a member out of the library. A member the library ever lent a book to stays,
     * because every issue is a record about the member and the history is never removed.
     */
    public function destroy(LibraryMember $member, RemoveLibraryMember $remove): RedirectResponse
    {
        Gate::authorize('delete', $member);

        $remove->handle($member);

        return redirect()->route('library.members.index')->with('status', __('library.members.deleted'));
    }

    /**
     * The pupils the school may still make members: the ones without a card. The list is
     * ordered the way the guardian form does it, so a name is found in the same place.
     *
     * @return Collection<int, Student>
     */
    private function candidates(?int $exceptStudent = null): Collection
    {
        return Student::query()
            ->where('status', Student::ACTIVE)
            ->when($exceptStudent !== null, fn ($query) => $query->whereKeyNot($exceptStudent))
            ->whereDoesntHave('libraryMember')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
    }
}
