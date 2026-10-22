@extends('layouts.app')

@section('title', __('idcard.title'))

@section('page')
    <div class="mb-3">
        <a href="{{ route('students.index') }}" class="text-muted"><i class="ti ti-arrow-left me-1"></i>{{ __('menu.student_list') }}</a>
        <h3 class="page-title mt-2 mb-1">{{ __('idcard.title') }}</h3>
        <p class="text-muted mb-0">{{ __('idcard.hint') }}</p>
    </div>

    @if ($year === null)
        <div class="alert alert-warning">{{ __('idcard.no_year') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('students.id-cards') }}" class="d-flex flex-wrap gap-2 align-items-end">
                <div>
                    <label class="form-label" for="class">{{ __('idcard.choose_class') }}</label>
                    <select class="form-select" id="class" name="class" onchange="this.form.submit()">
                        <option value="">{{ __('idcard.pick') }}</option>
                        <option value="all" @selected($selected === 'all')>{{ __('idcard.all_classes') }}</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}" @selected($selected === (string) $class->id)>{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <noscript><button class="btn btn-outline-primary">{{ __('idcard.show') }}</button></noscript>
            </form>
        </div>
    </div>

    @if ($selected !== '')
        <div class="card">
            <div class="card-body">
                @if ($students->isEmpty())
                    <p class="text-muted mb-0">{{ __('idcard.none') }}</p>
                @else
                    <form method="POST" action="{{ route('students.id-cards.print') }}" target="_blank" id="idcard-form">
                        @csrf
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                            <div class="form-check mb-0">
                                <input class="form-check-input" type="checkbox" id="all" checked>
                                <label class="form-check-label" for="all">{{ __('idcard.select_all') }} (<span id="count">{{ $students->count() }}</span>)</label>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="ti ti-printer me-1"></i>{{ __('idcard.print_selected') }}</button>
                        </div>
                        @if ($students->count() >= $max)
                            <div class="alert alert-info">{{ __('idcard.max_note', ['max' => $max]) }}</div>
                        @endif
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead><tr><th style="width:40px"></th><th>{{ __('students.fields.admission_number') }}</th><th>{{ __('idcard.name') }}</th><th>{{ __('students.fields.class') }}</th></tr></thead>
                                <tbody>
                                    @foreach ($students as $student)
                                        @php($placement = $placements[$student->id] ?? null)
                                        <tr>
                                            <td><input class="form-check-input js-pick" type="checkbox" name="ids[]" value="{{ $student->id }}" checked aria-label="{{ $student->fullName() }}"></td>
                                            <td>{{ $student->admission_number }}</td>
                                            <td>{{ $student->fullName() }}</td>
                                            <td>{{ $placement ? $placement->schoolClass->name.($placement->stream ? ' '.$placement->stream->name : '') : '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    @endif
@endsection

@push('page-scripts')
    <script>
        (function () {
            var all = document.getElementById('all');
            if (!all) return;
            var picks = Array.prototype.slice.call(document.querySelectorAll('.js-pick'));
            function count() { document.getElementById('count').textContent = picks.filter(function (p) { return p.checked; }).length; }
            all.addEventListener('change', function () { picks.forEach(function (p) { p.checked = all.checked; }); count(); });
            picks.forEach(function (p) { p.addEventListener('change', function () { all.checked = picks.every(function (x) { return x.checked; }); count(); }); });
        })();
    </script>
@endpush
