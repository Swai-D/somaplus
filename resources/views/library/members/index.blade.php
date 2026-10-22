@extends('layouts.app')

@section('title', __('menu.library_members'))

@section('page')
    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
        <div>
            <h3 class="page-title mb-1">{{ __('menu.library_members') }}</h3>
            <p class="text-muted mb-0">{{ __('library.members.list.intro') }}</p>
        </div>
        @can('create', App\Models\LibraryMember::class)
            <button type="button" class="btn btn-primary mt-2 mt-md-0" data-bs-target="#member-modal" data-bs-toggle="modal">
                <i class="ti ti-circle-plus me-1"></i>{{ __('library.members.list.add') }}
            </button>
        @endcan
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('library.members.search') }}" method="POST" class="d-flex gap-2 mb-1">
                @csrf
                <input type="search" name="term" value="{{ $term ?? '' }}" class="form-control @error('term') is-invalid @enderror" placeholder="{{ __('library.members.list.search_placeholder') }}" aria-label="{{ __('library.members.list.search_placeholder') }}" minlength="2" maxlength="100" required autocomplete="off">
                <button type="submit" class="btn btn-primary"><i class="ti ti-search me-1"></i>{{ __('library.members.list.search') }}</button>
            </form>
            <div class="form-text">{{ __('library.members.list.search_hint') }}</div>
            @error('term')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="datatable table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('library.members.list.card') }}</th>
                            <th>{{ __('library.members.list.student') }}</th>
                            <th>{{ __('library.members.list.class') }}</th>
                            <th>{{ __('library.members.list.phone') }}</th>
                            <th>{{ __('library.members.list.books_out') }}</th>
                            <th>{{ __('library.members.list.joined') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($members as $member)
                            @php($placement = $placements[$member->student_id] ?? null)
                            @php($guardian = $member->student->guardians->first() ?? null)
                            <tr>
                                <td class="text-muted">{{ $loop->iteration }}</td>
                                <td class="fw-medium">{{ $member->card_number }}</td>
                                <td><a href="{{ route('library.members.show', $member) }}">{{ $member->fullName() }}</a></td>
                                <td>{{ $placement?->schoolClass?->name ?? __('library.members.list.no_class') }}</td>
                                <td title="{{ __('library.members.list.phone_hint') }}">{{ $guardian?->phone ?? '—' }}</td>
                                <td>
                                    <span class="badge {{ $member->outCount() > 0 ? 'bg-warning text-dark' : 'bg-secondary' }}">
                                        {{ number_format($member->outCount()) }}
                                    </span>
                                </td>
                                <td class="text-muted">{{ $member->joined_on->isoFormat('D MMM YYYY') }}</td>
                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        @can('update', $member)
                                            <a href="{{ route('library.members.edit', $member) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('library.members.list.edit') }}"><i class="ti ti-edit"></i></a>
                                        @endcan
                                        @can('delete', $member)
                                            <form action="{{ route('library.members.destroy', $member) }}" method="POST" onsubmit="return confirm('{{ __('library.members.show.delete_confirm') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('library.members.list.delete') }}"><i class="ti ti-trash"></i></button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">{{ __('library.members.list.empty') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @can('create', App\Models\LibraryMember::class)
        <x-form-modal id="member-modal" title="{{ __('library.members.form.new') }}" action="{{ route('library.members.store') }}">
            @include('library.members._fields', ['member' => null, 'candidates' => $candidates])
        </x-form-modal>
    @endcan
@endsection
