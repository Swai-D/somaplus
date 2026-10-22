<?php

namespace App\Http\Controllers\Library;

use App\Domain\Library\IssueBook;
use App\Domain\Library\ReturnBook;
use App\Domain\Settings\RecordAudit;
use App\Domain\Students\CurrentPlacements;
use App\Http\Controllers\Controller;
use App\Http\Requests\Library\IssueRequest;
use App\Models\AcademicYear;
use App\Models\Book;
use App\Models\BookIssue;
use App\Models\Enrollment;
use App\Models\LibraryMember;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * The books that went out and came back: the list, putting one copy out and
 * taking it back. The list shows the copies out first, as in the template.
 */
class BookIssueController extends Controller
{
    public function index(Request $request, RecordAudit $audit, CurrentPlacements $placements): View
    {
        Gate::authorize('viewAny', BookIssue::class);

        $filter = $request->query('filter');

        $query = BookIssue::query()->with(['book.subject', 'member.student'])
            ->orderByDesc('issued_on')->orderByDesc('id');

        if ($filter === 'open') {
            $query->whereNull('returned_on');
        } elseif ($filter === 'returned') {
            $query->whereNotNull('returned_on');
        }

        $issues = $query->limit(200)->get();

        return view('library.issues.index', [
            'issues' => $issues,
            'filter' => $filter,
            'members' => LibraryMember::query()
                ->with('student')
                ->join('students', 'students.id', '=', 'library_members.student_id')
                ->select('library_members.*')
                ->orderBy('students.first_name')
                ->orderBy('students.last_name')
                ->get(),
            'books' => Book::query()
                ->withCount(['issues as open_issues_count' => fn ($query) => $query->whereNull('returned_on')])
                ->orderBy('name')
                ->get(),
            'placements' => $this->placements($issues, $placements),
        ]);
    }

    /**
     * Searching is a POST so the name or the card number typed never ends up in a URL, a log or the browser history.
     */
    public function search(Request $request, RecordAudit $audit, CurrentPlacements $placements): View
    {
        Gate::authorize('viewAny', BookIssue::class);

        $term = trim((string) $request->validate(['term' => ['required', 'string', 'min:2', 'max:100']])['term']);
        $like = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], mb_strtolower($term)).'%';

        $issues = BookIssue::query()->with(['book.subject', 'member.student'])
            ->where(function ($query) use ($like): void {
                $query->whereRaw('lower(coalesce(books.name, \'\')) like ?', [$like])
                    ->orWhereRaw('lower(library_members.card_number) like ?', [$like])
                    ->orWhereRaw("lower(students.first_name || ' ' || students.last_name) like ?", [$like]);
            })
            ->join('books', 'books.id', '=', 'book_issues.book_id')
            ->join('library_members', 'library_members.id', '=', 'book_issues.member_id')
            ->join('students', 'students.id', '=', 'library_members.student_id')
            ->select('book_issues.*')
            ->orderByDesc('book_issues.issued_on')
            ->limit(100)
            ->get();

        $audit->handle('issue.searched', new BookIssue);

        return view('library.issues.index', [
            'issues' => $issues,
            'term' => $term,
            'filter' => null,
            'members' => LibraryMember::query()
                ->with('student')
                ->join('students', 'students.id', '=', 'library_members.student_id')
                ->select('library_members.*')
                ->orderBy('students.first_name')
                ->orderBy('students.last_name')
                ->get(),
            'books' => Book::query()
                ->withCount(['issues as open_issues_count' => fn ($query) => $query->whereNull('returned_on')])
                ->orderBy('name')
                ->get(),
            'placements' => $this->placements($issues, $placements),
        ]);
    }

    /**
     * The form of an issue is a pop-up of the list, as in the template, so this address
     * only opens the list with the pop-up ready.
     */
    public function create(): RedirectResponse
    {
        Gate::authorize('create', BookIssue::class);

        return redirect()->route('library.issues.index', ['add' => 'issue-modal']);
    }

    public function store(IssueRequest $request, IssueBook $issue): RedirectResponse
    {
        /** @var array{member_id: int, book_id: int, issued_on: string, due_on: string} $data */
        $data = $request->validated();

        $member = LibraryMember::query()->findOrFail($data['member_id']);
        $book = Book::query()->findOrFail($data['book_id']);

        $issue->handle($book, $member, $data['issued_on'], $data['due_on']);

        return redirect()->route('library.issues.index')->with('status', __('library.issues.issued'));
    }

    /**
     * Takes a copy back into the library, on the day the school says it came.
     */
    public function takeBack(BookIssue $bookIssue, ReturnBook $return): RedirectResponse
    {
        Gate::authorize('update', $bookIssue);

        $return->handle($bookIssue, now()->toDateString());

        return redirect()->route('library.issues.index')->with('status', __('library.issues.returned'));
    }

    /**
     * The class of each borrowed copy's member, from the pupil's placement of this year.
     *
     * @param  Collection<int, BookIssue>  $issues
     * @return array<int, Enrollment> keyed by pupil id
     */
    private function placements($issues, CurrentPlacements $current): array
    {
        $year = AcademicYear::query()->where('is_current', true)->first();
        $students = $issues->map(fn (BookIssue $issue) => $issue->member->student);

        return $current->for($students, $year);
    }
}
