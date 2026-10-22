@extends('layouts.app')

@section('title', __('menu.issue_return'))

@section('page')
    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
        <div>
            <h3 class="page-title mb-1">{{ __('menu.issue_return') }}</h3>
            <p class="text-muted mb-0">{{ __('library.issues.list.intro') }}</p>
        </div>
        @can('create', App\Models\BookIssue::class)
            <button type="button" class="btn btn-primary mt-2 mt-md-0" data-bs-target="#issue-modal" data-bs-toggle="modal">
                <i class="ti ti-circle-plus me-1"></i>{{ __('library.issues.list.add') }}
            </button>
        @endcan
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('library.issues.search') }}" method="POST" class="d-flex gap-2 mb-1">
                @csrf
                <input type="search" name="term" value="{{ $term ?? '' }}" class="form-control @error('term') is-invalid @enderror" placeholder="{{ __('library.issues.list.search_placeholder') }}" aria-label="{{ __('library.issues.list.search_placeholder') }}" minlength="2" maxlength="100" required autocomplete="off">
                <button type="submit" class="btn btn-primary"><i class="ti ti-search me-1"></i>{{ __('library.issues.list.search') }}</button>
            </form>
            <div class="form-text">{{ __('library.issues.list.search_hint') }}</div>
            @error('term')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
            <ul class="nav nav-pills">
                <li class="nav-item"><a href="{{ route('library.issues.index') }}" class="nav-link {{ $filter === null ? 'active' : '' }}">{{ __('library.issues.list.all') }}</a></li>
                <li class="nav-item"><a href="{{ route('library.issues.index', ['filter' => 'open']) }}" class="nav-link {{ $filter === 'open' ? 'active' : '' }}">{{ __('library.issues.list.only_open') }}</a></li>
                <li class="nav-item"><a href="{{ route('library.issues.index', ['filter' => 'returned']) }}" class="nav-link {{ $filter === 'returned' ? 'active' : '' }}">{{ __('library.issues.list.only_returned') }}</a></li>
            </ul>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="datatable table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('library.issues.list.member') }}</th>
                            <th>{{ __('library.issues.list.class') }}</th>
                            <th>{{ __('library.issues.list.book') }}</th>
                            <th>{{ __('library.issues.list.number') }}</th>
                            <th>{{ __('library.issues.list.issued_on') }}</th>
                            <th>{{ __('library.issues.list.due_on') }}</th>
                            <th>{{ __('library.issues.list.returned_on') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($issues as $issue)
                            <tr>
                                <td class="text-muted">{{ $loop->iteration }}</td>
                                <td class="fw-medium">
                                    <a href="{{ route('library.members.show', $issue->member) }}">{{ $issue->member->fullName() }}</a>
                                    <span class="text-muted fs-13 ms-1">{{ $issue->member->card_number }}</span>
                                </td>
                                <td>{{ ($placements[$issue->member->student_id] ?? null)?->schoolClass?->name ?? __('library.issues.list.no_class') }}</td>
                                <td>{{ $issue->book->name }}</td>
                                <td class="text-muted">{{ $issue->book->book_number ?? '—' }}</td>
                                <td>{{ $issue->issued_on->isoFormat('D MMM YYYY') }}</td>
                                <td>
                                    {{ $issue->due_on?->isoFormat('D MMM YYYY') ?? '—' }}
                                    @if ($issue->isOverdue())
                                        <span class="badge bg-danger ms-1">{{ __('library.issues.list.overdue') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($issue->isReturned())
                                        <span class="text-muted">{{ $issue->returned_on->isoFormat('D MMM YYYY') }}</span>
                                    @else
                                        <span class="badge bg-warning text-dark">{{ __('library.issues.list.out') }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if (! $issue->isReturned())
                                        @can('update', $issue)
                                            <form action="{{ route('library.issues.return', $issue) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-primary">{{ __('library.issues.list.issue') }}</button>
                                            </form>
                                        @endcan
                                    @else
                                        <span class="badge bg-success">{{ __('library.issues.list.came_back') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">{{ __('library.issues.list.empty') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @can('create', App\Models\BookIssue::class)
        <x-form-modal id="issue-modal" title="{{ __('library.issues.form.new') }}" action="{{ route('library.issues.store') }}">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="member_id">{{ __('library.issues.form.member') }}<span class="text-danger"> *</span></label>
                    <select class="form-select @error('member_id') is-invalid @enderror" id="member_id" name="member_id" required>
                        <option value="">{{ __('library.issues.form.choose_member') }}</option>
                        @foreach ($members as $member)
                            <option value="{{ $member->id }}" @selected(old('member_id') == $member->id)>{{ $member->card_number }} — {{ $member->fullName() }}</option>
                        @endforeach
                    </select>
                    <div class="form-text">{{ __('library.issues.form.member_hint') }}</div>
                    @error('member_id')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="book_id">{{ __('library.issues.form.book') }}<span class="text-danger"> *</span></label>
                    <select class="form-select @error('book_id') is-invalid @enderror" id="book_id" name="book_id" required>
                        <option value="">{{ __('library.issues.form.choose_book') }}</option>
                        @foreach ($books as $book)
                            <option value="{{ $book->id }}" @selected(old('book_id') == $book->id) @disabled($book->availableCount() < 1)>
                                {{ $book->name }} ({{ __('library.books.list.available') }}: {{ number_format($book->availableCount()) }})
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">{{ __('library.issues.form.book_hint') }}</div>
                    @error('book_id')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    @include('academics._field', ['name' => 'issued_on', 'label' => __('library.issues.form.issued_on'), 'type' => 'date', 'value' => old('issued_on', now()->toDateString()), 'hint' => __('library.issues.form.day_hint')])
                </div>
                <div class="col-md-6">
                    @include('academics._field', ['name' => 'due_on', 'label' => __('library.issues.form.due_on'), 'type' => 'date', 'value' => old('due_on', now()->addDays(14)->toDateString()), 'hint' => __('library.issues.form.due_hint')])
                </div>
            </div>
        </x-form-modal>
    @endcan
@endsection
