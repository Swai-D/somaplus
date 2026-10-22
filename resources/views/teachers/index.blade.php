@extends('layouts.app')

@section('title', __('menu.teacher_list'))

@section('page')
    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
        <div>
            <h3 class="page-title mb-1">{{ __('menu.teacher_list') }}</h3>
            <p class="text-muted mb-0">{{ __('teachers.list.intro') }}</p>
        </div>
        @can('create', App\Models\Teacher::class)
            <button type="button" class="btn btn-primary mt-2 mt-md-0" data-bs-target="#teacher-modal" data-bs-toggle="modal">
                <i class="ti ti-circle-plus me-1"></i>{{ __('teachers.list.add') }}
            </button>
        @endcan
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('teachers.search') }}" method="POST" class="d-flex gap-2 mb-1">
                @csrf
                <input type="search" name="term" value="{{ $term ?? '' }}" class="form-control @error('term') is-invalid @enderror" placeholder="{{ __('teachers.list.search_placeholder') }}" aria-label="{{ __('teachers.list.search_placeholder') }}" minlength="2" maxlength="100" required autocomplete="off">
                <button type="submit" class="btn btn-primary"><i class="ti ti-search me-1"></i>{{ __('teachers.list.search') }}</button>
            </form>
            <div class="form-text mb-3">{{ __('teachers.list.search_hint') }}</div>
            @error('term')<div class="text-danger fs-13 mb-2">{{ $message }}</div>@enderror

            @if (! isset($term))
                <form action="{{ route('teachers.index') }}" method="GET" class="d-flex flex-wrap gap-2">
                    <select name="status" class="form-select w-auto" aria-label="{{ __('teachers.list.status') }}">
                        <option value="" @selected(! $status)>{{ __('teachers.list.all') }}</option>
                        <option value="active" @selected($status === 'active')>{{ __('teachers.list.only_active') }}</option>
                        <option value="inactive" @selected($status === 'inactive')>{{ __('teachers.list.only_inactive') }}</option>
                    </select>
                    <button type="submit" class="btn btn-outline-secondary">{{ __('teachers.list.filter') }}</button>
                </form>
            @endif
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
                            <th>{{ __('teachers.list.name') }}</th>
                            <th>{{ __('teachers.list.staff_number') }}</th>
                            <th>{{ __('teachers.list.subjects') }}</th>
                            <th>{{ __('teachers.list.classes') }}</th>
                            <th>{{ __('teachers.list.phone') }}</th>
                            <th>{{ __('teachers.list.email') }}</th>
                            <th>{{ __('teachers.list.joined') }}</th>
                            <th>{{ __('teachers.list.status') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($teachers as $teacher)
                            <tr>
                                <td class="text-muted">{{ $loop->iteration }}</td>
                                <td class="fw-medium">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="avatar avatar-md bg-light text-dark fw-semibold">{{ mb_strtoupper(mb_substr($teacher->fullName(), 0, 1)) }}</span>
                                        <a href="{{ route('teachers.show', $teacher) }}">{{ $teacher->fullName() }}</a>
                                    </div>
                                </td>
                                <td>{{ $teacher->staff_number ?? '—' }}</td>
                                <td>
                                    @if ($teacher->subjects->isEmpty())
                                        <span class="text-muted">{{ __('teachers.list.no_subjects') }}</span>
                                    @else
                                        {{ $teacher->subjects->pluck('name')->join(', ') }}
                                    @endif
                                </td>
                                <td>
                                    @if ($teacher->classes->isEmpty())
                                        <span class="text-muted">{{ __('teachers.list.no_classes') }}</span>
                                    @else
                                        {{ $teacher->classes->pluck('name')->join(', ') }}
                                    @endif
                                </td>
                                <td>{{ $teacher->phone }}</td>
                                <td>{{ $teacher->user?->email }}</td>
                                <td class="text-muted">{{ $teacher->joined_on->toFormattedDateString() }}</td>
                                <td>
                                    <span class="badge {{ $teacher->isActive() ? 'bg-success' : 'bg-secondary' }}">
                                        <i class="ti ti-point-filled me-1"></i>{{ $teacher->isActive() ? __('teachers.list.active') : __('teachers.list.inactive') }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('teachers.show', $teacher) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('teachers.list.view') }}"><i class="ti ti-eye"></i></a>
                                        @can('update', $teacher)
                                            <a href="{{ route('teachers.edit', $teacher) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('teachers.list.edit') }}"><i class="ti ti-edit"></i></a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">{{ __('teachers.list.empty') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @can('create', App\Models\Teacher::class)
        <x-form-modal id="teacher-modal" title="{{ __('teachers.list.add') }}" action="{{ route('teachers.store') }}" size="modal-lg">
            @include('teachers._fields', ['teacher' => null, 'subjects' => $subjects, 'classes' => $classes])
        </x-form-modal>
    @endcan
@endsection
