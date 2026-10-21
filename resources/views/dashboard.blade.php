@extends('layouts.app')

@section('title', __('menu.dashboard'))

@php
    $figures = $summary['figures'] ?? null;
    $fees = $figures['fees'] ?? null;
    $attendance = $figures['attendance'] ?? null;
    $attendanceTrend = $figures['attendance_trend'] ?? null;
    $students = $figures['students'] ?? null;
    $people = $figures['people'] ?? null;
    $classBreakdown = $figures['class_breakdown'] ?? null;
    $recentStudents = $figures['recent_students'] ?? null;
    $recentPayments = $figures['recent_payments'] ?? null;
    $topStudents = $figures['top_students'] ?? null;

    // One coloured tile per figure the person may see, in the template's order.
    $tiles = [];
    if ($students !== null) {
        $tiles[] = ['bg-primary', 'ti-school', __('dashboard.students'), number_format($students['active']), $students['new_this_month'] > 0 ? __('dashboard.new_this_month', ['count' => $students['new_this_month']]) : null];
    }
    if (($figures['guardians'] ?? null) !== null) {
        $tiles[] = ['bg-teal', 'ti-users-group', __('dashboard.guardians'), number_format($figures['guardians']), null];
    }
    if ($people !== null) {
        $tiles[] = ['bg-secondary', 'ti-user-check', __('dashboard.teachers'), number_format($people['teachers']), __('dashboard.teacher_accounts')];
    }
    if (($figures['classes'] ?? null) !== null) {
        $tiles[] = ['bg-info', 'ti-books', __('dashboard.classes'), number_format($figures['classes']), null];
    }
    if ($fees !== null) {
        $tiles[] = ['bg-purple', 'ti-coin', __('dashboard.fees_invoiced'), $fees['invoiced']->format(), $fees['year']];
        $tiles[] = ['bg-info', 'ti-cash', __('dashboard.fees_collected'), $fees['collected']->format(), $fees['year']];
        $tiles[] = ['bg-danger', 'ti-wallet', __('dashboard.fees_outstanding'), $fees['outstanding']->format(), $fees['year']];
    }

    $barColours = ['present' => 'bg-success', 'absent' => 'bg-danger', 'late' => 'bg-warning', 'excused' => 'bg-info'];

    // The people of the school, for the doughnut of the template.
    $peopleSeries = $people !== null ? [$people['students'], $people['teachers'], $people['staff']] : [];
    $peopleLabels = [__('dashboard.people_students'), __('dashboard.people_teachers'), __('dashboard.people_staff')];
@endphp

@section('page')
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>{{ __('dashboard.welcome', ['name' => $user->name]) }}</h4>
                <h6>{{ $summary['scope'] === 'school' ? $summary['school'] : __('dashboard.platform') }}</h6>
            </div>
        </div>
    </div>

    @if ($summary['scope'] === 'platform')
        <div class="row">
            <div class="col-xl-4 col-md-6">
                <div class="card bg-primary border-0">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1 text-white">{{ __('dashboard.schools') }}</p>
                            <h3 class="mb-0 text-white">{{ $summary['schools'] }}</h3>
                        </div>
                        <span class="avatar avatar-lg" style="background:rgba(255,255,255,.2)"><i class="ti ti-building text-white fs-24"></i></span>
                    </div>
                </div>
            </div>
        </div>
    @else
        @if ($tiles !== [])
            <div class="row">
                @foreach ($tiles as [$colour, $icon, $label, $value, $hint])
                    <div class="col-xl-4 col-md-6">
                        <div class="card {{ $colour }} border-0">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="mb-1 text-white">{{ $label }}</p>
                                    <h4 class="text-white mb-1">{{ $value }}</h4>
                                    @if ($hint !== null)
                                        <span class="fs-12 text-white opacity-75">{{ $hint }}</span>
                                    @endif
                                </div>
                                <span class="avatar avatar-lg" style="background:rgba(255,255,255,.2)"><i class="ti {{ $icon }} text-white fs-24"></i></span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @if ($fees !== null || $attendance !== null)
            <div class="row">
                @if ($fees !== null)
                    <div class="col-xl-8 d-flex">
                        <div class="card flex-fill">
                            <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-2">
                                <h5 class="card-title mb-0">{{ __('dashboard.fees_chart') }}</h5>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="fs-13"><i class="ti ti-point-filled text-teal"></i>{{ __('dashboard.invoiced') }}: <b>{{ $fees['invoiced']->format() }}</b></span>
                                    <span class="fs-13"><i class="ti ti-point-filled text-orange"></i>{{ __('dashboard.collected') }}: <b>{{ $fees['collected']->format() }}</b></span>
                                </div>
                            </div>
                            <div class="card-body"><div id="feesChart"></div></div>
                        </div>
                    </div>
                @endif

                @if ($attendance !== null)
                    <div class="col-xl-4 d-flex">
                        <div class="card flex-fill">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0">{{ __('dashboard.attendance_today') }}</h5>
                                <a href="{{ route('attendance.summary') }}" class="btn btn-sm btn-light">{{ __('dashboard.view_all') }}</a>
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
            </div>
        @endif

        @if ($people !== null || $attendanceTrend !== null)
            <div class="row">
                @if ($people !== null)
                    <div class="col-xl-4 d-flex">
                        <div class="card flex-fill">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0">{{ __('dashboard.people') }}</h5>
                            </div>
                            <div class="card-body">
                                <div id="peopleChart"></div>
                                <div class="d-flex justify-content-around mt-3">
                                    <div class="text-center">
                                        <p class="fs-13 mb-1">{{ __('dashboard.people_students') }}</p>
                                        <h6 class="fw-bold mb-0">{{ number_format($people['students']) }}</h6>
                                    </div>
                                    <div class="text-center">
                                        <p class="fs-13 mb-1">{{ __('dashboard.people_teachers') }}</p>
                                        <h6 class="fw-bold mb-0">{{ number_format($people['teachers']) }}</h6>
                                    </div>
                                    <div class="text-center">
                                        <p class="fs-13 mb-1">{{ __('dashboard.people_staff') }}</p>
                                        <h6 class="fw-bold mb-0">{{ number_format($people['staff']) }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($attendanceTrend !== null)
                    <div class="col-xl-8 d-flex">
                        <div class="card flex-fill">
                            <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-2">
                                <h5 class="card-title mb-0">{{ __('dashboard.attendance_trend') }}</h5>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="fs-13"><i class="ti ti-point-filled text-success"></i>{{ __('attendance.statuses.present') }}</span>
                                    <span class="fs-13"><i class="ti ti-point-filled text-orange"></i>{{ __('attendance.statuses.absent') }}</span>
                                </div>
                            </div>
                            <div class="card-body"><div id="attendanceChart"></div></div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <div class="row">
            @if ($topStudents !== null)
                <div class="col-xl-4 col-md-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0">{{ __('dashboard.top_students') }}</h5>
                            @can('viewAny', App\Models\Exam::class)
                                <a href="{{ route('exams.index') }}" class="btn btn-sm btn-light">{{ __('dashboard.view_all') }}</a>
                            @endcan
                        </div>
                        <div class="card-body">
                            @if ($topStudents['exam'] !== null)
                                <p class="fs-13 text-muted mb-3">{{ $topStudents['exam']->name }}</p>
                            @endif

                            @forelse ($topStudents['rows'] as $row)
                                <div class="d-flex align-items-center justify-content-between {{ $loop->last ? '' : 'border-bottom pb-3 mb-3' }}">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="avatar avatar-md bg-light text-dark fw-semibold">{{ \Illuminate\Support\Str::upper(mb_substr($row['student']->first_name, 0, 1).mb_substr($row['student']->last_name, 0, 1)) }}</span>
                                        <div>
                                            <h6 class="fs-14 mb-0">{{ $row['student']->fullName() }}</h6>
                                            <span class="fs-12 text-muted">{{ $row['class'] }}</span>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="fs-12 text-muted">{{ __('dashboard.average') }}</span>
                                        <h6 class="fs-14 fw-bold mb-0">{{ \App\Domain\Exams\Score::format($row['average']) }}</h6>
                                        @if ($row['position'] !== null)
                                            <span class="badge badge-soft-primary">{{ __('dashboard.position', ['position' => $row['position']]) }}</span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted mb-0">{{ __('dashboard.no_results') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endif

            @if ($classBreakdown !== null)
                <div class="col-xl-4 col-md-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0">{{ __('dashboard.students_by_class') }}</h5>
                            <a href="{{ route('students.index') }}" class="btn btn-sm btn-light">{{ __('dashboard.view_all') }}</a>
                        </div>
                        <div class="card-body">
                            @forelse ($classBreakdown as $class)
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-dark">{{ $class['name'] }}</span>
                                        <span class="fw-semibold text-dark">{{ $class['count'] }}</span>
                                    </div>
                                    <div class="progress progress-xs">
                                        <div class="progress-bar bg-primary" style="width: {{ $class['share'] }}%"></div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted mb-0">{{ __('dashboard.no_class_breakdown') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endif

            @if ($recentStudents !== null)
                <div class="col-xl-4 col-md-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0">{{ __('dashboard.new_admissions') }}</h5>
                            <a href="{{ route('students.index') }}" class="btn btn-sm btn-light">{{ __('dashboard.view_all') }}</a>
                        </div>
                        <div class="card-body">
                            @forelse ($recentStudents as $student)
                                <div class="d-flex align-items-center justify-content-between {{ $loop->last ? '' : 'border-bottom pb-3 mb-3' }}">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="avatar avatar-md bg-light text-dark fw-semibold">{{ \Illuminate\Support\Str::upper(mb_substr($student->first_name, 0, 1).mb_substr($student->last_name, 0, 1)) }}</span>
                                        <div>
                                            <h6 class="fs-14 mb-0"><a href="{{ route('students.show', $student) }}">{{ $student->fullName() }}</a></h6>
                                            <span class="fs-12 text-muted">{{ $student->admission_number }}</span>
                                        </div>
                                    </div>
                                    <span class="fs-12 text-muted">{{ $student->admitted_on->toFormattedDateString() }}</span>
                                </div>
                            @empty
                                <p class="text-muted mb-0">{{ __('dashboard.no_students') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="row">
            <div class="col-xl-4 col-md-6 d-flex">
                <div class="card flex-fill">
                    <div class="card-header"><h5 class="card-title mb-0">{{ __('dashboard.your_school') }}</h5></div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between border-bottom pb-3 mb-3">
                            <span class="text-muted">{{ __('dashboard.plan') }}</span>
                            <span class="fw-semibold text-dark">{{ $summary['plan'] ?? __('dashboard.no_plan') }}</span>
                        </div>
                        <div class="d-flex justify-content-between border-bottom pb-3 mb-3">
                            <span class="text-muted">{{ __('dashboard.subscription') }}</span>
                            <span class="badge {{ ['active' => 'bg-success', 'grace' => 'bg-warning', 'read_only' => 'bg-danger'][$summary['status']] }}">{{ __('dashboard.status.'.$summary['status']) }}</span>
                        </div>
                        @if ($summary['status'] === 'grace' && $summary['grace_ends_at'])
                            <p class="text-muted fs-13 mb-3">{{ __('dashboard.grace_ends', ['date' => $summary['grace_ends_at']->toFormattedDateString()]) }}</p>
                        @endif
                        <div class="d-flex justify-content-between border-bottom pb-3 mb-3">
                            <span class="text-muted">{{ __('dashboard.academic_year') }}</span>
                            <span class="fw-semibold text-dark">{{ $summary['academic_year'] ?? __('dashboard.no_year') }}@if ($summary['term']) · {{ $summary['term'] }}@endif</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">{{ __('dashboard.your_roles') }}</span>
                            <span class="fw-semibold text-dark text-end">{{ $summary['roles'] === [] ? __('dashboard.no_role') : implode(', ', $summary['roles']) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            @if ($recentPayments !== null)
                <div class="col-xl-4 col-md-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0">{{ __('dashboard.recent_payments') }}</h5>
                            <a href="{{ route('fees.payments.index') }}" class="btn btn-sm btn-light">{{ __('dashboard.view_all') }}</a>
                        </div>
                        <div class="card-body">
                            @forelse ($recentPayments as $payment)
                                <div class="d-flex align-items-center justify-content-between {{ $loop->last ? '' : 'border-bottom pb-3 mb-3' }}">
                                    <div>
                                        <h6 class="fs-14 mb-0">{{ $payment->invoice->student->fullName() }}</h6>
                                        <span class="fs-12 text-muted">{{ $payment->paid_on->toFormattedDateString() }}</span>
                                    </div>
                                    <span class="fw-semibold text-dark">{{ $payment->money()->format() }}</span>
                                </div>
                            @empty
                                <p class="text-muted mb-0">{{ __('dashboard.no_payments') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif
@endsection

@if (($summary['figures']['fees'] ?? null) !== null || ($summary['figures']['people'] ?? null) !== null || ($summary['figures']['attendance_trend'] ?? null) !== null)
    @push('vendor')
        <script src="{{ asset('assets/plugins/apexchart/apexcharts.min.js') }}"></script>
    @endpush

    @push('page-scripts')
        <script>
            (function () {
                @if (($summary['figures']['fees'] ?? null) !== null)
                    var chart = @json($summary['figures']['fees']['chart']);
                    var currency = @json($summary['figures']['fees']['currency']);

                    function short(value) {
                        if (value >= 1000000) { return currency + ' ' + (Math.round(value / 100000) / 10) + 'M'; }
                        if (value >= 1000) { return currency + ' ' + (Math.round(value / 100) / 10) + 'k'; }
                        return currency + ' ' + value;
                    }

                    new ApexCharts(document.querySelector('#feesChart'), {
                        series: [
                            { name: @json(__('dashboard.invoiced')), data: chart.invoiced },
                            { name: @json(__('dashboard.collected')), data: chart.collected }
                        ],
                        chart: { type: 'bar', height: 280, toolbar: { show: false } },
                        colors: ['#0E9384', '#FE9F43'],
                        plotOptions: { bar: { columnWidth: '45%', borderRadius: 4 } },
                        xaxis: { categories: chart.labels },
                        yaxis: { labels: { formatter: short } },
                        legend: { show: false },
                        grid: { borderColor: '#E6EAED', strokeDashArray: 3 },
                        fill: { opacity: 1 },
                        dataLabels: { enabled: false }
                    }).render();
                @endif

                @if (($summary['figures']['people'] ?? null) !== null)
                    new ApexCharts(document.querySelector('#peopleChart'), {
                        series: @json($peopleSeries),
                        labels: @json($peopleLabels),
                        colors: ['#FE9F43', '#092C4C', '#0E9384'],
                        chart: { type: 'donut', height: 250 },
                        legend: { show: false },
                        dataLabels: { enabled: true, formatter: function (value) { return Math.round(value) + '%'; } },
                        plotOptions: { pie: { donut: { size: '62%' } } },
                        stroke: { width: 2 }
                    }).render();
                @endif

                @if (($summary['figures']['attendance_trend'] ?? null) !== null)
                    new ApexCharts(document.querySelector('#attendanceChart'), {
                        series: [
                            { name: @json(__('attendance.statuses.present')), data: @json($summary['figures']['attendance_trend']['present']) },
                            { name: @json(__('attendance.statuses.absent')), data: @json($summary['figures']['attendance_trend']['absent']) }
                        ],
                        chart: { type: 'area', height: 290, toolbar: { show: false } },
                        colors: ['#03C95A', '#FE9F43'],
                        stroke: { curve: 'smooth', width: 2 },
                        dataLabels: { enabled: false },
                        legend: { show: false },
                        xaxis: { categories: @json($summary['figures']['attendance_trend']['labels']) },
                        grid: { borderColor: '#E6EAED', strokeDashArray: 3 },
                        fill: { type: 'gradient', gradient: { opacityFrom: .35, opacityTo: .02 } }
                    }).render();
                @endif
            })();
        </script>
    @endpush
@endif
