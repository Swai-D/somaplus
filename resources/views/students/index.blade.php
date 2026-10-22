@extends('layouts.app')

@section('title', __('menu.student_list'))

@section('page')
    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
        <div>
            <h3 class="page-title mb-1">{{ __('menu.student_list') }}</h3>
            <p class="text-muted mb-0">{{ __('students.list.intro') }}</p>
        </div>
        @can('create', App\Models\Student::class)
            <div class="mt-2 mt-md-0 d-flex gap-2">
                <button type="button" class="btn btn-outline-primary" data-bs-target="#student-modal" data-bs-toggle="modal">
                    <i class="ti ti-circle-plus me-1"></i>{{ __('students.create.title') }}
                </button>
                <a href="{{ route('students.import') }}" class="btn btn-outline-primary"><i class="ti ti-upload me-1"></i>{{ __('students.list.import') }}</a>
            </div>
        @endcan
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('students.search') }}" method="POST" class="d-flex gap-2 mb-1">
                @csrf
                <input type="search" name="term" value="{{ $term }}" class="form-control @error('term') is-invalid @enderror" placeholder="{{ __('students.list.search_placeholder') }}" aria-label="{{ __('students.list.search_placeholder') }}" minlength="2" maxlength="100" required autocomplete="off">
                <button type="submit" class="btn btn-primary"><i class="ti ti-search me-1"></i>{{ __('students.list.search') }}</button>
            </form>
            <div class="form-text mb-3">{{ __('students.list.search_hint') }}</div>
            @error('term')<div class="text-danger fs-13 mb-2">{{ $message }}</div>@enderror

            @if ($term === null)
                <form action="{{ route('students.index') }}" method="GET" class="d-flex flex-wrap gap-2">
                    <select name="status" class="form-select w-auto" aria-label="{{ __('students.fields.status') }}">
                        <option value="active" @selected(! $left)>{{ __('students.list.active') }}</option>
                        <option value="left" @selected($left)>{{ __('students.list.left') }}</option>
                    </select>
                    <select name="class" class="form-select w-auto" aria-label="{{ __('students.fields.class') }}">
                        <option value="">{{ __('students.list.all_classes') }}</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}" @selected($classId === $class->id)>{{ $class->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-outline-secondary">{{ __('students.list.filter') }}</button>
                </form>
            @endif
        </div>
    </div>

    @if ($year === null)
        <div class="alert alert-warning">{{ __('students.list.no_year') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            @if ($term !== null)
                <h5 class="mb-3">{{ __('students.list.results_for', ['term' => $term]) }}</h5>
            @endif
            @if ($students->isEmpty())
                <p class="text-muted mb-0">{{ $left ? __('students.list.empty_left') : __('students.list.empty') }}</p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('students.fields.admission_number') }}</th>
                                <th>{{ __('students.fields.name') }}</th>
                                <th>{{ __('students.fields.gender') }}</th>
                                <th>{{ __('students.fields.class') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($students as $student)
                                @php($placement = $placements[$student->id] ?? null)
                                <tr>
                                    <td>{{ $student->admission_number }}</td>
                                    <td class="fw-medium"><a href="{{ route('students.show', $student) }}">{{ $student->fullName() }}</a></td>
                                    <td>{{ __('students.fields.'.$student->gender) }}</td>
                                    <td>
                                        @if ($placement)
                                            {{ $placement->schoolClass->name }}@if ($placement->stream) – {{ $placement->stream->name }}@endif
                                        @elseif ($year)
                                            <span class="text-muted">{{ __('students.list.not_placed') }}</span>
                                        @endif
                                        @unless ($student->isActive()) <span class="badge bg-secondary ms-1">{{ __('students.list.left') }}</span> @endunless
                                    </td>
                                    <td class="text-end"><a href="{{ route('students.show', $student) }}" class="btn btn-sm btn-outline-secondary">{{ __('academics.open') }}</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($students instanceof Illuminate\Contracts\Pagination\Paginator)
                    <div class="mt-3">{{ $students->links() }}</div>
                @endif
            @endif
        </div>
    </div>

    @can('create', App\Models\Student::class)
        <x-form-modal id="student-modal" title="{{ __('students.create.title') }}" action="{{ route('students.store') }}" size="modal-lg">
            @include('students._fields', [
                'student' => null,
                'years' => $years,
                'classes' => $classes,
                'streams' => $streams,
                'currentYearId' => $currentYearId,
                'admissionAuto' => $admissionAuto,
                'admissionNext' => $admissionNext,
            ])
        </x-form-modal>
    @endcan
@endsection
