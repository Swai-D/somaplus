@extends('layouts.app')

@section('title', $teacher->fullName())

@section('page')
    <div class="mb-3">
        <a href="{{ route('teachers.index') }}" class="text-muted"><i class="ti ti-arrow-left me-1"></i>{{ __('menu.teacher_list') }}</a>
        <div class="d-flex justify-content-between align-items-start mt-2 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <span class="avatar avatar-lg bg-light text-dark fw-semibold">{{ mb_strtoupper(mb_substr($teacher->fullName(), 0, 1)) }}</span>
                <div>
                    <h3 class="page-title mb-1">{{ $teacher->fullName() }}</h3>
                    <span class="badge {{ $teacher->isActive() ? 'bg-success' : 'bg-secondary' }}">
                        <i class="ti ti-point-filled me-1"></i>{{ $teacher->isActive() ? __('teachers.list.active') : __('teachers.list.inactive') }}
                    </span>
                    @if ($teacher->staff_number)
                        <span class="text-muted ms-2">{{ __('teachers.list.staff_number') }}: {{ $teacher->staff_number }}</span>
                    @endif
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                @can('update', $teacher)
                    <a href="{{ route('teachers.edit', $teacher) }}" class="btn btn-sm btn-outline-secondary"><i class="ti ti-edit me-1"></i>{{ __('teachers.show.edit') }}</a>
                    <form action="{{ route('teachers.status', $teacher) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                            <i class="ti ti-power-off me-1"></i>{{ $teacher->isActive() ? __('teachers.show.deactivate') : __('teachers.show.activate') }}
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </div>

    @unless ($teacher->isActive())
        <div class="alert alert-warning">{{ __('teachers.show.out_of_duty') }}</div>
    @endunless

    <div class="row">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">{{ __('teachers.show.details') }}</h5>
                    <dl class="row mb-0">
                        <dt class="col-sm-5">{{ __('teachers.form.phone') }}</dt><dd class="col-sm-7">{{ $teacher->phone }}</dd>
                        <dt class="col-sm-5">{{ __('teachers.form.staff_number') }}</dt><dd class="col-sm-7">{{ $teacher->staff_number ?? '—' }}</dd>
                        <dt class="col-sm-5">{{ __('teachers.form.gender') }}</dt><dd class="col-sm-7">{{ $teacher->gender ? __('teachers.genders.'.$teacher->gender) : '—' }}</dd>
                        <dt class="col-sm-5">{{ __('teachers.form.date_of_birth') }}</dt><dd class="col-sm-7">{{ $teacher->date_of_birth?->toDateString() ?? '—' }}</dd>
                        <dt class="col-sm-5">{{ __('teachers.form.qualification') }}</dt><dd class="col-sm-7">{{ $teacher->qualification ?? '—' }}</dd>
                        <dt class="col-sm-5">{{ __('teachers.form.contract_type') }}</dt><dd class="col-sm-7">{{ $teacher->contract_type ? __('teachers.contract_types.'.$teacher->contract_type) : '—' }}</dd>
                        <dt class="col-sm-5">{{ __('teachers.form.shift') }}</dt><dd class="col-sm-7">{{ $teacher->shift ? __('teachers.shifts.'.$teacher->shift) : '—' }}</dd>
                        <dt class="col-sm-5">{{ __('teachers.form.joined_on') }}</dt><dd class="col-sm-7">{{ $teacher->joined_on->toDateString() }}</dd>
                        <dt class="col-sm-5">{{ __('teachers.form.address') }}</dt><dd class="col-sm-7">{{ $teacher->address ?? '—' }}</dd>
                        @if ($teacher->notes)
                            <dt class="col-sm-5">{{ __('teachers.form.notes') }}</dt><dd class="col-sm-7">{{ $teacher->notes }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">{{ __('teachers.show.teaching') }}</h5>
                    <dl class="row mb-0">
                        <dt class="col-sm-5">{{ __('teachers.form.subjects') }}</dt>
                        <dd class="col-sm-7">
                            @if ($teacher->subjects->isEmpty())
                                <span class="text-muted">{{ __('teachers.show.no_subjects') }}</span>
                            @else
                                {{ $teacher->subjects->pluck('name')->join(', ') }}
                            @endif
                        </dd>
                        <dt class="col-sm-5">{{ __('teachers.form.classes') }}</dt>
                        <dd class="col-sm-7">
                            @if ($teacher->classes->isEmpty())
                                <span class="text-muted">{{ __('teachers.show.no_classes') }}</span>
                            @else
                                {{ $teacher->classes->pluck('name')->join(', ') }}
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">{{ __('teachers.show.account') }}</h5>
                    <dl class="row mb-0">
                        <dt class="col-sm-5">{{ __('teachers.form.email') }}</dt><dd class="col-sm-7">{{ $teacher->user?->email ?? '—' }}</dd>
                        <dt class="col-sm-5">{{ __('teachers.form.name') }}</dt><dd class="col-sm-7">{{ $teacher->fullName() }}</dd>
                        <dt class="col-sm-5">{{ __('teachers.list.status') }}</dt>
                        <dd class="col-sm-7">{{ $teacher->isActive() ? __('teachers.list.active') : __('teachers.list.inactive') }}</dd>
                    </dl>
                </div>
            </div>

            @can('delete', $teacher)
                <div class="card border-danger">
                    <div class="card-body">
                        <h5 class="mb-2 text-danger">{{ __('teachers.show.delete') }}</h5>
                        <p class="text-muted fs-13">{{ __('teachers.show.delete_hint') }}</p>
                        <form action="{{ route('teachers.destroy', $teacher) }}" method="POST" onsubmit="return confirm(@js(__('teachers.show.delete_confirm')))">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm"><i class="ti ti-trash me-1"></i>{{ __('teachers.list.delete') }}</button>
                        </form>
                    </div>
                </div>
            @endcan
        </div>
    </div>
@endsection
