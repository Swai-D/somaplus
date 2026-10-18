@extends('layouts.app')

@section('title', __('academics.classes.edit'))

@section('page')
    <div class="mb-3">
        <a href="{{ route('academics.classes.show', $class) }}" class="text-muted"><i class="ti ti-arrow-left me-1"></i>{{ $class->name }}</a>
        <h3 class="page-title mt-2">{{ __('academics.classes.edit') }}</h3>
    </div>
    <div class="card"><div class="card-body">@include('academics.classes._form')</div></div>
@endsection
