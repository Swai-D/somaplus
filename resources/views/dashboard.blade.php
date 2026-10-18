@extends('layouts.app')

@section('title', __('menu.dashboard'))

@section('page')
    <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
        <div class="my-auto mb-2">
            <h3 class="page-title mb-1">{{ __('dashboard.welcome', ['name' => $currentUser->name]) }}</h3>
            <p class="text-muted mb-0">
                {{ $summary['scope'] === 'school' ? $summary['school'] : __('dashboard.platform') }}
            </p>
        </div>
    </div>

    <div class="row">
        @if ($summary['scope'] === 'platform')
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <p class="text-muted mb-1">{{ __('dashboard.schools') }}</p>
                        <h3 class="mb-0">{{ $summary['schools'] }}</h3>
                    </div>
                </div>
            </div>
        @else
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <p class="text-muted mb-1">{{ __('dashboard.plan') }}</p>
                        <h4 class="mb-0">{{ $summary['plan'] ?? __('dashboard.no_plan') }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <p class="text-muted mb-1">{{ __('dashboard.subscription') }}</p>
                        <h4 class="mb-0">
                            <span class="badge {{ ['active' => 'bg-success', 'grace' => 'bg-warning', 'read_only' => 'bg-danger'][$summary['status']] }}">{{ __('dashboard.status.'.$summary['status']) }}</span>
                        </h4>
                        @if ($summary['status'] === 'grace' && $summary['grace_ends_at'])
                            <p class="text-muted fs-13 mt-2 mb-0">{{ __('dashboard.grace_ends', ['date' => $summary['grace_ends_at']->toFormattedDateString()]) }}</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <p class="text-muted mb-1">{{ __('dashboard.your_roles') }}</p>
                        <h4 class="mb-0">{{ $summary['roles'] === [] ? __('dashboard.no_role') : implode(', ', $summary['roles']) }}</h4>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
