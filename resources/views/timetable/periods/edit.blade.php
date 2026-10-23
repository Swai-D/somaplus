@extends('layouts.app')

@section('title', __('timetable.periods.edit_title'))

@section('page')
    <div class="mb-3">
        <h3 class="page-title mb-1">{{ __('timetable.periods.edit_title') }}</h3>
        <p class="text-muted mb-0">{{ $period->name }}</p>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('timetable.periods.update', $period) }}" method="POST">
                @csrf
                @method('PUT')
                @include('timetable.periods._fields', ['period' => $period])
                <a href="{{ route('timetable.periods.index') }}" class="btn btn-secondary me-2">{{ __('timetable.cancel') }}</a>
                <button type="submit" class="btn btn-primary">{{ __('timetable.save') }}</button>
            </form>
        </div>
    </div>
@endsection
