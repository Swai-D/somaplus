<div class="header">
    <div class="main-header">
        <div class="header-left active">
            <a class="logo logo-normal" href="{{ route('dashboard') }}"><img alt="{{ config('app.name') }}" src="{{ asset('assets/img/logo.svg') }}"></a>
            <a class="logo logo-white" href="{{ route('dashboard') }}"><img alt="{{ config('app.name') }}" src="{{ asset('assets/img/logo-white.svg') }}"></a>
            <a class="logo-small" href="{{ route('dashboard') }}"><img alt="{{ config('app.name') }}" src="{{ asset('assets/img/logo-small.svg') }}"></a>
            <a class="logo-small-white" href="{{ route('dashboard') }}"><img alt="{{ config('app.name') }}" src="{{ asset('assets/img/logo-small-white.svg') }}"></a>
        </div>

        <a class="mobile_btn" href="#sidebar" id="mobile_btn">
            <span class="bar-icon"><span></span><span></span><span></span></span>
        </a>

        <ul class="nav user-menu">
            <li class="nav-item dropdown has-arrow flag-nav nav-item-box">
                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="javascript:void(0);" role="button" title="{{ __('layout.language') }}">
                    <img alt="{{ __('layout.language') }}" class="img-fluid" src="{{ asset(app()->getLocale() === 'sw' ? 'assets/img/flags/tz.svg' : 'assets/img/flags/us-flag.svg') }}">
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    @foreach (['en' => ['English', 'assets/img/flags/english.svg'], 'sw' => ['Kiswahili', 'assets/img/flags/tz.svg']] as $code => [$name, $flag])
                        <form action="{{ route('locale.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="locale" value="{{ $code }}">
                            <button type="submit" class="dropdown-item {{ app()->getLocale() === $code ? 'active' : '' }}">
                                <img alt="" height="16" src="{{ asset($flag) }}"> {{ $name }}
                            </button>
                        </form>
                    @endforeach
                </div>
            </li>

            <li class="nav-item nav-item-box">
                <a href="javascript:void(0);" id="btnFullscreen" title="{{ __('layout.fullscreen') }}"><i class="ti ti-maximize"></i></a>
            </li>

            <li class="nav-item dropdown has-arrow main-drop profile-nav">
                <a class="nav-link userset" data-bs-toggle="dropdown" href="javascript:void(0);">
                    <span class="user-info p-0">
                        <span class="avatar avatar-md bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center">{{ mb_strtoupper(mb_substr($currentUser->name, 0, 1)) }}</span>
                    </span>
                </a>
                <div class="dropdown-menu menu-drop-user">
                    <div class="profileset d-flex align-items-center">
                        <div>
                            <h6 class="fw-medium">{{ $currentUser->name }}</h6>
                            <p>{{ $roleLabel }}</p>
                        </div>
                    </div>
                    <hr class="my-2">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item logout"><i class="ti ti-logout me-2"></i>{{ __('layout.logout') }}</button>
                    </form>
                </div>
            </li>
        </ul>

        <div class="dropdown mobile-user-menu">
            <a aria-expanded="false" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="javascript:void(0);"><i class="fa fa-ellipsis-v"></i></a>
            <div class="dropdown-menu dropdown-menu-right">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="dropdown-item">{{ __('layout.logout') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
