{{-- A term select. Variables: terms, term. --}}
<div class="col-md-3 mb-3">
    <label class="form-label" for="term_id">{{ __('timetable.term') }}</label>
    <select class="form-select @error('term_id') is-invalid @enderror" id="term_id" name="term_id">
        @foreach ($terms as $option)
            <option value="{{ $option->id }}" @selected($term?->id === $option->id)>{{ $option->academicYear?->name }} – {{ $option->name }}</option>
        @endforeach
    </select>
    @error('term_id')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
</div>
