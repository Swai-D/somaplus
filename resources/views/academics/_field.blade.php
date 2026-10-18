{{-- One labelled form field. Variables: name, label, type (default text), value, required (default true), hint. --}}
<div class="mb-3">
    <label class="form-label" for="{{ $name }}">{{ $label }}@if ($required ?? true)<span class="text-danger"> *</span>@endif</label>
    <input class="form-control @error($name) is-invalid @enderror" id="{{ $name }}" name="{{ $name }}" type="{{ $type ?? 'text' }}" value="{{ old($name, $value ?? '') }}" @if ($required ?? true) required @endif>
    @isset($hint)<div class="form-text">{{ $hint }}</div>@endisset
    @error($name)<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
</div>
