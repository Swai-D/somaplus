@extends('layouts.app')

@section('title', __('custom_roles.add'))

@section('page')
    <div class="mb-3">
        <a href="{{ route('settings.roles.index') }}" class="text-muted"><i class="ti ti-arrow-left me-1"></i>{{ __('custom_roles.title') }}</a>
        <h3 class="page-title mt-2 mb-0">{{ __('custom_roles.add') }}</h3>
    </div>

    @include('settings.roles._form')
@endsection
