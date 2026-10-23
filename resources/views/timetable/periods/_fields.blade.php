{{-- The fields of a lesson slot. Variables: period (null when adding). --}}
<div class="row">
    <div class="col-md-12 mb-3">
        <label class="form-label" for="name">{{ __('timetable.periods.name') }}</label>
        <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" maxlength="50" value="{{ old('name', $period?->name) }}" required>
        @error('name')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label" for="starts_at">{{ __('timetable.periods.starts_at') }}</label>
        <input class="form-control @error('starts_at') is-invalid @enderror" id="starts_at" name="starts_at" type="time" value="{{ old('starts_at', $period?->start()) }}" required>
        @error('starts_at')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label" for="ends_at">{{ __('timetable.periods.ends_at') }}</label>
        <input class="form-control @error('ends_at') is-invalid @enderror" id="ends_at" name="ends_at" type="time" value="{{ old('ends_at', $period?->end()) }}" required>
        @error('ends_at')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-12 mb-3">
        <div class="form-check">
            <input class="form-check-input" id="is_break" name="is_break" type="checkbox" value="1" @checked(old('is_break', $period?->is_break))>
            <label class="form-check-label" for="is_break">{{ __('timetable.periods.is_break') }}</label>
        </div>
        <div class="text-muted fs-13">{{ __('timetable.periods.is_break_help') }}</div>
        @error('is_break')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
    </div>
</div>
