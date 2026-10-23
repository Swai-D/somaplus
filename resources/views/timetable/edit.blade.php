@extends('layouts.app')

@section('title', __('timetable.edit_title'))

@section('page')
    <div class="mb-3">
        <h3 class="page-title mb-1">{{ __('timetable.edit_title') }}</h3>
        <p class="text-muted mb-0">{{ $entry->subject->name }} · {{ $entry->groupLabel() }}</p>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('timetable.entries.update', $entry) }}" method="POST">
                @csrf
                @method('PUT')
                @include('timetable._entry_fields', ['defaults' => []])
                <a href="{{ route('timetable.index', array_filter(['term_id' => $entry->term_id, 'school_class_id' => $entry->school_class_id, 'stream_id' => $entry->stream_id])) }}" class="btn btn-secondary me-2">{{ __('timetable.cancel') }}</a>
                <button type="submit" class="btn btn-primary">{{ __('timetable.save') }}</button>
            </form>
        </div>
    </div>

    @include('timetable._entry_script')
@endsection
