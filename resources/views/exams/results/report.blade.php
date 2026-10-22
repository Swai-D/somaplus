@extends('layouts.app')

@section('title', __('exams.report.title', ['name' => $student->fullName()]))

@section('page')
    <div class="mb-3 d-print-none">
        <a href="{{ route('students.show', $student) }}" class="text-muted"><i class="ti ti-arrow-left me-1"></i>{{ $student->fullName() }}</a>
        <div class="d-flex justify-content-between align-items-start mt-2">
            <h3 class="page-title mb-0">{{ __('exams.report.heading') }}</h3>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">{{ __('exams.report.print') }}</button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @include('partials.letterhead')
            <h4 class="mb-1">{{ $student->fullName() }}</h4>
            <p class="text-muted mb-0">{{ $student->admission_number }} · {{ $term->academicYear->name }} – {{ $term->name }}</p>
        </div>
    </div>

    @include('exams.results._cards', ['exams' => $exams])
@endsection
