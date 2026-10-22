@extends('layouts.app')

@section('title', $school->name)

@section('page')
    <div class="mb-3">
        <h3 class="page-title mb-1">{{ $school->name }}</h3>
        <a href="{{ route('platform.schools.index') }}">{{ __('platform.back') }}</a>
    </div>

    <form method="POST" action="{{ route('platform.schools.update', $school) }}">
        @csrf
        @method('PUT')
        <div class="card"><div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="name">{{ __('platform.name') }}</label>
                    <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $school->name) }}" maxlength="150" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('platform.slug') }}</label>
                    <input class="form-control" value="{{ $school->slug }}.{{ $baseDomain }}" disabled>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="domain">{{ __('platform.own_domain') }}</label>
                    <input class="form-control @error('domain') is-invalid @enderror" id="domain" name="domain" value="{{ old('domain', $school->domain) }}" maxlength="190">
                    @error('domain')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">{{ __('platform.own_domain_hint') }}</div>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label" for="subscription_plan_id">{{ __('platform.plan') }}</label>
                    <select class="form-select @error('subscription_plan_id') is-invalid @enderror" id="subscription_plan_id" name="subscription_plan_id" required>
                        @foreach ($plans as $plan)
                            <option value="{{ $plan->id }}" @selected((int) old('subscription_plan_id', $school->subscription_plan_id) === $plan->id)>{{ $plan->name }}</option>
                        @endforeach
                    </select>
                    @error('subscription_plan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">{{ __('platform.currency') }}</label>
                    <input class="form-control" value="{{ $school->currency }}" disabled>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label" for="subscription_status">{{ __('platform.status') }}</label>
                    <select class="form-select @error('subscription_status') is-invalid @enderror" id="subscription_status" name="subscription_status" required>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected(old('subscription_status', $school->subscription_status->value) === $status->value)>{{ __('platform.statuses.'.$status->value) }}</option>
                        @endforeach
                    </select>
                    @error('subscription_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label" for="grace_ends_at">{{ __('platform.grace_ends_at') }}</label>
                    <input type="date" class="form-control @error('grace_ends_at') is-invalid @enderror" id="grace_ends_at" name="grace_ends_at" value="{{ old('grace_ends_at', $school->grace_ends_at?->toDateString()) }}">
                    @error('grace_ends_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <p class="text-muted fs-13">{{ __('platform.readonly_note') }}</p>
            <button type="submit" class="btn btn-primary">{{ __('platform.save') }}</button>
        </div></div>
    </form>
@endsection
