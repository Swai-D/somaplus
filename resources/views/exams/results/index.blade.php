@extends('layouts.app')

@section('title', __('exams.results.title', ['exam' => $exam->name]))

@section('page')
    <div class="mb-3">
        <a href="{{ route('exams.show', $exam) }}" class="text-muted"><i class="ti ti-arrow-left me-1"></i>{{ $exam->name }}</a>
        <h3 class="page-title mt-2">{{ __('exams.results.title', ['exam' => $exam->name]) }}</h3>
        <p class="text-muted mb-0">{{ $exam->academicYear->name }} – {{ $exam->term->name }} · {{ __('exams.exams.max_mark') }}: {{ $exam->max_mark }}</p>
    </div>

    @unless ($exam->isLocked())
        <div class="alert alert-warning">{{ __('exams.results.not_locked') }}</div>
    @else
        <div class="card">
            <div class="card-body">
                <form action="{{ route('exams.results.index', $exam) }}" method="GET">
                    <div class="row align-items-end">
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="school_class_id">{{ __('exams.marks.class') }}</label>
                            <select class="form-select @error('school_class_id') is-invalid @enderror" id="school_class_id" name="school_class_id" required>
                                <option value="">{{ __('exams.choose') }}</option>
                                @foreach ($classes as $option)
                                    <option value="{{ $option->id }}" @selected($class?->id === $option->id)>{{ $option->name }}</option>
                                @endforeach
                            </select>
                            @error('school_class_id')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="stream_id">{{ __('exams.marks.stream') }}</label>
                            <select class="form-select @error('stream_id') is-invalid @enderror" id="stream_id" name="stream_id">
                                <option value="">{{ __('exams.marks.all_streams') }}</option>
                                @foreach ($streams as $option)
                                    <option value="{{ $option->id }}" @selected($stream?->id === $option->id)>{{ $option->schoolClass->name }} – {{ $option->name }}</option>
                                @endforeach
                            </select>
                            @error('stream_id')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3 mb-3"><button type="submit" class="btn btn-primary w-100">{{ __('exams.marks.show') }}</button></div>
                    </div>
                </form>
            </div>
        </div>

        @if ($class !== null)
            @if (count($results) === 0)
                <div class="card"><div class="card-body text-muted">{{ __('exams.results.empty') }}</div></div>
            @else
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-1">{{ $class->name }}@if ($stream) – {{ $stream->name }}@endif</h5>
                        <p class="text-muted fs-13">{{ __('exams.results.rules') }}</p>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead><tr>
                                    <th>{{ __('exams.results.position') }}</th>
                                    <th>{{ __('exams.marks.student') }}</th>
                                    <th>{{ __('exams.results.subjects') }}</th>
                                    <th>{{ __('exams.results.total') }}</th>
                                    <th>{{ __('exams.results.average') }}</th>
                                    <th>{{ __('exams.marks.grade') }}</th>
                                    <th>{{ __('exams.results.stream_position') }}</th>
                                    <th></th>
                                </tr></thead>
                                <tbody>
                                    @foreach ($results as $row)
                                        <tr>
                                            <td class="fw-medium">@if ($row->classPosition !== null){{ $row->classPosition }} / {{ $row->classSize }}@else<span class="text-muted">–</span>@endif</td>
                                            <td class="fw-medium">{{ $row->student->fullName() }}<div class="text-muted fs-13">{{ $row->student->admission_number }}</div></td>
                                            <td>{{ $row->subjects }}@if ($row->absent > 0) <span class="text-muted fs-13">({{ __('exams.results.absent_in', ['count' => $row->absent]) }})</span>@endif</td>
                                            <td>@if ($row->hasScores()){{ $row->totalText() }}@else<span class="text-muted">–</span>@endif</td>
                                            <td>@if ($row->hasScores()){{ $row->averageText() }}@else<span class="text-muted">{{ __('exams.results.no_marks') }}</span>@endif</td>
                                            <td>@if ($row->band)<span class="fw-medium">{{ $row->band->grade }}</span> <span class="text-muted fs-13">{{ $row->band->remark }}</span>@else<span class="text-muted">–</span>@endif</td>
                                            <td>@if ($row->streamPosition !== null){{ $row->streamPosition }} / {{ $row->streamSize }}@else<span class="text-muted">–</span>@endif</td>
                                            <td class="text-end">
                                                @can('viewReport', $row->student)
                                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('students.report', [$row->student, $exam->term_id]) }}">{{ __('exams.results.report') }}</a>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    @endunless
@endsection
