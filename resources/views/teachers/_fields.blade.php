{{-- The fields of a teacher, used by the pop-up of the list and by the page that changes a teacher.
     Variables: teacher (Teacher or null), subjects, classes. --}}
@php($editing = isset($teacher))
@php($chosenSubjects = old('subjects', $teacher?->subjects->pluck('id')->all() ?? []))
@php($chosenClasses = old('classes', $teacher?->classes->pluck('id')->all() ?? []))

<h5 class="mb-3">{{ __('teachers.form.account') }}</h5>
<p class="text-muted fs-13">{{ __('teachers.form.account_hint') }}</p>

<div class="row">
    <div class="col-md-6">
        @include('academics._field', ['name' => 'name', 'label' => __('teachers.form.name'), 'value' => $teacher?->user?->name ?? ''])
    </div>
    <div class="col-md-6">
        @include('academics._field', ['name' => 'email', 'label' => __('teachers.form.email'), 'type' => 'email', 'value' => $teacher?->user?->email ?? ''])
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label" for="password">{{ __('teachers.form.password') }}@unless ($editing)<span class="text-danger"> *</span>@endunless</label>
            <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" autocomplete="new-password" @unless ($editing) required @endunless>
            <div class="form-text">{{ $editing ? __('teachers.form.password_keep') : __('teachers.form.password_new') }}</div>
            @error('password')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<h5 class="mt-2 mb-3">{{ __('teachers.form.personal') }}</h5>

<div class="row">
    <div class="col-md-6">
        @include('academics._field', ['name' => 'phone', 'label' => __('teachers.form.phone'), 'type' => 'tel', 'value' => $teacher->phone ?? ''])
    </div>
    <div class="col-md-6">
        @include('academics._field', ['name' => 'staff_number', 'label' => __('teachers.form.staff_number'), 'value' => $teacher->staff_number ?? '', 'required' => false, 'hint' => __('teachers.form.staff_number_hint')])
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label" for="gender">{{ __('teachers.form.gender') }}</label>
        <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
            <option value=""></option>
            @foreach (['male', 'female'] as $gender)
                <option value="{{ $gender }}" @selected(old('gender', $teacher->gender ?? '') === $gender)>{{ __('teachers.genders.'.$gender) }}</option>
            @endforeach
        </select>
        @error('gender')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        @include('academics._field', ['name' => 'date_of_birth', 'label' => __('teachers.form.date_of_birth'), 'type' => 'date', 'required' => false, 'value' => isset($teacher) && $teacher->date_of_birth ? $teacher->date_of_birth->toDateString() : ''])
    </div>
    <div class="col-md-6">
        @include('academics._field', ['name' => 'address', 'label' => __('teachers.form.address'), 'required' => false, 'value' => $teacher->address ?? ''])
    </div>
    <div class="col-md-6">
        @include('academics._field', ['name' => 'qualification', 'label' => __('teachers.form.qualification'), 'required' => false, 'value' => $teacher->qualification ?? ''])
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label" for="contract_type">{{ __('teachers.form.contract_type') }}</label>
        <select class="form-select @error('contract_type') is-invalid @enderror" id="contract_type" name="contract_type">
            <option value=""></option>
            @foreach (['permanent', 'contractual', 'part_time', 'hourly'] as $contract)
                <option value="{{ $contract }}" @selected(old('contract_type', $teacher->contract_type ?? '') === $contract)>{{ __('teachers.contract_types.'.$contract) }}</option>
            @endforeach
        </select>
        @error('contract_type')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label" for="shift">{{ __('teachers.form.shift') }}</label>
        <select class="form-select @error('shift') is-invalid @enderror" id="shift" name="shift">
            <option value=""></option>
            @foreach (['day', 'evening', 'night'] as $shift)
                <option value="{{ $shift }}" @selected(old('shift', $teacher->shift ?? '') === $shift)>{{ __('teachers.shifts.'.$shift) }}</option>
            @endforeach
        </select>
        @error('shift')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        @include('academics._field', ['name' => 'joined_on', 'label' => __('teachers.form.joined_on'), 'type' => 'date', 'value' => isset($teacher) ? $teacher->joined_on->toDateString() : now()->toDateString()])
    </div>
    <div class="col-12">
        <div class="mb-3">
            <label class="form-label" for="notes">{{ __('teachers.form.notes') }}</label>
            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2" maxlength="1000">{{ old('notes', $teacher->notes ?? '') }}</textarea>
            @error('notes')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<h5 class="mt-2 mb-1">{{ __('teachers.form.teaching') }}</h5>

<div class="row">
    <div class="col-md-6">
        <p class="fw-semibold mb-2">{{ __('teachers.form.subjects') }}</p>
        @if ($subjects->isEmpty())
            <p class="text-muted fs-13">{{ __('teachers.form.no_subjects') }}</p>
        @else
            <div class="border rounded p-3">
                @foreach ($subjects as $subject)
                    <div class="form-check mb-1">
                        <input class="form-check-input" type="checkbox" name="subjects[]" value="{{ $subject->id }}" id="teacher-subject-{{ $subject->id }}" @checked(in_array($subject->id, (array) $chosenSubjects, true))>
                        <label class="form-check-label" for="teacher-subject-{{ $subject->id }}">{{ $subject->name }}</label>
                    </div>
                @endforeach
            </div>
        @endif
        @error('subjects')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <p class="fw-semibold mb-2">{{ __('teachers.form.classes') }}</p>
        @if ($classes->isEmpty())
            <p class="text-muted fs-13">{{ __('teachers.form.no_classes') }}</p>
        @else
            <div class="border rounded p-3">
                @foreach ($classes as $class)
                    <div class="form-check mb-1">
                        <input class="form-check-input" type="checkbox" name="classes[]" value="{{ $class->id }}" id="teacher-class-{{ $class->id }}" @checked(in_array($class->id, (array) $chosenClasses, true))>
                        <label class="form-check-label" for="teacher-class-{{ $class->id }}">{{ $class->name }}</label>
                    </div>
                @endforeach
            </div>
        @endif
        @error('classes')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
    </div>
</div>
