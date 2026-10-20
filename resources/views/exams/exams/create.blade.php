@extends('layouts.app')

@section('title', __('exams.exams.add'))

@section('page')
    <div class="mb-3">
        <a href="{{ route('exams.index') }}" class="text-muted"><i class="ti ti-arrow-left me-1"></i>{{ __('menu.exam_list') }}</a>
        <h3 class="page-title mt-2">{{ __('exams.exams.add') }}</h3>
    </div>
    @if ($scales->isEmpty())
        <div class="alert alert-warning">{{ __('exams.exams.need_scale') }}</div>
    @endif
    <div class="card"><div class="card-body">@include('exams.exams._form', ['exam' => null])</div></div>
@endsection
