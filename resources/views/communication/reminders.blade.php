@extends('layouts.app')

@section('title', __('menu.fee_reminders'))

@section('page')
    <div class="mb-3">
        <h3 class="page-title mb-1">{{ __('menu.fee_reminders') }}</h3>
        <p class="text-muted mb-0">{{ __('communication.intro') }}</p>
    </div>

    @unless ($smsActive)
        <div class="alert alert-warning">
            {{ __('communication.sms_off') }}
            @can('settings.manage')<a href="{{ route('settings.integrations.index') }}">{{ __('menu.integrations') }}</a>@endcan
        </div>
    @endunless

    @error('invoices')<div class="alert alert-danger">{{ $message }}</div>@enderror
    @error('invoices.*')<div class="alert alert-danger">{{ $message }}</div>@enderror

    <div class="card">
        <div class="card-body">
            <form action="{{ route('communication.reminders.index') }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="term_id">{{ __('fees.structures.term') }}</label>
                        <select class="form-select" id="term_id" name="term_id">
                            <option value="">{{ __('fees.invoices.all') }}</option>
                            @foreach ($terms as $term)
                                <option value="{{ $term->id }}" @selected($termId === $term->id)>{{ $term->academicYear->name }} – {{ $term->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="school_class_id">{{ __('fees.structures.class') }}</label>
                        <select class="form-select" id="school_class_id" name="school_class_id">
                            <option value="">{{ __('fees.invoices.all') }}</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}" @selected($classId === $class->id)>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3"><button type="submit" class="btn btn-outline-primary">{{ __('fees.invoices.show_button') }}</button></div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @if ($rows->isEmpty())
                <p class="text-muted mb-0">{{ __('communication.none') }}</p>
            @else
                <form action="{{ route('communication.reminders.store') }}" method="POST">
                    @csrf
                    <div class="table-responsive">
                        <table class="table align-middle mb-3">
                            <thead><tr>
                                <th style="width: 40px"><input type="checkbox" class="form-check-input js-all" aria-label="{{ __('communication.select_all') }}"></th>
                                <th>{{ __('fees.invoices.student') }}</th><th>{{ __('fees.invoices.number') }}</th><th class="text-end">{{ __('fees.payments.balance') }}</th><th>{{ __('communication.guardians') }}</th><th>{{ __('communication.last') }}</th>
                            </tr></thead>
                            <tbody>
                                @foreach ($rows as $row)
                                    <tr>
                                        <td><input type="checkbox" class="form-check-input js-row" name="invoices[]" value="{{ $row['invoice']->id }}" @disabled($row['reachable'] === 0) @checked(in_array($row['invoice']->id, array_map('intval', (array) old('invoices', []))))></td>
                                        <td>{{ $row['invoice']->student->fullName() }}</td>
                                        <td>{{ $row['invoice']->reference() }}</td>
                                        <td class="text-end">{{ $row['balance']->format() }}</td>
                                        <td>@if ($row['reachable'] > 0){{ trans_choice('communication.reachable', $row['reachable'], ['count' => $row['reachable']]) }}@else<span class="text-danger">{{ __('communication.no_phone') }}</span>@endif</td>
                                        <td>@if ($row['last']){{ __('communication.statuses.'.$row['last']->status) }} · {{ $row['last']->day->toDateString() }}@endif</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if ($truncated)<p class="text-muted fs-13">{{ __('communication.limited') }}</p>@endif

                    <div class="row align-items-end">
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="locale">{{ __('communication.language') }}</label>
                            <select class="form-select" id="locale" name="locale">
                                @foreach (config('app.available_locales') as $code)
                                    <option value="{{ $code }}" @selected(old('locale', 'sw') === $code)>{{ __('communication.languages.'.$code) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3"><button type="submit" class="btn btn-primary" @disabled(! $smsActive)>{{ __('communication.send') }}</button></div>
                    </div>
                    <p class="text-muted fs-13 mb-0">{{ __('communication.send_hint') }}</p>
                </form>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="mb-3">{{ __('communication.history') }}</h5>
            @if ($history->isEmpty())
                <p class="text-muted mb-0">{{ __('communication.history_none') }}</p>
            @else
                <div class="table-responsive"><table class="table align-middle mb-0">
                    <thead><tr><th>{{ __('integrations.notices.received') }}</th><th>{{ __('fees.invoices.number') }}</th><th>{{ __('fees.payments.status') }}</th></tr></thead>
                    <tbody>
                        @foreach ($history as $message)
                            <tr>
                                <td>{{ $message->created_at->format('Y-m-d H:i') }}</td>
                                <td>{{ $message->invoice->reference() }}</td>
                                <td>{{ __('communication.statuses.'.$message->status) }}@if ($message->error) <span class="text-muted fs-13">· {{ __('communication.reasons.'.$message->error) !== 'communication.reasons.'.$message->error ? __('communication.reasons.'.$message->error) : $message->error }}</span>@endif</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table></div>
            @endif
        </div>
    </div>
@endsection

@push('page-scripts')
<script>
    (function () {
        var all = document.querySelector('.js-all');
        if (!all) { return; }
        all.addEventListener('change', function () {
            document.querySelectorAll('.js-row:not(:disabled)').forEach(function (box) { box.checked = all.checked; });
        });
    })();
</script>
@endpush
