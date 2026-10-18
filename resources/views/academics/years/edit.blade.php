@extends('layouts.app')

@section('title', __('academics.years.edit'))

@section('page')
    <div class="mb-3">
        <a href="{{ route('academics.years.show', $year) }}" class="text-muted"><i class="ti ti-arrow-left me-1"></i>{{ $year->name }}</a>
        <h3 class="page-title mt-2">{{ __('academics.years.edit') }}</h3>
    </div>
    <div class="card"><div class="card-body">@include('academics.years._form')</div></div>
@endsection
