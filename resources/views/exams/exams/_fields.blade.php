{{-- Variables: exam (Exam or null), years (with terms), scales; hasMarks (bool, when editing). --}}
@php($editing = isset($exam))
@php($frozen = $editing && ($hasMarks ?? false))
@error('exam')<div class="alert alert-danger">{{ $message }}</div>@enderror
@if ($frozen)<div class="alert alert-info">{{ __('exams.exams.has_marks') }}</div>@endif
<div class="row">
    <div class="col-md-6">@include('academics._field', ['name' => 'name', 'label' => __('exams.exams.name'), 'value' => $exam->name ?? '', 'hint' => __('exams.exams.name_hint')])</div>
    <div class="col-md-6">@include('academics._field', ['name' => 'max_mark', 'type' => 'number', 'label' => __('exams.exams.max_mark'), 'value' => $exam->max_mark ?? 100, 'hint' => __('exams.exams.max_hint')])</div>
    <div class="col-md-6 mb-3">
        <label class="form-label" for="term_id">{{ __('exams.exams.term') }}<span class="text-danger"> *</span></label>
        <select class="form-select @error('term_id') is-invalid @enderror" id="term_id" name="term_id" required @disabled($frozen)>
            <option value="">{{ __('exams.choose') }}</option>
            @foreach ($years as $year)
                <optgroup label="{{ $year->name }}">
                    @foreach ($year->terms as $term)
                        <option value="{{ $term->id }}" @selected((string) old('term_id', $exam->term_id ?? '') === (string) $term->id)>{{ $year->name }} – {{ $term->name }}</option>
                    @endforeach
                </optgroup>
            @endforeach
        </select>
        @error('term_id')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label" for="grading_scale_id">{{ __('exams.exams.scale') }}<span class="text-danger"> *</span></label>
        <select class="form-select @error('grading_scale_id') is-invalid @enderror" id="grading_scale_id" name="grading_scale_id" required @disabled($frozen)>
            <option value="">{{ __('exams.choose') }}</option>
            @foreach ($scales as $scale)
                <option value="{{ $scale->id }}" @selected((string) old('grading_scale_id', $exam->grading_scale_id ?? '') === (string) $scale->id)>{{ $scale->name }}</option>
            @endforeach
        </select>
        <div class="form-text">{{ __('exams.exams.scale_hint') }}</div>
        @error('grading_scale_id')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
    </div>
</div>
@if ($frozen)
    {{-- Disabled fields are not sent, so the unchanged values are sent as they are. --}}
    <input type="hidden" name="term_id" value="{{ $exam->term_id }}">
    <input type="hidden" name="grading_scale_id" value="{{ $exam->grading_scale_id }}">
@endif
