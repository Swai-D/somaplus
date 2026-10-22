@extends('layouts.app')

@section('title', __('menu.exam_list'))

@section('page')
    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
        <div>
            <h3 class="page-title mb-1">{{ __('menu.exam_list') }}</h3>
            <p class="text-muted mb-0">{{ __('exams.exams.intro') }}</p>
        </div>
        @can('create', App\Models\Exam::class)
            <button type="button" class="btn btn-primary mt-2 mt-md-0" data-bs-target="#exam-modal" data-bs-toggle="modal">
                <i class="ti ti-circle-plus me-1"></i>{{ __('exams.exams.add') }}
            </button>
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

    @can('create', App\Models\Exam::class)
        <x-form-modal id="exam-modal" title="{{ __('exams.exams.add') }}" action="{{ route('exams.store') }}" size="modal-lg">
            @include('exams.exams._fields', ['exam' => null, 'years' => $years, 'scales' => $scales])
        </x-form-modal>
    @endcan
@endsection
