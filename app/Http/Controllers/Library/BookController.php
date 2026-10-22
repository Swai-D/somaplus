<?php

namespace App\Http\Controllers\Library;

use App\Domain\Library\RemoveBook;
use App\Domain\Library\SaveBook;
use App\Domain\Settings\RecordAudit;
use App\Http\Controllers\Controller;
use App\Http\Requests\Library\BookRequest;
use App\Models\Book;
use App\Models\Subject;
use App\Tenancy\TenantContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * The books of the library: their list, their page, adding one, changing one and
 * taking one out. What is available of a book is counted, never typed.
 */
class BookController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Book::class);

        $books = Book::query()
            ->with('subject')
            ->withCount(['issues as open_issues_count' => fn ($query) => $query->whereNull('returned_on')])
            ->orderBy('name')
            ->limit(200)
            ->get();

        return view('library.books.index', [
            'books' => $books,
            'subjects' => Subject::query()->orderBy('name')->get(),
        ]);
    }

    /**
     * Searching is a POST so the title or the author typed never ends up in a URL, a log or the browser history.
     */
    public function search(Request $request, RecordAudit $audit): View
    {
        Gate::authorize('viewAny', Book::class);

        $term = trim((string) $request->validate(['term' => ['required', 'string', 'min:2', 'max:100']])['term']);
        $like = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], mb_strtolower($term)).'%';

        $books = Book::query()
            ->with('subject')
            ->withCount(['issues as open_issues_count' => fn ($query) => $query->whereNull('returned_on')])
            ->where(function ($query) use ($like): void {
                $query->whereRaw('lower(name) like ?', [$like])
                    ->orWhereRaw('lower(coalesce(publisher, \'\')) like ?', [$like])
                    ->orWhereRaw('lower(coalesce(author, \'\')) like ?', [$like])
                    ->orWhereRaw('lower(coalesce(book_number, \'\')) like ?', [$like]);
            })
            ->orderBy('name')
            ->limit(50)
            ->get();

        $audit->handle('book.searched', new Book);

        return view('library.books.index', [
            'books' => $books,
            'term' => $term,
            'subjects' => Subject::query()->orderBy('name')->get(),
        ]);
    }

    /**
     * The form of a new book is a pop-up of the list, as in the template, so this address
     * only opens the list with the pop-up ready.
     */
    public function create(): RedirectResponse
    {
        Gate::authorize('create', Book::class);

        return redirect()->route('library.books.index', ['add' => 'book-modal']);
    }

    public function store(BookRequest $request, SaveBook $save): RedirectResponse
    {
        /** @var array{subject_id?: int|null, name: string, publisher?: string|null, author?: string|null, book_number?: string|null, rack_number?: string|null, quantity: int} $data */
        $data = $request->validated();
        if ($request->filled('price')) {
            $data['price'] = $request->minorPrice(TenantContext::instance()->current()?->currency);
        }

        $book = $save->handle(null, $data);

        return redirect()->route('library.books.index')->with('status', __('library.books.saved'));
    }

    public function edit(Book $book): View
    {
        Gate::authorize('update', $book);

        return view('library.books.edit', [
            'book' => $book,
            'subjects' => Subject::query()->orderBy('name')->get(),
        ]);
    }

    public function update(BookRequest $request, Book $book, SaveBook $save): RedirectResponse
    {
        Gate::authorize('update', $book);

        /** @var array{subject_id?: int|null, name: string, publisher?: string|null, author?: string|null, book_number?: string|null, rack_number?: string|null, quantity: int} $data */
        $data = $request->validated();
        if ($request->filled('price')) {
            $data['price'] = $request->minorPrice(TenantContext::instance()->current()?->currency);
        }

        $save->handle($book, $data);

        return redirect()->route('library.books.index')->with('status', __('library.books.saved'));
    }

    /**
     * Takes a book out of the library. A book the school ever lent stays, because the
     * record of the issue says it is this book.
     */
    public function destroy(Book $book, RemoveBook $remove): RedirectResponse
    {
        Gate::authorize('delete', $book);

        $remove->handle($book);

        return redirect()->route('library.books.index')->with('status', __('library.books.deleted'));
    }
}
