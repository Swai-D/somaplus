@extends('layouts.app')

@section('title', __('menu.class_timetable'))

@section('page')
    @include('timetable._print')

    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3 d-print-none">
        <div>
            <h3 class="page-title mb-1">{{ __('menu.class_timetable') }}</h3>
            <p class="text-muted mb-0">{{ __('timetable.intro') }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
            @if ($week !== null)
                <button type="button" class="btn btn-outline-secondary" onclick="window.print()"><i class="ti ti-printer me-1"></i>{{ __('timetable.print') }}</button>
            @endif
            @if ($canManage)
                <button type="button" class="btn btn-outline-primary" data-bs-target="#copy-modal" data-bs-toggle="modal"><i class="ti ti-copy me-1"></i>{{ __('timetable.copy') }}</button>
                <button type="button" class="btn btn-primary" data-bs-target="#entry-modal" data-bs-toggle="modal"><i class="ti ti-circle-plus me-1"></i>{{ __('timetable.add') }}</button>
            @endif
        </div>
    </div>

    <div class="card d-print-none">
        <div class="card-body">
            <form action="{{ route('timetable.index') }}" method="GET">
                <div class="row align-items-end">
                    @include('timetable._term_filter')
                    <div class="col-md-3 mb-3">
                        <label class="form-label" for="school_class_id">{{ __('timetable.class') }}</label>
                        <select class="form-select @error('school_class_id') is-invalid @enderror" id="school_class_id" name="school_class_id" required>
                            <option value="">{{ __('timetable.choose') }}</option>
                            @foreach ($classes as $option)
                                <option value="{{ $option->id }}" @selected($class?->id === $option->id)>{{ $option->name }}</option>
                            @endforeach
                        </select>
                        @error('school_class_id')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label" for="stream_id">{{ __('timetable.stream') }}</label>
                        <select class="form-select @error('stream_id') is-invalid @enderror" id="stream_id" name="stream_id">
                            <option value="">{{ __('timetable.all_streams') }}</option>
                            @foreach ($streams as $option)
                                <option value="{{ $option->id }}" @selected($stream?->id === $option->id)>{{ $option->schoolClass->name }} – {{ $option->name }}</option>
                            @endforeach
                        </select>
                        @error('stream_id')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3 mb-3"><button type="submit" class="btn btn-primary w-100">{{ __('timetable.show') }}</button></div>
                </div>
            </form>
        </div>
    </div>

    @if ($term === null)
        <div class="alert alert-warning">{{ __('timetable.no_term') }}</div>
    @elseif ($periods->isEmpty())
        <div class="alert alert-warning">
            {{ __('timetable.no_periods') }}
            @can('create', App\Models\TimetablePeriod::class)
                <a href="{{ route('timetable.periods.index') }}">{{ __('timetable.set_periods') }}</a>
            @endcan
        </div>
    @endif

    @if ($class === null)
        <div class="card"><div class="card-body text-muted">{{ __('timetable.pick_class') }}</div></div>
    @elseif ($week !== null)
        <div class="card">
            <div class="card-body p-3">
                <h5 class="mb-3">{{ $class->name }}@if ($stream) – {{ $stream->name }}@endif · {{ $term->academicYear?->name }} {{ $term->name }}</h5>
                @include('timetable._week', ['week' => $week, 'show' => 'class', 'canManage' => $canManage])
            </div>
        </div>
    @endif

    @if ($canManage)
        <x-form-modal id="entry-modal" title="{{ __('timetable.add') }}" action="{{ route('timetable.entries.store') }}" size="modal-lg">
            @include('timetable._entry_fields', ['entry' => null, 'defaults' => ['term_id' => $term?->id, 'school_class_id' => $class?->id, 'stream_id' => $stream?->id, 'weekday' => 1]])
        </x-form-modal>

        <x-form-modal id="copy-modal" title="{{ __('timetable.copy') }}" action="{{ route('timetable.copy') }}" submit="{{ __('timetable.copy_do') }}">
            <p class="text-muted">{{ __('timetable.copy_help') }}</p>
            <div class="mb-3">
                <label class="form-label" for="from_term_id">{{ __('timetable.copy_from') }}</label>
                <select class="form-select @error('from_term_id') is-invalid @enderror" id="from_term_id" name="from_term_id" required>
                    @foreach ($terms as $option)
                        <option value="{{ $option->id }}" @selected((int) old('from_term_id') === $option->id)>{{ $option->academicYear?->name }} – {{ $option->name }}</option>
                    @endforeach
                </select>
                @error('from_term_id')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label" for="to_term_id">{{ __('timetable.copy_to') }}</label>
                <select class="form-select @error('to_term_id') is-invalid @enderror" id="to_term_id" name="to_term_id" required>
                    @foreach ($terms as $option)
                        <option value="{{ $option->id }}" @selected((int) old('to_term_id', $term?->id) === $option->id)>{{ $option->academicYear?->name }} – {{ $option->name }}</option>
                    @endforeach
                </select>
                @error('to_term_id')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
            </div>
        </x-form-modal>

        @include('timetable._entry_script')
    @endif
@endsection
