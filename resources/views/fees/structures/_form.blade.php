{{-- Variables: structure (FeeStructure or null), terms, classes. Eight item lines are shown; empty lines are ignored. --}}
@php($editing = isset($structure))
<form action="{{ $editing ? route('fees.structures.update', $structure) : route('fees.structures.store') }}" method="POST">
    @csrf
    @if ($editing) @method('PUT') @endif
    @include('fees.structures._fields', ['structure' => $structure, 'terms' => $terms, 'classes' => $classes])
    <button type="submit" class="btn btn-primary">{{ __('fees.save') }}</button>
</form>
