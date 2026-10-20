@extends('layouts.app')

@section('title', __('exams.scales.add'))

@section('page')
    <div class="mb-3">
        <a href="{{ route('grading-scales.index') }}" class="text-muted"><i class="ti ti-arrow-left me-1"></i>{{ __('menu.grading_scales') }}</a>
        <h3 class="page-title mt-2">{{ __('exams.scales.add') }}</h3>
    </div>
    <div class="card"><div class="card-body">@include('exams.scales._form', ['scale' => null])</div></div>
@endsection
