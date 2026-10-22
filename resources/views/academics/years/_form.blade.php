{{-- Variables: year (AcademicYear or null). --}}
@php($editing = isset($year))
<form action="{{ $editing ? route('academics.years.update', $year) : route('academics.years.store') }}" method="POST">
    @csrf
    @if ($editing) @method('PUT') @endif
    @include('academics.years._fields', ['year' => $year])
    <button type="submit" class="btn btn-primary">{{ __('academics.save') }}</button>
</form>
