@extends('layouts.app')

@section('title', $guardian->name)

@section('page')
    <div class="mb-3">
        <a href="{{ route('guardians.index') }}" class="text-muted"><i class="ti ti-arrow-left me-1"></i>{{ __('students.guardians.list.back') }}</a>
        <h3 class="page-title mt-2 mb-1">{{ $guardian->name }}</h3>
        <p class="text-muted mb-0">{{ __('students.guardians.show.intro') }}</p>
    </div>

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">{{ __('students.guardians.form.personal') }}</h5></div>
                <div class="card-body">
                    <div class="d-flex justify-content-between border-bottom pb-3 mb-3">
                        <span class="text-muted">{{ __('students.fields.phone') }}</span>
                        <span class="fw-semibold text-dark">{{ $guardian->phone }}</span>
                    </div>
                    <div class="d-flex justify-content-between border-bottom pb-3 mb-3">
                        <span class="text-muted">{{ __('students.fields.email') }}</span>
                        <span class="fw-semibold text-dark">{{ $guardian->email }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">{{ __('students.guardians.list.joined') }}</span>
                        <span class="fw-semibold text-dark">{{ $guardian->created_at->toFormattedDateString() }}</span>
                    </div>
                </div>
                @can('update', $guardian)
                    <div class="card-footer d-flex gap-2">
                        <a href="{{ route('guardians.index', ['edit' => $guardian->id]) }}" class="btn btn-primary"><i class="ti ti-edit me-1"></i>{{ __('students.guardians.list.edit') }}</a>
                        @if ($guardian->students->isEmpty())
                            <form method="POST" action="{{ route('guardians.destroy', $guardian) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger"><i class="ti ti-trash me-1"></i>{{ __('students.guardians.list.delete') }}</button>
                            </form>
                        @endif
                    </div>
                @endcan
            </div>
        </div>

        <div class="col-lg-8">
            @can('managePortal', $guardian)
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">{{ __('family.access.title') }}</h5></div>
                    <div class="card-body">
                        @error('portal')<div class="alert alert-danger">{{ $message }}</div>@enderror
                        <p class="text-muted">{{ __('family.access.hint') }}</p>
                        @if ($guardian->hasPortalAccess())
                            <p class="mb-3"><span class="badge bg-success">{{ __('family.access.has') }}</span></p>
                            <form method="POST" action="{{ route('guardians.portal.destroy', $guardian) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger">{{ __('family.access.revoke') }}</button>
                            </form>
                        @elseif (blank($guardian->email))
                            <p class="text-danger mb-0">{{ __('family.errors.no_email') }}</p>
                        @else
                            <form method="POST" action="{{ route('guardians.portal.store', $guardian) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary">{{ __('family.access.give') }}</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endcan

            <div class="card">
                <div class="card-header"><h5 class="mb-0">{{ __('students.guardians.list.children') }}</h5></div>
                <div class="card-body">
                    @if ($students->isEmpty())
                        <p class="text-muted mb-0">{{ __('students.guardians.show.no_children') }}</p>
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('students.fields.name') }}</th>
                                        <th>{{ __('students.fields.class') }}</th>
                                        <th>{{ __('students.fields.relationship') }}</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($students as $child)
                                        @php($placement = $placements[$child->id] ?? null)
                                        <tr>
                                            <td class="fw-medium"><a href="{{ route('students.show', $child) }}">{{ $child->fullName() }}</a></td>
                                            <td>
                                                @if ($placement)
                                                    {{ $placement->schoolClass->name }}@if ($placement->stream) – {{ $placement->stream->name }}@endif
                                                @else
                                                    <span class="text-muted">{{ __('students.guardians.show.not_placed') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ __('students.relationships.'.$child->pivot->relationship) }}</td>
                                            <td class="text-end">
                                                @can('update', $guardian)
                                                    <form method="POST" action="{{ route('guardians.students.destroy', [$guardian, $child]) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="ti ti-trash me-1"></i>{{ __('students.guardians.list.unlink') }}</button>
                                                    </form>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            @can('create', App\Models\Guardian::class)
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">{{ __('students.guardians.show.add_child') }}</h5></div>
                    <div class="card-body">
                        @if ($candidates->isEmpty())
                            <p class="text-muted mb-0">{{ __('students.guardians.show.no_candidates') }}</p>
                        @else
                            <form method="POST" action="{{ route('guardians.students.store', $guardian) }}" class="row g-3 align-items-end">
                                @csrf
                                <div class="col-md-5">
                                    <label class="form-label" for="student_id">{{ __('students.guardians.show.pupil') }}</label>
                                    <select id="student_id" name="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                                        <option value="">{{ __('students.guardians.show.choose_pupil') }}</option>
                                        @foreach ($candidates as $candidate)
                                            <option value="{{ $candidate->id }}" @selected(old('student_id') == $candidate->id)>{{ $candidate->fullName() }} ({{ $candidate->admission_number }})</option>
                                        @endforeach
                                    </select>
                                    @error('student_id')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label" for="relationship">{{ __('students.fields.relationship') }}</label>
                                    <select id="relationship" name="relationship" class="form-select @error('relationship') is-invalid @enderror" required>
                                        @foreach (['father', 'mother', 'guardian', 'other'] as $relationship)
                                            <option value="{{ $relationship }}" @selected(old('relationship', 'guardian') === $relationship)>{{ __('students.relationships.'.$relationship) }}</option>
                                        @endforeach
                                    </select>
                                    @error('relationship')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-2">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="is_primary" name="is_primary" value="1" @checked(old('is_primary'))>
                                        <label class="form-check-label" for="is_primary">{{ __('students.fields.is_primary') }}</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100"><i class="ti ti-link me-1"></i>{{ __('students.guardians.show.link') }}</button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            @endcan
        </div>
    </div>
@endsection
