{{-- Year, class and stream selects. Variables: years, classes, streams, currentYearId, required (bool). --}}
@php($required = $required ?? false)
<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label" for="academic_year_id">{{ __('students.fields.year') }}@if ($required)<span class="text-danger"> *</span>@endif</label>
        <select class="form-select @error('academic_year_id') is-invalid @enderror" id="academic_year_id" name="academic_year_id" @if ($required) required @endif>
            <option value="">{{ __('students.fields.choose') }}</option>
            @foreach ($years as $year)
                <option value="{{ $year->id }}" @selected((string) old('academic_year_id', $currentYearId) === (string) $year->id)>{{ $year->name }}</option>
            @endforeach
        </select>
        @error('academic_year_id')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label" for="school_class_id">{{ __('students.fields.class') }}@if ($required)<span class="text-danger"> *</span>@endif</label>
        <select class="form-select @error('school_class_id') is-invalid @enderror" id="school_class_id" name="school_class_id" @if ($required) required @endif>
            <option value="">{{ __('students.fields.choose') }}</option>
            @foreach ($classes as $class)
                <option value="{{ $class->id }}" @selected((string) old('school_class_id') === (string) $class->id)>{{ $class->name }}</option>
            @endforeach
        </select>
        @error('school_class_id')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label" for="stream_id">{{ __('students.fields.stream') }}</label>
        <select class="form-select @error('stream_id') is-invalid @enderror" id="stream_id" name="stream_id">
            <option value="">{{ __('students.fields.no_stream') }}</option>
            @foreach ($streams as $stream)
                <option value="{{ $stream->id }}" @selected((string) old('stream_id') === (string) $stream->id)>{{ $stream->schoolClass->name }} – {{ $stream->name }}</option>
            @endforeach
        </select>
        @error('stream_id')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
    </div>
</div>
