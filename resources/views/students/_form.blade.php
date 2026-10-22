{{-- Variables: student (Student or null). --}}
@php($editing = isset($student))
<form action="{{ $editing ? route('students.update', $student) : route('students.store') }}" method="POST">
    @csrf
    @if ($editing) @method('PUT') @endif
    @include('students._fields', ['student' => $student])
    <button type="submit" class="btn btn-primary">{{ __('students.save') }}</button>
</form>
