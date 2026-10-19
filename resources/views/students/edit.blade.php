@extends('layouts.app')

@section('title', __('students.edit_title'))

@section('page')
    <div class="mb-3">
        <a href="{{ route('students.show', $student) }}" class="text-muted"><i class="ti ti-arrow-left me-1"></i>{{ $student->fullName() }}</a>
        <h3 class="page-title mt-2">{{ __('students.edit_title') }}</h3>
    </div>
    <div class="card"><div class="card-body">@include('students._form')</div></div>
@endsection
