@extends('layouts.app')

@section('title', __('academics.subjects.edit'))

@section('page')
    <div class="mb-3">
        <a href="{{ route('academics.subjects.index') }}" class="text-muted"><i class="ti ti-arrow-left me-1"></i>{{ __('menu.subjects') }}</a>
        <h3 class="page-title mt-2">{{ __('academics.subjects.edit') }}</h3>
    </div>
    <div class="card"><div class="card-body">@include('academics.subjects._form')</div></div>
@endsection
