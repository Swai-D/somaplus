{{-- Variables: year (AcademicYear or null). --}}
<div class="row">
    <div class="col-md-4">@include('academics._field', ['name' => 'name', 'label' => __('academics.years.name'), 'value' => $year->name ?? '', 'hint' => __('academics.years.name_hint')])</div>
    <div class="col-md-4">@include('academics._field', ['name' => 'starts_on', 'label' => __('academics.starts_on'), 'type' => 'date', 'value' => isset($year) ? $year->starts_on->toDateString() : ''])</div>
    <div class="col-md-4">@include('academics._field', ['name' => 'ends_on', 'label' => __('academics.ends_on'), 'type' => 'date', 'value' => isset($year) ? $year->ends_on->toDateString() : ''])</div>
</div>
<div class="form-check mb-3">
    <input class="form-check-input" type="checkbox" name="is_current" id="is_current" value="1" @checked(old('is_current', $year->is_current ?? false))>
    <label class="form-check-label" for="is_current">{{ __('academics.years.is_current') }}</label>
    <div class="form-text">{{ __('academics.years.is_current_hint') }}</div>
</div>
