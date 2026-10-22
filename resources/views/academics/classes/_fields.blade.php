{{-- Variables: class (SchoolClass or null). --}}
<div class="row">
    <div class="col-md-6">@include('academics._field', ['name' => 'name', 'label' => __('academics.classes.name'), 'value' => $class->name ?? '', 'hint' => __('academics.classes.name_hint')])</div>
    <div class="col-md-6">@include('academics._field', ['name' => 'sort_order', 'label' => __('academics.classes.sort_order'), 'type' => 'number', 'value' => $class->sort_order ?? 0, 'required' => false, 'hint' => __('academics.classes.sort_order_hint')])</div>
</div>
