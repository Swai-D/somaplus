{{-- Class, stream and subject selects. Variables: classes, streams, subjects, class, stream, subject. --}}
<div class="col-md-3 mb-3">
    <label class="form-label" for="school_class_id">{{ __('exams.marks.class') }}</label>
    <select class="form-select @error('school_class_id') is-invalid @enderror" id="school_class_id" name="school_class_id" required>
        <option value="">{{ __('exams.choose') }}</option>
        @foreach ($classes as $option)
            <option value="{{ $option->id }}" @selected($class?->id === $option->id)>{{ $option->name }}</option>
        @endforeach
    </select>
    @error('school_class_id')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
</div>
<div class="col-md-3 mb-3">
    <label class="form-label" for="stream_id">{{ __('exams.marks.stream') }}</label>
    <select class="form-select @error('stream_id') is-invalid @enderror" id="stream_id" name="stream_id">
        <option value="">{{ __('exams.marks.all_streams') }}</option>
        @foreach ($streams as $option)
            <option value="{{ $option->id }}" @selected($stream?->id === $option->id)>{{ $option->schoolClass->name }} – {{ $option->name }}</option>
        @endforeach
    </select>
    @error('stream_id')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
</div>
<div class="col-md-3 mb-3">
    <label class="form-label" for="subject_id">{{ __('exams.marks.subject') }}</label>
    <select class="form-select @error('subject_id') is-invalid @enderror" id="subject_id" name="subject_id" required>
        <option value="">{{ __('exams.choose') }}</option>
        @foreach ($subjects as $option)
            <option value="{{ $option->id }}" @selected($subject?->id === $option->id)>{{ $option->name }}</option>
        @endforeach
    </select>
    @error('subject_id')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
</div>
