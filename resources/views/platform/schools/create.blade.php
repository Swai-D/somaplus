@extends('layouts.app')

@section('title', __('platform.add'))

@section('page')
    <div class="mb-3">
        <h3 class="page-title mb-1">{{ __('platform.add') }}</h3>
        <a href="{{ route('platform.schools.index') }}">{{ __('platform.back') }}</a>
    </div>

    <form method="POST" action="{{ route('platform.schools.store') }}">
        @csrf
        <div class="card"><div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="name">{{ __('platform.name') }}</label>
                    <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" maxlength="150" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="slug">{{ __('platform.slug') }}</label>
                    <div class="input-group">
                        <input class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug') }}" maxlength="40" required>
                        <span class="input-group-text">.{{ $baseDomain }}</span>
                        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-text">{{ __('platform.slug_hint') }}</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="domain">{{ __('platform.own_domain') }}</label>
                    <input class="form-control @error('domain') is-invalid @enderror" id="domain" name="domain" value="{{ old('domain') }}" maxlength="190">
                    @error('domain')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">{{ __('platform.own_domain_hint') }}</div>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label" for="subscription_plan_id">{{ __('platform.plan') }}</label>
                    <select class="form-select @error('subscription_plan_id') is-invalid @enderror" id="subscription_plan_id" name="subscription_plan_id" required>
                        @foreach ($plans as $plan)
                            <option value="{{ $plan->id }}" @selected((int) old('subscription_plan_id') === $plan->id)>{{ $plan->name }}</option>
                        @endforeach
                    </select>
                    @error('subscription_plan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label" for="currency">{{ __('platform.currency') }}</label>
                    <select class="form-select @error('currency') is-invalid @enderror" id="currency" name="currency" required>
                        @foreach ($currencies as $code)
                            <option value="{{ $code }}" @selected(old('currency', 'TZS') === $code)>{{ $code }}</option>
                        @endforeach
                    </select>
                    @error('currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">{{ __('platform.currency_hint') }}</div>
                </div>
            </div>
            <h5 class="mt-2">{{ __('platform.owner') }}</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="owner_name">{{ __('platform.owner_name') }}</label>
                    <input class="form-control @error('owner_name') is-invalid @enderror" id="owner_name" name="owner_name" value="{{ old('owner_name') }}" maxlength="150" required>
                    @error('owner_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="owner_email">{{ __('platform.owner_email') }}</label>
                    <input type="email" class="form-control @error('owner_email') is-invalid @enderror" id="owner_email" name="owner_email" value="{{ old('owner_email') }}" maxlength="190" required>
                    @error('owner_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">{{ __('platform.owner_hint') }}</div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">{{ __('platform.create') }}</button>
        </div></div>
    </form>
@endsection
