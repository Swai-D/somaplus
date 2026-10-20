@extends('layouts.app')

@section('title', __('exams.scales.edit'))

@section('page')
    <div class="mb-3">
        <a href="{{ route('grading-scales.index') }}" class="text-muted"><i class="ti ti-arrow-left me-1"></i>{{ __('menu.grading_scales') }}</a>
        <h3 class="page-title mt-2">{{ __('exams.scales.edit') }}</h3>
    </div>
    @if ($used)
        <div class="alert alert-warning">
            {{ __('exams.scales.used') }}
            <form action="{{ route('grading-scales.copy', $scale) }}" method="POST" class="mt-2">
                @csrf
                <button type="submit" class="btn btn-sm btn-primary">{{ __('exams.scales.copy') }}</button>
            </form>
        </div>
        <div class="card"><div class="card-body">@include('exams.scales._bands', ['bands' => $scale->bands])</div></div>
    @else
        <div class="card"><div class="card-body">@include('exams.scales._form')</div></div>
    @endif
@endsection
