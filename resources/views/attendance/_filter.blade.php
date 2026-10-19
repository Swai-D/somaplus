{{-- Class and stream selects. Variables: classes, streams, class, stream. --}}
<div class="col-md-4 mb-3">
    <label class="form-label" for="school_class_id">{{ __('attendance.class') }}</label>
    <select class="form-select @error('school_class_id') is-invalid @enderror" id="school_class_id" name="school_class_id" required>
        <option value="">{{ __('attendance.choose') }}</option>
        @foreach ($classes as $option)
            <option value="{{ $option->id }}" @selected($class?->id === $option->id)>{{ $option->name }}</option>
        @endforeach
    </select>
    @error('school_class_id')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
</div>
<div class="col-md-4 mb-3">
    <label class="form-label" for="stream_id">{{ __('attendance.stream') }}</label>
    <select class="form-select @error('stream_id') is-invalid @enderror" id="stream_id" name="stream_id">
        <option value="">{{ __('attendance.all_streams') }}</option>
        @foreach ($streams as $option)
            <option value="{{ $option->id }}" @selected($stream?->id === $option->id)>{{ $option->schoolClass->name }} – {{ $option->name }}</option>
        @endforeach
    </select>
    @error('stream_id')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
</div>
