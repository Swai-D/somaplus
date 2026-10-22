{{-- Variables: scale (GradingScale or null). Ten lines are shown; empty lines are ignored. --}}
@php($editing = isset($scale))
<form action="{{ $editing ? route('grading-scales.update', $scale) : route('grading-scales.store') }}" method="POST">
    @csrf
    @if ($editing) @method('PUT') @endif
    @include('exams.scales._fields', ['scale' => $scale])
    <button type="submit" class="btn btn-primary">{{ __('exams.save') }}</button>
</form>
