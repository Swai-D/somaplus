{{--
    The fields of one lesson, in the pop-up and on the edit page.
    Variables: entry (null when adding), defaults (term_id, school_class_id, stream_id), terms, classes,
    streams, subjects, teachers, periods.
--}}
@php($defaults = $defaults ?? [])
@php($value = fn (string $key) => old($key, $entry?->{$key} ?? ($defaults[$key] ?? '')))
<div data-timetable-form>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label" for="f_term_id">{{ __('timetable.term') }}</label>
            <select class="form-select @error('term_id') is-invalid @enderror" id="f_term_id" name="term_id" required>
                @foreach ($terms as $option)
                    <option value="{{ $option->id }}" @selected((int) $value('term_id') === $option->id)>{{ $option->academicYear?->name }} – {{ $option->name }}</option>
                @endforeach
            </select>
            @error('term_id')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label" for="f_subject_id">{{ __('timetable.subject') }}</label>
            <select class="form-select @error('subject_id') is-invalid @enderror" id="f_subject_id" name="subject_id" required>
                <option value="">{{ __('timetable.choose') }}</option>
                @foreach ($subjects as $option)
                    <option value="{{ $option->id }}" @selected((int) $value('subject_id') === $option->id)>{{ $option->name }}</option>
                @endforeach
            </select>
            @error('subject_id')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label" for="f_school_class_id">{{ __('timetable.class') }}</label>
            <select class="form-select @error('school_class_id') is-invalid @enderror" id="f_school_class_id" name="school_class_id" required>
                <option value="">{{ __('timetable.choose') }}</option>
                @foreach ($classes as $option)
                    <option value="{{ $option->id }}" @selected((int) $value('school_class_id') === $option->id)>{{ $option->name }}</option>
                @endforeach
            </select>
            @error('school_class_id')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label" for="f_stream_id">{{ __('timetable.stream') }}</label>
            <select class="form-select @error('stream_id') is-invalid @enderror" id="f_stream_id" name="stream_id">
                <option value="">{{ __('timetable.whole_class') }}</option>
                @foreach ($streams as $option)
                    <option value="{{ $option->id }}" data-class="{{ $option->school_class_id }}" @selected((int) $value('stream_id') === $option->id)>{{ $option->schoolClass->name }} – {{ $option->name }}</option>
                @endforeach
            </select>
            @error('stream_id')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label" for="f_weekday">{{ __('timetable.day') }}</label>
            <select class="form-select @error('weekday') is-invalid @enderror" id="f_weekday" name="weekday" required>
                @foreach (App\Models\TimetableEntry::WEEKDAYS as $day)
                    <option value="{{ $day }}" @selected((int) $value('weekday') === $day)>{{ __('timetable.days.'.$day) }}</option>
                @endforeach
            </select>
            @error('weekday')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label" for="f_timetable_period_id">{{ __('timetable.period') }}</label>
            <select class="form-select @error('timetable_period_id') is-invalid @enderror" id="f_timetable_period_id" name="timetable_period_id" required>
                <option value="">{{ __('timetable.choose') }}</option>
                @foreach ($periods as $option)
                    <option value="{{ $option->id }}" @selected((int) $value('timetable_period_id') === $option->id)>{{ $option->name }} ({{ $option->range() }})</option>
                @endforeach
            </select>
            @error('timetable_period_id')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label" for="f_teacher_id">{{ __('timetable.teacher') }}</label>
            <select class="form-select @error('teacher_id') is-invalid @enderror" id="f_teacher_id" name="teacher_id">
                <option value="">{{ __('timetable.no_teacher') }}</option>
                @foreach ($teachers as $option)
                    <option value="{{ $option->id }}" @selected((int) $value('teacher_id') === $option->id)>{{ $option->fullName() }}</option>
                @endforeach
            </select>
            @error('teacher_id')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label" for="f_room">{{ __('timetable.room') }}</label>
            <input class="form-control @error('room') is-invalid @enderror" id="f_room" name="room" type="text" maxlength="50" value="{{ $value('room') }}">
            @error('room')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
        </div>
    </div>
</div>
