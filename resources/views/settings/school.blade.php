@extends('layouts.app')

@section('title', __('school.title'))

@section('page')
    <div class="mb-3">
        <h3 class="page-title mb-1">{{ __('school.title') }}</h3>
        <p class="text-muted mb-0">{{ __('school.intro') }}</p>
    </div>

    <form method="POST" action="{{ route('settings.school.update') }}">
        @csrf
        @method('PUT')
        <div class="card"><div class="card-body">
            <div class="mb-3">
                <label class="form-label">{{ __('school.name') }}</label>
                <input class="form-control" value="{{ $school->name }}" disabled>
                <div class="form-text">{{ __('school.name_hint') }}</div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="phone">{{ __('school.phone') }}</label>
                    <input class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $profile->phone) }}" maxlength="30">
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="email">{{ __('school.email') }}</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $profile->email) }}" maxlength="190">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label" for="address">{{ __('school.address') }}</label>
                <input class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $profile->address) }}" maxlength="250">
                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <p class="text-muted fs-13">{{ __('school.used_on') }}</p>
            <button type="submit" class="btn btn-primary">{{ __('school.save') }}</button>
        </div></div>
    </form>
@endsection
