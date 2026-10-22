@extends('layouts.app')

@section('title', __('menu.book_list'))

@section('page')
    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
        <div>
            <h3 class="page-title mb-1">{{ __('menu.book_list') }}</h3>
            <p class="text-muted mb-0">{{ __('library.books.list.intro') }}</p>
        </div>
        @can('create', App\Models\Book::class)
            <button type="button" class="btn btn-primary mt-2 mt-md-0" data-bs-target="#book-modal" data-bs-toggle="modal">
                <i class="ti ti-circle-plus me-1"></i>{{ __('library.books.list.add') }}
            </button>
        @endcan
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('library.books.search') }}" method="POST" class="d-flex gap-2 mb-1">
                @csrf
                <input type="search" name="term" value="{{ $term ?? '' }}" class="form-control @error('term') is-invalid @enderror" placeholder="{{ __('library.books.list.search_placeholder') }}" aria-label="{{ __('library.books.list.search_placeholder') }}" minlength="2" maxlength="100" required autocomplete="off">
                <button type="submit" class="btn btn-primary"><i class="ti ti-search me-1"></i>{{ __('library.books.list.search') }}</button>
            </form>
            <div class="form-text">{{ __('library.books.list.search_hint') }}</div>
            @error('term')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
            <div class="search-set">
                <div class="search-input">
                    <span class="btn-searchset"><i class="ti ti-search fs-14 feather-search"></i></span>
                </div>
            </div>
            <div class="d-flex align-items-center flex-wrap gap-2"></div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="datatable table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('library.books.list.subject') }}</th>
                            <th>{{ __('library.books.list.name') }}</th>
                            <th>{{ __('library.books.list.publisher') }}</th>
                            <th>{{ __('library.books.list.author') }}</th>
                            <th>{{ __('library.books.list.number') }}</th>
                            <th>{{ __('library.books.list.rack') }}</th>
                            <th>{{ __('library.books.list.qty') }}</th>
                            <th>{{ __('library.books.list.available') }}</th>
                            <th>{{ __('library.books.list.price') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($books as $book)
                            <tr>
                                <td class="text-muted">{{ $loop->iteration }}</td>
                                <td>{{ $book->subject?->name ?? __('library.books.list.no_subject') }}</td>
                                <td class="fw-medium">{{ $book->name }}</td>
                                <td>{{ $book->publisher ?? '—' }}</td>
                                <td>{{ $book->author ?? '—' }}</td>
                                <td class="text-muted">{{ $book->book_number ?? '—' }}</td>
                                <td class="text-muted">{{ $book->rack_number ?? '—' }}</td>
                                <td>{{ number_format($book->quantity) }}</td>
                                <td>
                                    <span class="badge {{ $book->availableCount() > 0 ? 'bg-success' : 'bg-secondary' }}">
                                        {{ number_format($book->availableCount()) }}
                                    </span>
                                    @if ($book->outCount() > 0)
                                        <span class="text-muted fs-13 ms-1">({{ __('library.books.list.out') }}: {{ number_format($book->outCount()) }})</span>
                                    @endif
                                </td>
                                <td>{{ $book->priceMoney()?->format() ?? '—' }}</td>
                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        @can('update', $book)
                                            <a href="{{ route('library.books.edit', $book) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('library.books.list.view') }}"><i class="ti ti-edit"></i></a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">{{ __('library.books.list.empty') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @can('create', App\Models\Book::class)
        <x-form-modal id="book-modal" title="{{ __('library.books.form.new') }}" action="{{ route('library.books.store') }}">
            @include('library.books._fields', ['book' => null, 'subjects' => $subjects])
        </x-form-modal>
    @endcan
@endsection
