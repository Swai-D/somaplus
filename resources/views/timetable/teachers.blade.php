@extends('layouts.app')

@section('title', __('menu.teacher_timetable'))

@section('page')
    @include('timetable._print')

    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3 d-print-none">
        <div>
            <h3 class="page-title mb-1">{{ __('menu.teacher_timetable') }}</h3>
            <p class="text-muted mb-0">{{ __('timetable.teachers_intro') }}</p>
        </div>
        @if ($week !== null)
            <button type="button" class="btn btn-outline-secondary mt-2 mt-md-0" onclick="window.print()"><i class="ti ti-printer me-1"></i>{{ __('timetable.print') }}</button>
        @endif
    </div>

    <div class="card d-print-none">
        <div class="card-body">
            <form action="{{ route('timetable.teachers') }}" method="GET">
                <div class="row align-items-end">
                    @include('timetable._term_filter')
                    <div class="col-md-5 mb-3">
                        <label class="form-label" for="teacher_id">{{ __('timetable.teacher') }}</label>
                        <select class="form-select @error('teacher_id') is-invalid @enderror" id="teacher_id" name="teacher_id" required>
                            <option value="">{{ __('timetable.choose') }}</option>
                            @foreach ($teachers as $option)
                                <option value="{{ $option->id }}" @selected($teacher?->id === $option->id)>{{ $option->fullName() }}</option>
                            @endforeach
                        </select>
                        @error('teacher_id')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3 mb-3"><button type="submit" class="btn btn-primary w-100">{{ __('timetable.show') }}</button></div>
                </div>
            </form>
        </div>
    </div>

    @if ($term === null)
        <div class="alert alert-warning">{{ __('timetable.no_term') }}</div>
    @elseif ($teacher === null)
        <div class="card"><div class="card-body text-muted">{{ __('timetable.pick_teacher') }}</div></div>
    @elseif ($week !== null)
        <div class="card">
            <div class="card-body p-3">
                <h5 class="mb-3">{{ $teacher->fullName() }} · {{ $term->academicYear?->name }} {{ $term->name }}</h5>
                @include('timetable._week', ['week' => $week, 'show' => 'teacher', 'canManage' => false])
            </div>
        </div>
    @endif
@endsection
