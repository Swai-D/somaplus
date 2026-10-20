@extends('layouts.app')

@section('title', __('exams.marks.title', ['exam' => $exam->name]))

@section('page')
    <div class="mb-3">
        <a href="{{ route('exams.show', $exam) }}" class="text-muted"><i class="ti ti-arrow-left me-1"></i>{{ $exam->name }}</a>
        <h3 class="page-title mt-2">{{ __('exams.marks.title', ['exam' => $exam->name]) }}</h3>
        <p class="text-muted mb-0">{{ $exam->academicYear->name }} – {{ $exam->term->name }} · {{ __('exams.exams.max_mark') }}: {{ $exam->max_mark }}</p>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('exams.marks.index', $exam) }}" method="GET">
                <div class="row align-items-end">
                    @include('exams.marks._filter')
                    <div class="col-md-3 mb-3"><button type="submit" class="btn btn-primary w-100">{{ __('exams.marks.show') }}</button></div>
                </div>
            </form>
        </div>
    </div>

    @if ($class !== null && $subject !== null)
        @if ($exam->isLocked())
            <div class="alert alert-warning">{{ __('exams.errors.locked') }}</div>
        @endif
        @error('exam')<div class="alert alert-danger">{{ $message }}</div>@enderror
        @error('rows')<div class="alert alert-danger">{{ $message }}</div>@enderror

        @if (count($rows) === 0)
            <div class="card"><div class="card-body text-muted">{{ __('exams.marks.empty') }}</div></div>
        @else
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">{{ $class->name }}@if ($stream) – {{ $stream->name }}@endif · {{ $subject->name }}</h5>
                    <form action="{{ route('exams.marks.store', $exam) }}" method="POST">
                        @csrf
                        <input type="hidden" name="school_class_id" value="{{ $class->id }}">
                        <input type="hidden" name="stream_id" value="{{ $stream?->id }}">
                        <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                        <div class="table-responsive">
                            <table class="table align-middle mb-3">
                                <thead><tr>
                                    <th>{{ __('exams.marks.student') }}</th>
                                    <th style="width: 160px">{{ __('exams.marks.score') }} / {{ $exam->max_mark }}</th>
                                    <th>{{ __('exams.marks.absent') }}</th>
                                    <th>{{ __('exams.marks.grade') }}</th>
                                </tr></thead>
                                <tbody>
                                    @foreach ($rows as $row)
                                        @php($id = $row->student->id)
                                        <tr>
                                            <td class="fw-medium">{{ $row->student->fullName() }}<div class="text-muted fs-13">{{ $row->student->admission_number }}</div></td>
                                            <td>
                                                <input class="form-control form-control-sm @error("rows.{$id}.score") is-invalid @enderror" type="text" inputmode="decimal" maxlength="10" autocomplete="off"
                                                    name="rows[{{ $id }}][score]" value="{{ old("rows.{$id}.score", $row->score()) }}" @disabled(! $editable)>
                                                @error("rows.{$id}.score")<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
                                            </td>
                                            <td>
                                                <input type="hidden" name="rows[{{ $id }}][absent]" value="0">
                                                <input class="form-check-input" type="checkbox" name="rows[{{ $id }}][absent]" value="1" @checked(old("rows.{$id}.absent", $row->isAbsent())) @disabled(! $editable)>
                                            </td>
                                            <td>
                                                @if ($row->isAbsent()) <span class="text-muted">{{ __('exams.marks.absent') }}</span>
                                                @elseif ($row->band) <span class="fw-medium">{{ $row->band->grade }}</span> <span class="text-muted fs-13">{{ $row->band->remark }}</span>
                                                @else <span class="text-muted">–</span> @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if ($editable)
                            <p class="text-muted fs-13">{{ __('exams.marks.hint') }}</p>
                            <button type="submit" class="btn btn-primary">{{ __('exams.marks.save') }}</button>
                        @endif
                    </form>
                </div>
            </div>
        @endif
    @endif
@endsection
