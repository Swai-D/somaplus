<div class="header">
    <div class="main-header">
        <div class="header-left active">
            <a class="logo logo-normal" href="{{ route('dashboard') }}"><img alt="{{ config('app.name') }}" src="{{ asset('img/somaplus-logo.svg') }}"></a>
            <a class="logo logo-white" href="{{ route('dashboard') }}"><img alt="{{ config('app.name') }}" src="{{ asset('img/somaplus-logo-white.svg') }}"></a>
            <a class="logo-small" href="{{ route('dashboard') }}"><img alt="{{ config('app.name') }}" src="{{ asset('assets/img/logo-small.svg') }}"></a>
            <a class="logo-small-white" href="{{ route('dashboard') }}"><img alt="{{ config('app.name') }}" src="{{ asset('assets/img/logo-small-white.svg') }}"></a>
        </div>

        <a class="mobile_btn" href="#sidebar" id="mobile_btn">
            <span class="bar-icon">
                <span></span>
                <span></span>
                <span></span>
            </span>
        </a>

        <ul class="nav user-menu">
            @can('viewAny', App\Models\Student::class)
                <li class="nav-item nav-searchinputs">
                    <div class="top-nav-search">
                        <a class="responsive-search" href="javascript:void(0);"><i class="fa fa-search"></i></a>
                        <form action="{{ route('students.search') }}" method="POST" class="dropdown">
                            @csrf
                            <div class="searchinputs input-group dropdown-toggle" data-bs-auto-close="outside" data-bs-toggle="dropdown" id="navbarSearch">
                                <input type="search" name="term" placeholder="{{ __('layout.search') }}" aria-label="{{ __('layout.search') }}" minlength="2" maxlength="100" required autocomplete="off">
                                <div class="search-addon">
                                    <button type="submit" class="btn p-0 border-0 bg-transparent" aria-label="{{ __('layout.search') }}">
                                        <span><i class="ti ti-search"></i></span>
                                    </button>
                                </div>
                            </div>
                            <div aria-labelledby="navbarSearch" class="dropdown-menu search-dropdown">
                                <div class="search-info">
                                    <h6><span><i class="feather-16" data-feather="search"></i></span>{{ __('layout.search_hint') }}</h6>
                                </div>
                                <div class="search-info">
                                    <h6><span><i class="feather-16" data-feather="help-circle"></i></span>{{ __('layout.help') }}</h6>
                                    <p class="mb-0"><a href="{{ route('help.show', ['category' => 'students', 'slug' => 'add-and-find']) }}">{{ __('layout.search_help') }}</a></p>
                                </div>
                            </div>
                        </form>
                    </div>
                </li>
            @endcan

            @if ($quickActions !== [])
                <li class="nav-item dropdown link-nav">
                    <a href="javascript:void(0);" class="btn btn-primary btn-md d-inline-flex align-items-center" data-bs-toggle="dropdown">
                        <i class="ti ti-circle-plus me-1"></i>{{ __('layout.add_new') }}
                    </a>
                    <div class="dropdown-menu dropdown-xl dropdown-menu-center">
                        <div class="row g-2">
                            @foreach ($quickActions as $action)
                                <div class="col-md-2">
                                    <a href="{{ $action['url'] }}" class="link-item">
                                        <span class="link-icon"><i class="{{ $action['icon'] }}"></i></span>
                                        <p>{{ $action['label'] }}</p>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </li>
            @endif

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
                <a href="{{ route('help.index') }}" title="{{ __('layout.help') }}"><i class="ti ti-help-circle"></i></a>
            </li>

            <li class="nav-item nav-item-box">
                <a href="javascript:void(0);" id="btnFullscreen" title="{{ __('layout.fullscreen') }}"><i class="ti ti-maximize"></i></a>
            </li>

            <li class="nav-item dropdown nav-item-box">
                <a href="javascript:void(0);" class="dropdown-toggle nav-link" data-bs-toggle="dropdown" title="{{ __('layout.notifications') }}">
                    <i class="ti ti-bell"></i>
                    @if ($alerts !== [])
                        <span class="badge rounded-pill">{{ count($alerts) }}</span>
                    @endif
                </a>
                <div class="dropdown-menu notifications">
                    <div class="topnav-dropdown-header">
                        <h5 class="notification-title">{{ __('layout.notifications') }}</h5>
                    </div>
                    <div class="noti-content">
                        <ul class="notification-list">
                            @forelse ($alerts as $alert)
                                <li class="notification-message">
                                    <div class="media d-flex p-2">
                                        <span class="avatar flex-shrink-0 bg-{{ $alert['tone'] }}-transparent text-{{ $alert['tone'] }}"><i class="{{ $alert['icon'] }} fs-18"></i></span>
                                        <div class="flex-grow-1 ms-2">
                                            <p class="noti-details mb-0">
                                                <span class="noti-title">{{ $alert['title'] }}</span> {{ $alert['message'] }}
                                            </p>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="p-3 text-center text-muted">{{ __('layout.no_notifications') }}</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </li>

            @if ($settingsUrl !== null)
                <li class="nav-item nav-item-box">
                    <a href="{{ $settingsUrl }}" title="{{ __('layout.settings') }}"><i class="ti ti-settings"></i></a>
                </li>
            @endif

            <li class="nav-item dropdown has-arrow main-drop profile-nav">
                <a href="javascript:void(0);" class="nav-link userset" data-bs-toggle="dropdown">
                    <span class="user-info p-0">
                        <span class="avatar avatar-md bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center">{{ mb_strtoupper(mb_substr($currentUser->name, 0, 1)) }}</span>
                    </span>
                </a>
                <div class="dropdown-menu menu-drop-user">
                    <div class="profileset d-flex align-items-center">
                        <span class="user-img me-2">
                            <span class="avatar avatar-md bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center">{{ mb_strtoupper(mb_substr($currentUser->name, 0, 1)) }}</span>
                        </span>
                        <div>
                            <h6 class="fw-medium">{{ $currentUser->name }}</h6>
                            <p>{{ $roleLabel }}@if ($currentSchool) &middot; {{ $currentSchool->name }}@endif</p>
                        </div>
                    </div>
                    @if ($settingsUrl !== null)
                        <a class="dropdown-item" href="{{ $settingsUrl }}"><i class="ti ti-settings-2 me-2"></i>{{ __('layout.settings') }}</a>
                    @endif
                    <a class="dropdown-item" href="{{ route('help.index') }}"><i class="ti ti-help-circle me-2"></i>{{ __('layout.help') }}</a>
                    <hr class="my-2">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item logout w-100 text-start"><i class="ti ti-logout me-2"></i>{{ __('layout.logout') }}</button>
                    </form>
                </div>
            </li>
        </ul>

        <div class="dropdown mobile-user-menu">
            <a aria-expanded="false" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="javascript:void(0);"><i class="fa fa-ellipsis-v"></i></a>
            <div class="dropdown-menu dropdown-menu-right">
                @foreach ($quickActions as $action)
                    <a class="dropdown-item" href="{{ $action['url'] }}">{{ $action['label'] }}</a>
                @endforeach
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="dropdown-item">{{ __('layout.logout') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
