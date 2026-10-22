{{-- Variables: subject (Subject or null). --}}
@php($editing = isset($subject))
<form action="{{ $editing ? route('academics.subjects.update', $subject) : route('academics.subjects.store') }}" method="POST">
    @csrf
    @if ($editing) @method('PUT') @endif
    @include('academics.subjects._fields', ['subject' => $subject])
    <button type="submit" class="btn btn-primary">{{ __('academics.save') }}</button>
</form>
