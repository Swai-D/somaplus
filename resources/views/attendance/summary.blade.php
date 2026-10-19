@extends('layouts.app')

@section('title', __('menu.attendance_summary'))

@section('page')
    <div class="mb-3">
        <h3 class="page-title mb-1">{{ __('menu.attendance_summary') }}</h3>
        <p class="text-muted mb-0">{{ __('attendance.summary_intro') }}</p>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('attendance.summary') }}" method="GET">
                <div class="row align-items-end">
                    @include('attendance._filter')
                    <div class="col-md-2 mb-3">
                        <label class="form-label" for="from">{{ __('attendance.from') }}</label>
                        <input class="form-control" id="from" name="from" type="date" value="{{ $from }}" required>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label" for="to">{{ __('attendance.to') }}</label>
                        <input class="form-control @error('to') is-invalid @enderror" id="to" name="to" type="date" value="{{ $to }}" required>
                        @error('to')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 mb-3"><button type="submit" class="btn btn-primary">{{ __('attendance.show') }}</button></div>
                </div>
            </form>
        </div>
    </div>

    @if ($class !== null)
        <div class="card">
            <div class="card-body">
                @if ($rows === [])
                    <p class="text-muted mb-0">{{ __('attendance.summary_empty') }}</p>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('attendance.student') }}</th>
                                    @foreach (App\Models\AttendanceRecord::STATUSES as $status)
                                        <th class="text-end">{{ __('attendance.statuses.'.$status) }}</th>
                                    @endforeach
                                    <th class="text-end">{{ __('attendance.rate') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rows as $row)
                                    <tr>
                                        <td class="fw-medium">{{ $row->student->fullName() }}</td>
                                        @foreach (App\Models\AttendanceRecord::STATUSES as $status)
                                            <td class="text-end">{{ $row->{$status} }}</td>
                                        @endforeach
                                        <td class="text-end">{{ $row->rate() === null ? '–' : $row->rate().'%' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p class="text-muted fs-13 mt-3 mb-0">{{ __('attendance.rate_hint') }}</p>
                @endif
            </div>
        </div>
    @endif
@endsection
