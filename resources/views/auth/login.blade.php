@extends('layouts.base')

@section('title', __('auth.title'))
@section('body_class', 'account-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}">
@endpush

@section('content')
    <div id="global-loader"><div class="whirly-loader"></div></div>

    <div class="main-wrapper">
        <div class="account-content">
            <div class="login-wrapper bg-img">
                <div class="login-content authent-content">
                    <form action="{{ route('login.store') }}" method="POST">
                        @csrf
                        <div class="login-userset">
                            <div class="login-logo logo-normal"><img alt="{{ config('app.name') }}" src="{{ asset('img/somaplus-logo.svg') }}"></div>
                            <a class="login-logo logo-white" href="{{ route('login') }}"><img alt="{{ config('app.name') }}" src="{{ asset('img/somaplus-logo-white.svg') }}"></a>

                            <div class="login-userheading">
                                <h3>{{ __('auth.title') }}</h3>
                                <h4 class="fs-16">{{ $school ? __('auth.subtitle_school', ['school' => $school->name]) : __('auth.subtitle_platform') }}</h4>
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

                            <div class="mb-3">
                                <label class="form-label" for="password">{{ __('auth.password') }}<span class="text-danger"> *</span></label>
                                <div class="pass-group">
                                    <input class="pass-input form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" placeholder="{{ __('auth.password_placeholder') }}" autocomplete="current-password" required>
                                    <span class="ti toggle-password ti-eye-off text-gray-9"></span>
                                </div>
                                @error('password')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-login authentication-check d-flex align-items-center justify-content-between">
                                <div class="custom-control custom-checkbox">
                                    <label class="checkboxs ps-4 mb-0 pb-0 line-height-1 fs-16 text-gray-6">
                                        <input class="form-control" name="remember" type="checkbox" value="1" {{ old('remember') ? 'checked' : '' }}>
                                        <span class="checkmarks"></span>{{ __('auth.remember') }}
                                    </label>
                                </div>
                                <div class="text-end">
                                    <a class="text-orange fs-16 fw-medium" href="{{ route('password.request') }}">{{ __('auth.forgot') }}</a>
                                </div>
                            </div>

                            <div class="form-login">
                                <button class="btn btn-primary w-100" type="submit">{{ __('auth.submit') }}</button>
                            </div>

                            <div class="my-4 d-flex justify-content-center align-items-center copyright-text">
                                <p>&copy; {{ now()->year }} {{ config('app.name') }}</p>
                            </div>
                        </div>
                    </form>

                    <form action="{{ route('locale.update') }}" method="POST" class="d-flex justify-content-center gap-2 mt-2">
                        @csrf
                        @foreach (['en' => 'English', 'sw' => 'Kiswahili'] as $code => $name)
                            <button type="submit" name="locale" value="{{ $code }}" class="btn btn-sm {{ app()->getLocale() === $code ? 'btn-primary' : 'btn-outline-white' }}">{{ $name }}</button>
                        @endforeach
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
