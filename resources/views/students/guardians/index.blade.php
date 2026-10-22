@extends('layouts.app')

@section('title', __('menu.guardian_list'))

@section('page')
    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
        <div>
            <h3 class="page-title mb-1">{{ __('menu.guardian_list') }}</h3>
            <p class="text-muted mb-0">{{ __('students.guardians.list.intro') }}</p>
        </div>
        @can('create', App\Models\Guardian::class)
            <a href="{{ route('guardians.create') }}" class="btn btn-primary mt-2 mt-md-0"><i class="ti ti-plus me-1"></i>{{ __('students.guardians.list.add') }}</a>
        @endcan
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('guardians.search') }}" method="POST" class="d-flex gap-2 mb-1">
                @csrf
                <input type="search" name="term" value="{{ $term }}" class="form-control @error('term') is-invalid @enderror" placeholder="{{ __('students.guardians.list.search_placeholder') }}" aria-label="{{ __('students.guardians.list.search_placeholder') }}" minlength="2" maxlength="100" required autocomplete="off">
                <button type="submit" class="btn btn-primary"><i class="ti ti-search me-1"></i>{{ __('students.list.search') }}</button>
            </form>
            <div class="form-text mb-3">{{ __('students.guardians.list.search_hint') }}</div>
            @error('term')<div class="text-danger fs-13 mb-2">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @if ($guardians->isEmpty())
                <p class="text-muted mb-0">{{ $term === null ? __('students.guardians.list.empty') : __('students.guardians.list.none_found') }}</p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('students.fields.name') }}</th>
                                <th>{{ __('students.guardians.list.children') }}</th>
                                <th>{{ __('students.fields.phone') }}</th>
                                <th>{{ __('students.fields.email') }}</th>
                                <th>{{ __('students.guardians.list.joined') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($guardians as $guardian)
                                <tr>
                                    <td class="text-muted">{{ $loop->iteration }}</td>
                                    <td class="fw-medium">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="avatar avatar-md bg-light text-dark fw-semibold">{{ mb_strtoupper(mb_substr($guardian->name, 0, 1)) }}</span>
                                            <a href="{{ route('guardians.show', $guardian) }}">{{ $guardian->name }}</a>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($guardian->students->isEmpty())
                                            <span class="text-muted">{{ __('students.guardians.list.no_children') }}</span>
                                        @else
                                            @foreach ($guardian->students as $child)
                                                @php($placement = $placements[$child->id] ?? null)
                                                <div>
                                                    <a href="{{ route('students.show', $child) }}">{{ $child->fullName() }}</a>
                                                    @if ($placement)
                                                        <span class="text-muted fs-12">{{ $placement->schoolClass->name }}@if ($placement->stream) – {{ $placement->stream->name }}@endif</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>{{ $guardian->phone }}</td>
                                    <td>{{ $guardian->email }}</td>
                                    <td class="text-muted">{{ $guardian->created_at->toFormattedDateString() }}</td>
                                    <td class="text-end">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <a href="{{ route('guardians.show', $guardian) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('academics.open') }}"><i class="ti ti-eye"></i></a>
                                            @can('update', $guardian)
                                                <a href="{{ route('guardians.edit', $guardian) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('students.guardians.list.edit') }}"><i class="ti ti-edit"></i></a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
