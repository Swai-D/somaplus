{{-- Variables: student (Student or null); when creating also years, classes, streams, currentYearId. --}}
@php($editing = isset($student))
<div class="row">
    <div class="col-md-4">
        @if ($admissionAuto ?? false)
            <div class="mb-3">
                <label class="form-label" for="admission_number_shown">{{ __('students.fields.admission_number') }}</label>
                <input class="form-control" id="admission_number_shown" value="{{ $editing ? $student->admission_number : $admissionNext }}" readonly>
                <div class="form-text">{{ $editing ? __('admission.auto_locked') : __('admission.auto_notice') }}</div>
            </div>
        @else
            @include('academics._field', ['name' => 'admission_number', 'label' => __('students.fields.admission_number'), 'value' => $student->admission_number ?? ''])
        @endif
    </div>
    <div class="col-md-4">@include('academics._field', ['name' => 'first_name', 'label' => __('students.fields.first_name'), 'value' => $student->first_name ?? ''])</div>
    <div class="col-md-4">@include('academics._field', ['name' => 'middle_name', 'label' => __('students.fields.middle_name'), 'value' => $student->middle_name ?? '', 'required' => false])</div>
    <div class="col-md-4">@include('academics._field', ['name' => 'last_name', 'label' => __('students.fields.last_name'), 'value' => $student->last_name ?? ''])</div>
    <div class="col-md-4 mb-3">
        <label class="form-label" for="gender">{{ __('students.fields.gender') }}<span class="text-danger"> *</span></label>
        <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender" required>
            <option value="">{{ __('students.fields.choose') }}</option>
            @foreach (['male', 'female'] as $gender)
                <option value="{{ $gender }}" @selected(old('gender', $student->gender ?? '') === $gender)>{{ __('students.fields.'.$gender) }}</option>
            @endforeach
        </select>
        @error('gender')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">@include('academics._field', ['name' => 'date_of_birth', 'type' => 'date', 'label' => __('students.fields.date_of_birth'), 'value' => isset($student) ? $student->date_of_birth->toDateString() : ''])</div>
    <div class="col-md-4">@include('academics._field', ['name' => 'admitted_on', 'type' => 'date', 'label' => __('students.fields.admitted_on'), 'value' => isset($student) ? $student->admitted_on->toDateString() : now()->toDateString()])</div>
</div>
@unless ($editing)
    <h5 class="mt-2">{{ __('students.create.placement') }}</h5>
    <p class="text-muted">{{ __('students.create.placement_hint') }}</p>
    @include('students._placement')
@endunless
