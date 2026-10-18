{{-- Variables: subject (Subject or null). --}}
@php($editing = isset($subject))
<form action="{{ $editing ? route('academics.subjects.update', $subject) : route('academics.subjects.store') }}" method="POST">
    @csrf
    @if ($editing) @method('PUT') @endif
    <div class="row">
        <div class="col-md-6">@include('academics._field', ['name' => 'name', 'label' => __('academics.subjects.name'), 'value' => $subject->name ?? ''])</div>
        <div class="col-md-6">@include('academics._field', ['name' => 'code', 'label' => __('academics.subjects.code'), 'value' => $subject->code ?? '', 'required' => false, 'hint' => __('academics.subjects.code_hint')])</div>
    </div>
    <button type="submit" class="btn btn-primary">{{ __('academics.save') }}</button>
</form>
