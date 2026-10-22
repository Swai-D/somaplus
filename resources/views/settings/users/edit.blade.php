@extends('layouts.app')

@section('title', __('custom_roles.users.edit'))

@section('page')
    <div class="mb-3">
        <a href="{{ route('settings.users.index') }}" class="text-muted"><i class="ti ti-arrow-left me-1"></i>{{ __('custom_roles.users.title') }}</a>
        <h3 class="page-title mt-2 mb-0">{{ __('custom_roles.users.edit') }}</h3>
    </div>

    @include('settings.users._form')
@endsection
