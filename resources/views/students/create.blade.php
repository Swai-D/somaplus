@extends('layouts.app')

@section('title', __('students.create.title'))

@section('page')
    <div class="mb-3">
        <a href="{{ route('students.index') }}" class="text-muted"><i class="ti ti-arrow-left me-1"></i>{{ __('menu.student_list') }}</a>
        <h3 class="page-title mt-2">{{ __('students.create.title') }}</h3>
    </div>
    <div class="card"><div class="card-body">@include('students._form', ['student' => null])</div></div>
@endsection
