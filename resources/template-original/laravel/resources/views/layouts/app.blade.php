@verbatim
<!DOCTYPE html>
<html lang="en" data-layout-mode="light_mode">
<head>
<meta charset="utf-8"><meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@endverbatim@yield('title', 'SomaPlus')@verbatim | SomaPlus School Management</title>
<script src="@endverbatim{{ asset('assets/js/theme-script.js') }}@verbatim"></script>
<link rel="shortcut icon" type="image/svg+xml" href="@endverbatim{{ asset('assets/img/favicon.svg') }}@verbatim">
<link rel="stylesheet" href="@endverbatim{{ asset('assets/css/bootstrap.min.css') }}@verbatim">
<link rel="stylesheet" href="@endverbatim{{ asset('assets/css/animate.css') }}@verbatim">
<link rel="stylesheet" href="@endverbatim{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}@verbatim">
<link rel="stylesheet" href="@endverbatim{{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }}@verbatim">
<link rel="stylesheet" href="@endverbatim{{ asset('assets/plugins/fontawesome/css/all.min.css') }}@verbatim">
<link rel="stylesheet" href="@endverbatim{{ asset('assets/css/style.css') }}@verbatim">
<link rel="stylesheet" href="@endverbatim{{ asset('assets/css/edu-widgets.css') }}@verbatim">
<link rel="stylesheet" href="@endverbatim{{ asset('assets/css/somaplus.css') }}@verbatim">
<link rel="stylesheet" href="@endverbatim{{ asset('assets/plugins/flatpickr/flatpickr.min.css') }}@verbatim">
</head>
<body>
<div id="global-loader"><div class="whirly-loader"></div></div>

@endverbatim
<div class="main-wrapper">
@include('partials.header')
@include('partials.sidebar')
<div class="page-wrapper"><div class="content">
@yield('content')
</div>
@include('partials.footer')
</div>
</div>
@stack('modals')
@verbatim
<script src="@endverbatim{{ asset('assets/js/bootstrap.bundle.min.js') }}@verbatim"></script>
<script src="@endverbatim{{ asset('assets/js/feather.min.js') }}@verbatim"></script>
<script src="@endverbatim{{ asset('assets/plugins/simplebar/simplebar.min.js') }}@verbatim"></script>
@endverbatim@stack('vendor')@verbatim<script src="@endverbatim{{ asset('assets/js/datatable.js') }}@verbatim"></script>
<script src="@endverbatim{{ asset('assets/plugins/flatpickr/flatpickr.min.js') }}@verbatim"></script>
<script src="@endverbatim{{ asset('assets/js/script.js') }}@verbatim"></script>
<script src="@endverbatim{{ asset('assets/js/somaplus-i18n.js') }}@verbatim"></script>
<script src="@endverbatim{{ asset('assets/js/somaplus-crud.js') }}@verbatim"></script>
@endverbatim@stack('scripts')@verbatim
</body>
</html>

@endverbatim