@extends('layouts.app')

@section('title', __('menu.academic_years'))

@section('page')
    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
        <div>
            <h3 class="page-title mb-1">{{ __('menu.academic_years') }}</h3>
            <p class="text-muted mb-0">{{ __('academics.years.intro') }}</p>
        </div>
        @can('create', App\Models\AcademicYear::class)
            <button type="button" class="btn btn-primary mt-2 mt-md-0" data-bs-target="#year-modal" data-bs-toggle="modal">
                <i class="ti ti-circle-plus me-1"></i>{{ __('academics.years.add') }}
            </button>
        @endcan
    </div>

    <div class="card">
        <div class="card-body">
            @if ($years->isEmpty())
                <p class="text-muted mb-0">{{ __('academics.years.empty') }}</p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('academics.years.name') }}</th>
                                <th>{{ __('academics.starts_on') }}</th>
                                <th>{{ __('academics.ends_on') }}</th>
                                <th>{{ __('academics.years.terms') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($years as $year)
                                <tr>
                                    <td>
                                        <a href="{{ route('academics.years.show', $year) }}" class="fw-medium">{{ $year->name }}</a>
                                        @if ($year->is_current)<span class="badge bg-success ms-2">{{ __('academics.years.current') }}</span>@endif
                                    </td>
                                    <td>{{ $year->starts_on->toFormattedDateString() }}</td>
                                    <td>{{ $year->ends_on->toFormattedDateString() }}</td>
                                    <td>{{ $year->terms_count }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('academics.years.show', $year) }}" class="btn btn-sm btn-outline-primary">{{ __('academics.open') }}</a>
                                        @can('update', $year)
                                            <a href="{{ route('academics.years.edit', $year) }}" class="btn btn-sm btn-outline-secondary">{{ __('academics.edit') }}</a>
                                        @endcan
                                        @can('delete', $year)
                                            @include('academics._delete', ['action' => route('academics.years.destroy', $year)])
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    @can('create', App\Models\AcademicYear::class)
        <x-form-modal id="year-modal" title="{{ __('academics.years.add') }}" action="{{ route('academics.years.store') }}" size="modal-lg">
            @include('academics.years._fields', ['year' => null])
        </x-form-modal>
    @endcan
@endsection
