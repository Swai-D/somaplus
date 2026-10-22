@extends('layouts.app')

@section('title', __('platform.title'))

@section('page')
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h3 class="page-title mb-1">{{ __('platform.title') }}</h3>
            <p class="text-muted mb-0">{{ __('platform.intro') }}</p>
        </div>
        <a href="{{ route('platform.schools.create') }}" class="btn btn-primary">{{ __('platform.add') }}</a>
    </div>

    <div class="card">
        <div class="card-body">
            @if ($schools->isEmpty())
                <p class="text-muted mb-0">{{ __('platform.none') }}</p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>{{ __('platform.name') }}</th><th>{{ __('platform.address') }}</th><th>{{ __('platform.plan') }}</th><th>{{ __('platform.status') }}</th><th>{{ __('platform.currency') }}</th></tr></thead>
                        <tbody>
                            @foreach ($schools as $school)
                                <tr>
                                    <td><a href="{{ route('platform.schools.edit', $school) }}">{{ $school->name }}</a></td>
                                    <td>{{ $school->domain ?? $school->slug.'.'.config('tenancy.base_domain') }}</td>
                                    <td>{{ $school->plan->name }}</td>
                                    <td>{{ __('platform.statuses.'.$school->subscription_status->value) }}</td>
                                    <td>{{ $school->currency }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
