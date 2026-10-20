@extends('layouts.app')

@section('title', $exam->name)

@section('page')
    <div class="mb-3">
        <a href="{{ route('exams.index') }}" class="text-muted"><i class="ti ti-arrow-left me-1"></i>{{ __('menu.exam_list') }}</a>
        <div class="d-flex justify-content-between align-items-start mt-2">
            <h3 class="page-title mb-0">{{ $exam->name }}@if ($exam->isLocked()) <span class="badge bg-secondary ms-1">{{ __('exams.exams.locked') }}</span>@endif</h3>
            @can('update', $exam)
                @unless ($exam->isLocked())
                    <a href="{{ route('exams.edit', $exam) }}" class="btn btn-sm btn-outline-secondary">{{ __('exams.edit') }}</a>
                @endunless
            @endcan
        </div>
    </div>

    @error('delete')<div class="alert alert-danger">{{ $message }}</div>@enderror

    <div class="card">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-4">{{ __('exams.exams.term') }}</dt><dd class="col-sm-8">{{ $exam->academicYear->name }} – {{ $exam->term->name }}</dd>
                <dt class="col-sm-4">{{ __('exams.exams.max_mark') }}</dt><dd class="col-sm-8">{{ $exam->max_mark }}</dd>
                <dt class="col-sm-4">{{ __('exams.exams.scale') }}</dt><dd class="col-sm-8">{{ $exam->gradingScale->name }}</dd>
                <dt class="col-sm-4">{{ __('exams.exams.marks_count') }}</dt><dd class="col-sm-8">{{ $marksCount }}</dd>
            </dl>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="mb-3">{{ __('exams.marks.open') }}</h5>
            <form action="{{ route('exams.marks.index', $exam) }}" method="GET">
                <div class="row align-items-end">
                    @include('exams.marks._filter', ['class' => null, 'stream' => null, 'subject' => null])
                    <div class="col-md-3 mb-3"><button type="submit" class="btn btn-primary w-100">{{ __('exams.marks.show') }}</button></div>
                </div>
            </form>
        </div>
    </div>

    @can('viewResults', $exam)
        <div class="card">
            <div class="card-body">
                <h5 class="mb-2">{{ __('exams.results.heading') }}</h5>
                @if ($exam->isLocked())
                    <a href="{{ route('exams.results.index', $exam) }}" class="btn btn-primary">{{ __('exams.results.open') }}</a>
                @else
                    <p class="text-muted mb-0">{{ __('exams.results.not_locked') }}</p>
                @endif
            </div>
        </div>
    @endcan

    <div class="card">
        <div class="card-body">
            <h5 class="mb-3">{{ __('exams.exams.scale') }}: {{ $exam->gradingScale->name }}</h5>
            @include('exams.scales._bands', ['bands' => $exam->gradingScale->bands])
        </div>
    </div>

    @can('update', $exam)
        <div class="card">
            <div class="card-body">
                @if ($exam->isLocked())
                    <p class="text-muted">{{ __('exams.exams.locked_hint') }}</p>
                    <form action="{{ route('exams.unlock', $exam) }}" method="POST" class="d-inline" onsubmit="return confirm(@js(__('exams.exams.confirm_unlock')))">@csrf
                        <button type="submit" class="btn btn-outline-secondary">{{ __('exams.exams.unlock') }}</button>
                    </form>
                @else
                    <p class="text-muted">{{ __('exams.exams.lock_hint') }}</p>
                    <form action="{{ route('exams.lock', $exam) }}" method="POST" class="d-inline" onsubmit="return confirm(@js(__('exams.exams.confirm_lock')))">@csrf
                        <button type="submit" class="btn btn-primary">{{ __('exams.exams.lock') }}</button>
                    </form>
                @endif
                @can('delete', $exam)
                    @if (! $exam->isLocked() && $marksCount === 0)
                        @include('academics._delete', ['action' => route('exams.destroy', $exam)])
                    @endif
                @endcan
            </div>
        </div>
    @endcan
@endsection
