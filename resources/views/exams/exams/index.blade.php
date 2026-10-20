@extends('layouts.app')

@section('title', __('menu.exam_list'))

@section('page')
    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
        <div>
            <h3 class="page-title mb-1">{{ __('menu.exam_list') }}</h3>
            <p class="text-muted mb-0">{{ __('exams.exams.intro') }}</p>
        </div>
        @can('create', App\Models\Exam::class)
            <a href="{{ route('exams.create') }}" class="btn btn-primary mt-2 mt-md-0"><i class="ti ti-plus me-1"></i>{{ __('exams.exams.add') }}</a>
        @endcan
    </div>
    <div class="card">
        <div class="card-body">
            @if ($exams->isEmpty())
                <p class="text-muted mb-0">{{ __('exams.exams.empty') }}</p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr>
                            <th>{{ __('exams.exams.name') }}</th><th>{{ __('exams.exams.term') }}</th><th>{{ __('exams.exams.max_mark') }}</th><th>{{ __('exams.exams.scale') }}</th><th></th>
                        </tr></thead>
                        <tbody>
                            @foreach ($exams as $exam)
                                <tr>
                                    <td class="fw-medium">{{ $exam->name }}@if ($exam->isLocked()) <span class="badge bg-secondary ms-1">{{ __('exams.exams.locked') }}</span>@endif</td>
                                    <td>{{ $exam->academicYear->name }} – {{ $exam->term->name }}</td>
                                    <td>{{ $exam->max_mark }}</td>
                                    <td>{{ $exam->gradingScale->name }}</td>
                                    <td class="text-end"><a href="{{ route('exams.show', $exam) }}" class="btn btn-sm btn-outline-secondary">{{ __('academics.open') }}</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
