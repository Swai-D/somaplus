<div class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <a href="{{ route('dashboard') }}" class="logo logo-normal"><img src="{{ asset('img/somaplus-logo.svg') }}" alt="{{ config('app.name') }}"></a>
        <a href="{{ route('dashboard') }}" class="logo logo-white"><img src="{{ asset('img/somaplus-logo-white.svg') }}" alt="{{ config('app.name') }}"></a>
        <a href="{{ route('dashboard') }}" class="logo-small"><img src="{{ asset('assets/img/logo-small.svg') }}" alt="{{ config('app.name') }}"></a>
        <a href="{{ route('dashboard') }}" class="logo-small-white"><img src="{{ asset('assets/img/logo-small-white.svg') }}" alt="{{ config('app.name') }}"></a>
        <a id="toggle_btn" href="javascript:void(0);"><i data-feather="chevrons-left" class="feather-16"></i></a>
    </div>

    <div class="sidebar-header p-3 pb-0 pt-2">
        <div class="text-center rounded bg-light p-2 mb-4 sidebar-profile d-flex align-items-center">
            <div class="avatar avatar-md online">
                <span class="avatar avatar-md bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center">{{ mb_strtoupper(mb_substr($currentUser->name, 0, 1)) }}</span>
            </div>
            <div class="text-start sidebar-profile-info ms-2">
                <h6 class="fs-14 fw-bold mb-1">{{ $currentSchool?->name ?? config('app.name') }}</h6>
                <p class="fs-12">{{ $currentUser->name }}</p>
            </div>
        </div>
    </div>

    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                @foreach ($menu as $section)
                    <li class="submenu-open">
                        <h6 class="submenu-hdr">{{ $section['label'] }}</h6>
                        <ul>
                            @foreach ($section['items'] as $item)
                                @if ($item['children'] === [])
                                    <li><a href="{{ $item['url'] }}" class="{{ $item['active'] ? 'active' : '' }}"><i class="{{ $item['icon'] }} fs-16 me-2"></i><span>{{ $item['label'] }}</span></a></li>
                                @else
                                    <li class="submenu">
                                        <a href="javascript:void(0);" class="{{ $item['active'] ? 'subdrop active' : '' }}"><i class="{{ $item['icon'] }} fs-16 me-2"></i><span>{{ $item['label'] }}</span><span class="menu-arrow"></span></a>
                                        <ul style="{{ $item['active'] ? 'display: block;' : '' }}">
                                            @foreach ($item['children'] as $child)
                                                <li><a href="{{ $child['url'] }}" class="{{ $child['active'] ? 'active' : '' }}">{{ $child['label'] }}</a></li>
                                            @endforeach
                                        </ul>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
