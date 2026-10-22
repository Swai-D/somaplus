{{-- Variables: exam (Exam or null), years (with terms), scales; hasMarks (bool, when editing). --}}
@php($editing = isset($exam))
<form action="{{ $editing ? route('exams.update', $exam) : route('exams.store') }}" method="POST">
    @csrf
    @if ($editing) @method('PUT') @endif
    @include('exams.exams._fields', ['exam' => $exam, 'years' => $years, 'scales' => $scales, 'hasMarks' => $hasMarks ?? false])
    <button type="submit" class="btn btn-primary">{{ __('exams.save') }}</button>
</form>
