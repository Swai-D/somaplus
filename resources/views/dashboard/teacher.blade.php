@extends('layouts.app')

@section('title', __('dashboard.teacher.title'))

@section('page')
    @php
        $attendance = $figures['attendance'];
        $barColours = ['present' => 'bg-success', 'absent' => 'bg-danger', 'late' => 'bg-warning', 'excused' => 'bg-info'];
    @endphp

    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>{{ __('dashboard.teacher.title') }}</h4>
                <h6>{{ __('dashboard.teacher.subtitle') }}</h6>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4 d-flex">
            <div class="card flex-fill overflow-hidden">
                <div style="height:84px;background:linear-gradient(135deg,#FE9F43,#092C4C)"></div>
                <div class="card-body pt-0 text-center">
                    <span class="avatar avatar-xxl rounded-circle border border-3 border-white bg-light text-dark fw-semibold fs-24" style="margin-top:-40px;width:80px;height:80px">{{ \Illuminate\Support\Str::upper(mb_substr($user->name, 0, 1)) }}</span>
                    <h5 class="mt-2 mb-1">{{ $user->name }}</h5>
                    <p class="fs-13 mb-0">{{ $school->name }}</p>
                    <p class="fs-13 text-muted mb-3">{{ implode(', ', $roles) }}</p>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="border rounded p-2 text-center">
                                <p class="fs-12 text-muted mb-1">{{ __('dashboard.teacher.students') }}</p>
                                <h6 class="fw-bold mb-0">{{ number_format($figures['students']) }}</h6>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2 text-center">
                                <p class="fs-12 text-muted mb-1">{{ __('dashboard.teacher.classes') }}</p>
                                <h6 class="fw-bold mb-0">{{ number_format($figures['classes']) }}</h6>
                            </div>
                        </div>
                    </div>
                    @if ($figures['teaches'] !== null)
                        <div class="border rounded p-2 text-center mt-2">
                            <p class="fs-12 text-muted mb-1">{{ __('dashboard.teacher.teaches') }}</p>
                            <p class="mb-0">
                                @if ($figures['teaches']['subjects'] === [] && $figures['teaches']['classes'] === [])
                                    <span class="text-muted">{{ __('dashboard.teacher.teaches_none') }}</span>
                                @endif
                                @if ($figures['teaches']['subjects'] !== [])
                                    {{ implode(', ', $figures['teaches']['subjects']) }}
                                @endif
                                @if ($figures['teaches']['subjects'] !== [] && $figures['teaches']['classes'] !== [])
                                    <span class="text-muted">·</span>
                                @endif
                                @if ($figures['teaches']['classes'] !== [])
                                    {{ implode(', ', $figures['teaches']['classes']) }}
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 d-flex flex-column">
            <div class="card bg-primary border-0">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <p class="mb-1 text-white">{{ __('dashboard.teacher.marks_entered') }}</p>
                        <h4 class="text-white mb-0">{{ number_format($figures['marks_entered']) }}</h4>
                    </div>
                    <span class="avatar avatar-lg" style="background:rgba(255,255,255,.2)"><i class="ti ti-file-pencil text-white fs-24"></i></span>
                </div>
            </div>
            <div class="card bg-teal border-0">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <p class="mb-1 text-white">{{ __('dashboard.teacher.open_exams') }}</p>
                        <h4 class="text-white mb-0">{{ number_format($figures['open_exams']) }}</h4>
                    </div>
                    <span class="avatar avatar-lg" style="background:rgba(255,255,255,.2)"><i class="ti ti-clipboard-check text-white fs-24"></i></span>
                </div>
            </div>
        </div>

        @if ($attendance !== null)
            <div class="col-xl-4 col-md-6 d-flex">
                <div class="card flex-fill">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">{{ __('dashboard.attendance_today') }}</h5>
                        <a href="{{ route('attendance.index') }}" class="btn btn-sm btn-light">{{ __('menu.take_attendance') }}</a>
                    </div>
                    <div class="card-body">
                        @if ($attendance['total'] === 0)
                            <p class="text-muted mb-0">{{ __('dashboard.attendance_none') }}</p>
                        @else
                            @foreach ($attendance['rows'] as $row)
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-dark">{{ __('attendance.statuses.'.$row['status']) }}</span>
                                        <span class="fw-semibold text-dark">{{ $row['count'] }} ({{ $row['percent'] }}%)</span>
                                    </div>
                                    <div class="progress progress-xs">
                                        <div class="progress-bar {{ $barColours[$row['status']] }}" style="width: {{ $row['percent'] }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if ($figures['library'] !== null)
            <div class="col-xl-4 col-md-6 d-flex">
                <div class="card flex-fill">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">{{ __('dashboard.teacher.library') }}</h5>
                        <a href="{{ route('library.issues.index') }}" class="btn btn-sm btn-light">{{ __('menu.book_list') }}</a>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-4">
                                <div class="border rounded p-2 text-center">
                                    <p class="fs-12 text-muted mb-1">{{ __('dashboard.teacher.library_shelf') }}</p>
                                    <h6 class="fw-bold mb-0">{{ number_format($figures['library']['shelf']) }}</h6>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border rounded p-2 text-center">
                                    <p class="fs-12 text-muted mb-1">{{ __('dashboard.teacher.library_out') }}</p>
                                    <h6 class="fw-bold mb-0">{{ number_format($figures['library']['out']) }}</h6>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border rounded p-2 text-center">
                                    <p class="fs-12 text-muted mb-1">{{ __('dashboard.teacher.library_overdue') }}</p>
                                    <h6 class="fw-bold mb-0 {{ $figures['library']['overdue'] > 0 ? 'text-danger' : '' }}">{{ number_format($figures['library']['overdue']) }}</h6>
                                </div>
                            </div>
                        </div>
                        <p class="fs-13 text-muted mb-0 mt-2">
                            {{ __('dashboard.teacher.library_copies', ['copies' => number_format($figures['library']['copies'])]) }}
                            <a href="{{ route('library.issues.index', ['filter' => 'open']) }}">{{ __('dashboard.teacher.library_open_issues') }}</a>
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="row">
        <div class="col-xl-12 d-flex">
            <div class="card flex-fill">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">{{ __('dashboard.teacher.exams') }}</h5>
                    <a href="{{ route('exams.index') }}" class="btn btn-sm btn-light">{{ __('dashboard.view_all') }}</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ __('dashboard.teacher.exam') }}</th>
                                    <th>{{ __('dashboard.teacher.term') }}</th>
                                    <th>{{ __('dashboard.teacher.max_mark') }}</th>
                                    <th>{{ __('dashboard.teacher.your_marks') }}</th>
                                    <th>{{ __('dashboard.teacher.status') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($figures['exams'] as $exam)
                                    <tr>
                                        <td>{{ $exam->name }}</td>
                                        <td>{{ $exam->term->name }}</td>
                                        <td>{{ $exam->max_mark }}</td>
                                        <td>{{ $exam->getAttribute('my_marks_count') }}</td>
                                        <td>
                                            @if ($exam->isLocked())
                                                <span class="badge badge-danger badge-xs"><i class="ti ti-point-filled me-1"></i>{{ __('dashboard.teacher.locked') }}</span>
                                            @else
                                                <span class="badge badge-success badge-xs"><i class="ti ti-point-filled me-1"></i>{{ __('dashboard.teacher.open') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if (! $exam->isLocked() && $user->can('enterMarks', $exam))
                                                <a href="{{ route('exams.marks.index', $exam) }}" class="btn btn-sm btn-light">{{ __('dashboard.teacher.enter_marks') }}</a>
                                            @elseif ($exam->isLocked() && $user->can('results.view'))
                                                <a href="{{ route('exams.results.index', $exam) }}" class="btn btn-sm btn-light">{{ __('dashboard.teacher.results') }}</a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-muted">{{ __('dashboard.teacher.no_exams') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
