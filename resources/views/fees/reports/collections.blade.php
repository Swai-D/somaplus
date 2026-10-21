@extends('layouts.app')

@section('title', __('menu.collections_report'))

@php
    $money = fn (int $minor, string $currency) => App\Support\Money::of($minor, $currency)->format();
    $currencies = array_values(array_unique(array_merge(array_keys($report['collected']), array_keys($report['outstanding']), array_keys($report['reversed']))));
@endphp

@section('page')
    <div class="mb-3">
        <h3 class="page-title mb-1">{{ __('menu.collections_report') }}</h3>
        <p class="text-muted mb-0">{{ __('fees.report.intro') }}</p>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('fees.reports.collections') }}" class="row align-items-end">
                <div class="col-md-3 mb-3">
                    <label class="form-label" for="from">{{ __('fees.report.from') }}</label>
                    <input class="form-control @error('from') is-invalid @enderror" type="date" id="from" name="from" value="{{ $from->toDateString() }}">
                    @error('from')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label" for="to">{{ __('fees.report.to') }}</label>
                    <input class="form-control @error('to') is-invalid @enderror" type="date" id="to" name="to" value="{{ $to->toDateString() }}">
                    @error('to')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3 mb-3"><button type="submit" class="btn btn-primary">{{ __('fees.invoices.show_button') }}</button></div>
            </form>
        </div>
    </div>

    @forelse ($currencies as $currency)
        @php $row = $report['collected'][$currency] ?? ['total' => 0, 'count' => 0, 'methods' => [], 'sources' => []]; @endphp
        <div class="row">
            <div class="col-md-4"><div class="card"><div class="card-body">
                <div class="text-muted">{{ __('fees.report.collected') }} ({{ $currency }})</div>
                <h3 class="mb-0">{{ $money($row['total'], $currency) }}</h3>
                <div class="text-muted fs-13">{{ trans_choice('fees.report.payments_count', $row['count'], ['count' => $row['count']]) }}</div>
            </div></div></div>
            <div class="col-md-4"><div class="card"><div class="card-body">
                <div class="text-muted">{{ __('fees.report.outstanding') }} ({{ $currency }})</div>
                <h3 class="mb-0">{{ $money($report['outstanding'][$currency]['total'] ?? 0, $currency) }}</h3>
                <div class="text-muted fs-13">{{ trans_choice('fees.report.invoices_count', $report['outstanding'][$currency]['invoices'] ?? 0, ['count' => $report['outstanding'][$currency]['invoices'] ?? 0]) }}</div>
            </div></div></div>
            <div class="col-md-4"><div class="card"><div class="card-body">
                <div class="text-muted">{{ __('fees.report.reversed') }} ({{ $currency }})</div>
                <h3 class="mb-0">{{ $money($report['reversed'][$currency]['total'] ?? 0, $currency) }}</h3>
                <div class="text-muted fs-13">{{ trans_choice('fees.report.payments_count', $report['reversed'][$currency]['count'] ?? 0, ['count' => $report['reversed'][$currency]['count'] ?? 0]) }}</div>
            </div></div></div>
        </div>

        <div class="row">
            <div class="col-md-6"><div class="card"><div class="card-body">
                <h5 class="mb-3">{{ __('fees.report.by_method') }}</h5>
                <table class="table mb-0"><tbody>
                    @foreach (App\Models\Payment::METHODS as $method)
                        <tr><td>{{ __('fees.payments.methods.'.$method) }}</td><td class="text-end">{{ $money($row['methods'][$method] ?? 0, $currency) }}</td></tr>
                    @endforeach
                </tbody></table>
            </div></div></div>
            <div class="col-md-6"><div class="card"><div class="card-body">
                <h5 class="mb-3">{{ __('fees.report.by_source') }}</h5>
                <table class="table mb-0"><tbody>
                    @foreach (['manual', 'integration'] as $source)
                        <tr><td>{{ __('fees.report.sources.'.$source) }}</td><td class="text-end">{{ $money($row['sources'][$source] ?? 0, $currency) }}</td></tr>
                    @endforeach
                </tbody></table>
            </div></div></div>
        </div>

        @if ($report['days'] !== [])
            <div class="card"><div class="card-body">
                <h5 class="mb-3">{{ __('fees.report.by_day') }} ({{ $currency }})</h5>
                <div class="table-responsive"><table class="table align-middle mb-0">
                    <thead><tr><th>{{ __('fees.payments.date') }}</th><th class="text-end">{{ __('fees.report.collected') }}</th></tr></thead>
                    <tbody>
                        @foreach ($report['days'] as $day => $perCurrency)
                            @if (isset($perCurrency[$currency]))
                                <tr><td>{{ $day }}</td><td class="text-end">{{ $money($perCurrency[$currency], $currency) }}</td></tr>
                            @endif
                        @endforeach
                    </tbody>
                </table></div>
            </div></div>
        @endif
    @empty
        <div class="card"><div class="card-body text-muted">{{ __('fees.report.none') }}</div></div>
    @endforelse

    <div class="card">
        <div class="card-body">
            <h5 class="mb-1">{{ __('fees.report.exceptions') }}</h5>
            <p class="text-muted">{{ __('fees.report.exceptions_hint') }}</p>
            @if ($report['notices'] === [])
                <p class="text-muted mb-0">{{ __('fees.report.exceptions_none') }}</p>
            @else
                <p class="mb-2">
                    @foreach ($report['notice_counts'] as $status => $count)
                        <span class="badge bg-warning text-dark me-1">{{ __('integrations.notices.statuses.'.$status) }}: {{ $count }}</span>
                    @endforeach
                </p>
                <div class="table-responsive"><table class="table align-middle mb-0">
                    <thead><tr><th>{{ __('integrations.notices.received') }}</th><th>{{ __('fees.invoices.control_number') }}</th><th>{{ __('integrations.notices.reference') }}</th><th class="text-end">{{ __('fees.structures.amount') }}</th><th>{{ __('fees.payments.status') }}</th></tr></thead>
                    <tbody>
                        @foreach ($report['notices'] as $notice)
                            <tr>
                                <td>{{ $notice->received_at->format('Y-m-d H:i') }}</td>
                                <td>{{ $notice->control_number }}</td>
                                <td>{{ $notice->provider_reference }}</td>
                                <td class="text-end">@if ($notice->amount !== null && $notice->currency !== null){{ $money($notice->amount, $notice->currency) }}@endif</td>
                                <td>{{ __('integrations.notices.statuses.'.$notice->status) }}@if ($notice->reason) <span class="text-muted fs-13">· {{ __('integrations.notices.reasons.'.$notice->reason) }}</span>@endif</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table></div>
            @endif
        </div>
    </div>
@endsection
