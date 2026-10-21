@extends('layouts.app')

@section('title', $student->fullName())

@section('page')
    <div class="mb-3">
        <a href="{{ route('students.index') }}" class="text-muted"><i class="ti ti-arrow-left me-1"></i>{{ __('menu.student_list') }}</a>
        <div class="d-flex justify-content-between align-items-start mt-2">
            <h3 class="page-title mb-0">{{ $student->fullName() }}</h3>
            <div class="d-flex gap-2">
                @if ($student->isActive())
                    @can('printIdCard', App\Models\Student::class)
                        <a href="{{ route('students.id-card', $student) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="ti ti-id-badge-2 me-1"></i>{{ __('idcard.button') }}</a>
                    @endcan
                @endif
                @can('update', $student)
                    <a href="{{ route('students.edit', $student) }}" class="btn btn-sm btn-outline-secondary">{{ __('students.edit') }}</a>
                @endcan
            </div>
        </div>
    </div>

    @unless ($student->isActive())
        <div class="alert alert-warning">
            {{ __('students.show.left_notice', ['date' => $student->left_on?->toDateString()]) }}
            @if ($student->left_reason) <br>{{ $student->left_reason }} @endif
        </div>
    @endunless

    <div class="card">
        <div class="card-body">
            <h5 class="mb-3">{{ __('students.show.details') }}</h5>
            <dl class="row mb-0">
                <dt class="col-sm-4">{{ __('students.fields.admission_number') }}</dt><dd class="col-sm-8">{{ $student->admission_number }}</dd>
                <dt class="col-sm-4">{{ __('students.fields.gender') }}</dt><dd class="col-sm-8">{{ __('students.fields.'.$student->gender) }}</dd>
                <dt class="col-sm-4">{{ __('students.fields.date_of_birth') }}</dt><dd class="col-sm-8">{{ $student->date_of_birth->toDateString() }}</dd>
                <dt class="col-sm-4">{{ __('students.fields.admitted_on') }}</dt><dd class="col-sm-8">{{ $student->admitted_on->toDateString() }}</dd>
            </dl>
        </div>
    </div>

    @can('viewReport', $student)
        <div class="card">
            <div class="card-body">
                <h5 class="mb-1">{{ __('students.show.reports') }}</h5>
                @forelse ($reportTerms as $reportTerm)
                    <a class="btn btn-sm btn-outline-secondary me-1 mt-2" href="{{ route('students.report', [$student, $reportTerm]) }}">{{ $reportTerm->academicYear->name }} – {{ $reportTerm->name }}</a>
                @empty
                    <p class="text-muted mb-0">{{ __('students.show.no_reports') }}</p>
                @endforelse
            </div>
        </div>
    @endcan

    <div class="card">
        <div class="card-body">
            <h5 class="mb-1">{{ __('students.show.history') }}</h5>
            <p class="text-muted">{{ __('students.show.history_hint') }}</p>
            @if ($history->isEmpty())
                <p class="text-muted mb-0">{{ __('students.show.no_history') }}</p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr>
                            <th>{{ __('students.fields.year') }}</th><th>{{ __('students.fields.class') }}</th><th>{{ __('students.fields.stream') }}</th><th>{{ __('students.fields.since') }}</th>
                        </tr></thead>
                        <tbody>
                            @foreach ($history as $row)
                                <tr>
                                    <td>{{ $row->academicYear->name }}</td>
                                    <td>{{ $row->schoolClass->name }}</td>
                                    <td>{{ $row->stream?->name }}</td>
                                    <td>{{ $row->enrolled_on->toDateString() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    @can('update', $student)
        @if ($student->isActive())
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-1">{{ __('students.show.place') }}</h5>
                    <p class="text-muted">{{ __('students.show.place_hint') }}</p>
                    <form action="{{ route('students.enrollments.store', $student) }}" method="POST">
                        @csrf
                        @include('students._placement', ['required' => true])
                        @include('academics._field', ['name' => 'enrolled_on', 'type' => 'date', 'label' => __('students.fields.since'), 'value' => now()->toDateString()])
                        <button type="submit" class="btn btn-primary">{{ __('students.save') }}</button>
                    </form>
                </div>
            </div>
        @endif
    @endcan

    <div class="card">
        <div class="card-body">
            <h5 class="mb-3">{{ __('students.show.guardians') }}</h5>
            @if ($guardians->isEmpty())
                <p class="text-muted">{{ __('students.show.no_guardians') }}</p>
            @else
                <div class="table-responsive mb-3">
                    <table class="table align-middle mb-0">
                        <thead><tr>
                            <th>{{ __('students.fields.name') }}</th><th>{{ __('students.fields.relationship') }}</th><th>{{ __('students.fields.phone') }}</th><th>{{ __('students.fields.email') }}</th><th></th>
                        </tr></thead>
                        <tbody>
                            @foreach ($guardians as $guardian)
                                <tr>
                                    <td class="fw-medium">{{ $guardian->name }}@if ($guardian->pivot->is_primary) <span class="badge bg-primary ms-1">{{ __('students.fields.is_primary') }}</span>@endif</td>
                                    <td>{{ __('students.relationships.'.$guardian->pivot->relationship) }}</td>
                                    <td>{{ $guardian->phone }}</td>
                                    <td>{{ $guardian->email }}</td>
                                    <td class="text-end">
                                        @can('update', $student)
                                            <form action="{{ route('students.guardians.destroy', [$student, $guardian]) }}" method="POST" class="d-inline" onsubmit="return confirm(@js(__('students.confirm_remove')))">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('students.remove') }}</button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @can('update', $student)
                <h6 class="mt-3">{{ __('students.show.add_guardian') }}</h6>
                <form action="{{ route('students.guardians.store', $student) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">@include('academics._field', ['name' => 'name', 'label' => __('students.fields.name'), 'value' => ''])</div>
                        <div class="col-md-6">@include('academics._field', ['name' => 'phone', 'type' => 'tel', 'label' => __('students.fields.phone'), 'value' => '', 'hint' => __('students.fields.phone_hint')])</div>
                        <div class="col-md-6">@include('academics._field', ['name' => 'email', 'type' => 'email', 'label' => __('students.fields.email'), 'value' => '', 'required' => false])</div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="relationship">{{ __('students.fields.relationship') }}<span class="text-danger"> *</span></label>
                            <select class="form-select @error('relationship') is-invalid @enderror" id="relationship" name="relationship" required>
                                @foreach (['father', 'mother', 'guardian', 'other'] as $kind)
                                    <option value="{{ $kind }}" @selected(old('relationship') === $kind)>{{ __('students.relationships.'.$kind) }}</option>
                                @endforeach
                            </select>
                            @error('relationship')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="form-check mb-3">
                        <input type="hidden" name="is_primary" value="0">
                        <input class="form-check-input" type="checkbox" id="is_primary" name="is_primary" value="1" @checked(old('is_primary'))>
                        <label class="form-check-label" for="is_primary">{{ __('students.fields.is_primary') }}</label>
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('students.save') }}</button>
                </form>
            @endcan
        </div>
    </div>

    @can('update', $student)
        <div class="card">
            <div class="card-body">
                @if ($student->isActive())
                    <h5 class="mb-1">{{ __('students.show.leave') }}</h5>
                    <p class="text-muted">{{ __('students.show.leave_hint') }}</p>
                    <form action="{{ route('students.leave', $student) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">@include('academics._field', ['name' => 'left_on', 'type' => 'date', 'label' => __('students.fields.left_on'), 'value' => now()->toDateString()])</div>
                            <div class="col-md-8">@include('academics._field', ['name' => 'left_reason', 'label' => __('students.fields.left_reason'), 'value' => '', 'required' => false])</div>
                        </div>
                        <button type="submit" class="btn btn-outline-danger">{{ __('students.show.leave') }}</button>
                    </form>
                @else
                    <form action="{{ route('students.reinstate', $student) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary">{{ __('students.show.reinstate') }}</button>
                    </form>
                @endif
            </div>
        </div>
    @endcan
@endsection
