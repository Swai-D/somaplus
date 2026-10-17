@extends('layouts.base')

@section('content')
    <main class="container min-vh-100 d-flex flex-column justify-content-center align-items-center text-center">
        <img src="{{ asset('assets/img/logo.svg') }}" alt="{{ config('app.name') }}" class="mb-4" height="48">
        <h1 class="h3 mb-2">{{ config('app.name') }}</h1>
        <p class="text-muted mb-1">{{ __('app.tagline') }}</p>
        <p class="text-muted small">{{ __('app.made_by') }}</p>
    </main>
@endsection
