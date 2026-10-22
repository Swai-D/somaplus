{{-- Variables: subject (Subject or null). --}}
<div class="row">
    <div class="col-md-6">@include('academics._field', ['name' => 'name', 'label' => __('academics.subjects.name'), 'value' => $subject->name ?? ''])</div>
    <div class="col-md-6">@include('academics._field', ['name' => 'code', 'label' => __('academics.subjects.code'), 'value' => $subject->code ?? '', 'required' => false, 'hint' => __('academics.subjects.code_hint')])</div>
</div>
