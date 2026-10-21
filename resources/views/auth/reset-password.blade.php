@extends('layouts.base')

@section('title', __('passwords.reset_title'))
@section('body_class', 'account-page')

@section('content')
    <div id="global-loader"><div class="whirly-loader"></div></div>

    <div class="main-wrapper">
        <div class="account-content">
            <div class="login-wrapper bg-img">
                <div class="login-content authent-content">
                    <form action="{{ route('password.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <div class="login-userset">
                            <div class="login-logo logo-normal"><img alt="{{ config('app.name') }}" src="{{ asset('img/somaplus-logo.svg') }}"></div>
                            <a class="login-logo logo-white" href="{{ route('login') }}"><img alt="{{ config('app.name') }}" src="{{ asset('img/somaplus-logo-white.svg') }}"></a>

                            <div class="login-userheading">
                                <h3>{{ __('passwords.reset_title') }}</h3>
                                <h4 class="fs-16">{{ __('passwords.reset_subtitle') }}</h4>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="email">{{ __('auth.email') }}<span class="text-danger"> *</span></label>
                                <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email', $email) }}" autocomplete="username" required>
                                @error('email')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="password">{{ __('passwords.new_password') }}<span class="text-danger"> *</span></label>
                                <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" autocomplete="new-password" required>
                                @error('password')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="password_confirmation">{{ __('passwords.confirm_password') }}<span class="text-danger"> *</span></label>
                                <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
                            </div>

                            <div class="form-login">
                                <button class="btn btn-primary w-100" type="submit">{{ __('passwords.reset_submit') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>
@endpush
