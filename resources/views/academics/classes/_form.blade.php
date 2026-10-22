{{-- Variables: class (SchoolClass or null). --}}
@php($editing = isset($class))
<form action="{{ $editing ? route('academics.classes.update', $class) : route('academics.classes.store') }}" method="POST">
    @csrf
    @if ($editing) @method('PUT') @endif
    @include('academics.classes._fields', ['class' => $class])
    <button type="submit" class="btn btn-primary">{{ __('academics.save') }}</button>
</form>
