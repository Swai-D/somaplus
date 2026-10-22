@extends('layouts.base')

@push('head')
    <script src="{{ asset('assets/js/theme-script.js') }}"></script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/edu-widgets.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/somaplus-app.css') }}">
@endpush

@section('content')
    <div id="global-loader"><div class="whirly-loader"></div></div>

    <div class="main-wrapper">
        @include('partials.header')
        @include('partials.sidebar')

        <div class="page-wrapper">
            <div class="content">
                @if ($currentSchool?->isReadOnly())
                    <div class="alert alert-warning" role="alert">{{ __('tenancy.read_only') }}</div>
                @endif

                @if (session('status'))
                    <div class="alert alert-success" role="status">{{ session('status') }}</div>
                @endif
                @error('delete')
                    <div class="alert alert-danger" role="alert">{{ $message }}</div>
                @enderror

                @yield('page')
            </div>
            @include('partials.footer')
        </div>
    </div>
    @stack('modals')
@endsection

@push('page-scripts')
    <script>
        // A form in a pop-up that was refused opens its pop-up again, with what was typed.
        (function () {
            var wanted = document.body.dataset.openModal;
            if (!wanted || !window.bootstrap) { return; }

            var modal = document.getElementById(wanted);
            if (modal) { new window.bootstrap.Modal(modal).show(); }
        })();
    </script>
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/feather.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/simplebar/simplebar.min.js') }}"></script>
    @stack('vendor')
    <script src="{{ asset('assets/plugins/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable.js') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>
    @stack('page-scripts')
@endpush
