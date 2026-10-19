@extends('layouts.app')

@section('title', __('menu.take_attendance'))

@section('page')
    <div class="mb-3">
        <h3 class="page-title mb-1">{{ __('menu.take_attendance') }}</h3>
        <p class="text-muted mb-0">{{ __('attendance.intro') }}</p>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('attendance.index') }}" method="GET">
                <div class="row align-items-end">
                    @include('attendance._filter')
                    <div class="col-md-2 mb-3">
                        <label class="form-label" for="date">{{ __('attendance.date') }}</label>
                        <input class="form-control @error('date') is-invalid @enderror" id="date" name="date" type="date" value="{{ $date }}" max="{{ now()->toDateString() }}" required>
                        @error('date')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-2 mb-3"><button type="submit" class="btn btn-primary w-100">{{ __('attendance.show') }}</button></div>
                </div>
            </form>
        </div>
    </div>

    @if ($class !== null)
        @if ($year === null)
            <div class="alert alert-warning">{{ __('attendance.errors.no_year') }}</div>
        @elseif ($students->isEmpty())
            <div class="card"><div class="card-body text-muted">{{ __('attendance.empty') }}</div></div>
        @else
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">{{ $class->name }}@if ($stream) – {{ $stream->name }}@endif · {{ $date }}</h5>
                    @error('rows')<div class="alert alert-danger">{{ $message }}</div>@enderror
                    <form action="{{ route('attendance.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="school_class_id" value="{{ $class->id }}">
                        <input type="hidden" name="stream_id" value="{{ $stream?->id }}">
                        <input type="hidden" name="date" value="{{ $date }}">
                        <div class="table-responsive">
                            <table class="table align-middle mb-3">
                                <thead>
                                    <tr>
                                        <th>{{ __('attendance.student') }}</th>
                                        <th>{{ __('attendance.status') }}</th>
                                        <th>{{ __('attendance.note') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($students as $student)
                                        @php($record = $records[$student->id] ?? null)
                                        @php($current = old("rows.{$student->id}.status", $record?->status ?? 'present'))
                                        <tr>
                                            <td class="fw-medium">{{ $student->fullName() }}<div class="text-muted fs-13">{{ $student->admission_number }}</div></td>
                                            <td class="text-nowrap">
                                                @foreach (App\Models\AttendanceRecord::STATUSES as $status)
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="rows[{{ $student->id }}][status]" id="s{{ $student->id }}{{ $status }}" value="{{ $status }}" @checked($current === $status)>
                                                        <label class="form-check-label" for="s{{ $student->id }}{{ $status }}">{{ __('attendance.statuses.'.$status) }}</label>
                                                    </div>
                                                @endforeach
                                            </td>
                                            <td><input class="form-control form-control-sm" type="text" maxlength="255" name="rows[{{ $student->id }}][note]" value="{{ old("rows.{$student->id}.note", $record?->note) }}"></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('attendance.save') }}</button>
                    </form>
                </div>
            </div>
        @endif
    @endif
@endsection
