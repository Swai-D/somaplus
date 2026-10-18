@extends('layouts.app')

@section('title', $year->name)

@section('page')
    <div class="mb-3">
        <a href="{{ route('academics.years.index') }}" class="text-muted"><i class="ti ti-arrow-left me-1"></i>{{ __('menu.academic_years') }}</a>
        <h3 class="page-title mt-2 mb-1">
            {{ $year->name }}
            @if ($year->is_current)<span class="badge bg-success ms-2">{{ __('academics.years.current') }}</span>@endif
        </h3>
        <p class="text-muted mb-0">{{ $year->starts_on->toFormattedDateString() }} – {{ $year->ends_on->toFormattedDateString() }}</p>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="mb-3">{{ __('academics.years.terms') }}</h5>
            @if ($terms->isEmpty())
                <p class="text-muted mb-0">{{ __('academics.terms.empty') }}</p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('academics.terms.name') }}</th>
                                <th>{{ __('academics.starts_on') }}</th>
                                <th>{{ __('academics.ends_on') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($terms as $term)
                                <tr>
                                    <td class="fw-medium">{{ $term->name }}</td>
                                    <td>{{ $term->starts_on->toFormattedDateString() }}</td>
                                    <td>{{ $term->ends_on->toFormattedDateString() }}</td>
                                    <td class="text-end">
                                        @can('update', $term)
                                            <a href="{{ route('academics.terms.edit', $term) }}" class="btn btn-sm btn-outline-secondary">{{ __('academics.edit') }}</a>
                                        @endcan
                                        @can('delete', $term)
                                            @include('academics._delete', ['action' => route('academics.terms.destroy', $term)])
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

    @can('create', App\Models\Term::class)
        <div class="card">
            <div class="card-body">
                <h5 class="mb-3">{{ __('academics.terms.add') }}</h5>
                <form action="{{ route('academics.terms.store', $year) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">@include('academics._field', ['name' => 'name', 'label' => __('academics.terms.name'), 'hint' => __('academics.terms.name_hint')])</div>
                        <div class="col-md-4">@include('academics._field', ['name' => 'starts_on', 'label' => __('academics.starts_on'), 'type' => 'date'])</div>
                        <div class="col-md-4">@include('academics._field', ['name' => 'ends_on', 'label' => __('academics.ends_on'), 'type' => 'date'])</div>
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('academics.save') }}</button>
                </form>
            </div>
        </div>
    @endcan

    @can('update', $year)
        <a href="{{ route('academics.years.edit', $year) }}" class="btn btn-outline-secondary">{{ __('academics.years.edit') }}</a>
    @endcan
@endsection
