@extends('layouts.app')

@section('title', __('exams.exams.edit'))

@section('page')
    <div class="mb-3">
        <a href="{{ route('exams.show', $exam) }}" class="text-muted"><i class="ti ti-arrow-left me-1"></i>{{ $exam->name }}</a>
        <h3 class="page-title mt-2">{{ __('exams.exams.edit') }}</h3>
    </div>
    <div class="card"><div class="card-body">@include('exams.exams._form')</div></div>
@endsection
