@extends('layouts.base')

@section('title', __('passwords.forgot_title'))
@section('body_class', 'account-page')

@section('content')
    <div id="global-loader"><div class="whirly-loader"></div></div>

    <div class="main-wrapper">
        <div class="account-content">
            <div class="login-wrapper bg-img">
                <div class="login-content authent-content">
                    <form action="{{ route('password.email') }}" method="POST">
                        @csrf
                        <div class="login-userset">
                            <div class="login-logo logo-normal"><img alt="{{ config('app.name') }}" src="{{ asset('img/somaplus-logo.svg') }}"></div>
                            <a class="login-logo logo-white" href="{{ route('login') }}"><img alt="{{ config('app.name') }}" src="{{ asset('img/somaplus-logo-white.svg') }}"></a>

                            <div class="login-userheading">
                                <h3>{{ __('passwords.forgot_title') }}</h3>
                                <h4 class="fs-16">{{ $school ? __('passwords.forgot_subtitle_school', ['school' => $school->name]) : __('passwords.forgot_subtitle_platform') }}</h4>
                            </div>

                            @if (session('status'))
                                <div class="alert alert-success" role="status">{{ session('status') }}</div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label" for="email">{{ __('auth.email') }}<span class="text-danger"> *</span></label>
                                <div class="input-group">
                                    <input class="form-control border-end-0 @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email') }}" placeholder="{{ __('auth.email_placeholder') }}" autocomplete="username" required autofocus>
                                    <span class="input-group-text border-start-0"><i class="ti ti-mail"></i></span>
                                </div>
                                @error('email')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-login">
                                <button class="btn btn-primary w-100" type="submit">{{ __('passwords.send_link') }}</button>
                            </div>

                            <div class="signinform">
                                <h4><a class="hover-a" href="{{ route('login') }}">{{ __('passwords.back_to_login') }}</a></h4>
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
