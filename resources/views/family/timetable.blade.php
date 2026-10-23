@extends('layouts.app')

@section('title', __('timetable.family_title'))

@section('page')
    @include('timetable._print')

    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3 d-print-none">
        <div>
            <h3 class="page-title mb-1">{{ __('timetable.family_title') }}</h3>
            <p class="text-muted mb-0">{{ $student->fullName() }}</p>
        </div>
        <div class="d-flex gap-2 mt-2 mt-md-0">
            <a href="{{ route('family.children.show', $student->id) }}" class="btn btn-outline-secondary">{{ __('timetable.back') }}</a>
            @if ($week !== null && $week['class'] !== null)
                <button type="button" class="btn btn-outline-secondary" onclick="window.print()"><i class="ti ti-printer me-1"></i>{{ __('timetable.print') }}</button>
            @endif
        </div>
    </div>

    <div class="card d-print-none">
        <div class="card-body">
            <form action="{{ route('family.children.timetable', $student->id) }}" method="GET">
                <div class="row align-items-end">
                    @include('timetable._term_filter')
                    <div class="col-md-3 mb-3"><button type="submit" class="btn btn-primary w-100">{{ __('timetable.show') }}</button></div>
                </div>
            </form>
        </div>
    </div>

    @if ($term === null)
        <div class="alert alert-warning">{{ __('timetable.no_term') }}</div>
    @elseif ($week === null || $week['class'] === null)
        <div class="card"><div class="card-body text-muted">{{ __('timetable.family_not_placed') }}</div></div>
    @else
        <div class="card">
            <div class="card-body p-3">
                <h5 class="mb-3">{{ $week['class']->name }}@if ($week['stream']) – {{ $week['stream']->name }}@endif · {{ $term->academicYear?->name }} {{ $term->name }}</h5>
                @include('timetable._week', ['week' => $week, 'show' => 'class', 'canManage' => false])
            </div>
        </div>
    @endif
@endsection
