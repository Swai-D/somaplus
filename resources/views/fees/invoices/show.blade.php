@extends('layouts.app')

@section('title', $invoice->reference())

@section('page')
    <div class="mb-3 d-print-none">
        <a href="{{ route('fees.invoices.index') }}" class="text-muted"><i class="ti ti-arrow-left me-1"></i>{{ __('menu.invoices') }}</a>
        <div class="d-flex justify-content-between align-items-start mt-2">
            <h3 class="page-title mb-0">{{ $invoice->reference() }}</h3>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">{{ __('fees.invoices.print') }}</button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-4">{{ __('fees.invoices.student') }}</dt><dd class="col-sm-8">{{ $invoice->student->fullName() }} ({{ $invoice->student->admission_number }})</dd>
                <dt class="col-sm-4">{{ __('fees.structures.term') }}</dt><dd class="col-sm-8">{{ $invoice->term->academicYear->name }} – {{ $invoice->term->name }}</dd>
                <dt class="col-sm-4">{{ __('fees.structures.class') }}</dt><dd class="col-sm-8">{{ $invoice->schoolClass->name }}</dd>
                <dt class="col-sm-4">{{ __('fees.invoices.issued_on') }}</dt><dd class="col-sm-8">{{ $invoice->issued_on->toDateString() }}</dd>
                <dt class="col-sm-4">{{ __('fees.invoices.control_number') }}</dt><dd class="col-sm-8">{{ $invoice->control_number ?? '–' }}</dd>
            </dl>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>{{ __('fees.structures.item') }}</th><th class="text-end">{{ __('fees.structures.amount') }}</th></tr></thead>
                    <tbody>
                        @foreach ($invoice->lines as $line)
                            <tr><td>{{ $line->name }}</td><td class="text-end">{{ App\Support\Money::of($line->amount, $invoice->currency)->format() }}</td></tr>
                        @endforeach
                        <tr class="fw-bold"><td>{{ __('fees.structures.total') }}</td><td class="text-end">{{ $invoice->totalMoney()->format() }}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-4">{{ __('fees.payments.paid') }}</dt><dd class="col-sm-8">{{ $balance->paidMoney()->format() }}</dd>
                <dt class="col-sm-4">{{ __('fees.payments.balance') }}</dt><dd class="col-sm-8 fw-bold">{{ $balance->balanceMoney()->format() }}</dd>
                <dt class="col-sm-4">{{ __('fees.payments.status') }}</dt><dd class="col-sm-8">{{ __('fees.payments.statuses.'.$balance->status()) }}</dd>
            </dl>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="mb-3">{{ __('fees.payments.title') }}</h5>
            @error('reason')<div class="alert alert-danger">{{ $message }}</div>@enderror
            @if ($invoice->payments->isEmpty())
                <p class="text-muted mb-0">{{ __('fees.payments.empty') }}</p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr>
                            <th>{{ __('fees.payments.date') }}</th><th>{{ __('fees.payments.method') }}</th><th>{{ __('fees.payments.reference') }}</th><th class="text-end">{{ __('fees.structures.amount') }}</th><th>{{ __('fees.receipts.receipt') }}</th><th></th>
                        </tr></thead>
                        <tbody>
                            @foreach ($invoice->payments as $payment)
                                <tr @class(['text-decoration-line-through text-muted' => $payment->reversal])>
                                    <td>{{ $payment->paid_on->toDateString() }}</td>
                                    <td>{{ __('fees.payments.methods.'.$payment->method) }}</td>
                                    <td>{{ $payment->reference }}</td>
                                    <td class="text-end">{{ $payment->money()->format() }}</td>
                                    <td><a href="{{ route('fees.receipts.show', $payment->receipt) }}">{{ $payment->receipt->reference() }}</a>@if ($payment->reversal) <span class="badge bg-secondary ms-1">{{ __('fees.payments.reversed_badge') }}</span>@endif</td>
                                    <td class="text-end d-print-none">
                                        @can('reverse', $payment)
                                            @unless ($payment->reversal)
                                                <form action="{{ route('fees.payments.reverse', $payment) }}" method="POST" class="d-flex gap-1 justify-content-end" onsubmit="return confirm(@js(__('fees.payments.confirm_reverse')))">
                                                    @csrf
                                                    <input class="form-control form-control-sm" style="max-width: 180px" type="text" name="reason" maxlength="250" required placeholder="{{ __('fees.payments.reason') }}">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('fees.payments.reverse') }}</button>
                                                </form>
                                            @endunless
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    @can('create', App\Models\Payment::class)
        @if ($balance->balance() > 0)
            <div class="card d-print-none">
                <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h5 class="mb-0">{{ __('fees.payments.record') }}</h5>
                    <button type="button" class="btn btn-primary" data-bs-target="#invoice-payment-modal" data-bs-toggle="modal">
                        <i class="ti ti-circle-plus me-1"></i>{{ __('fees.payments.record_button') }}
                    </button>
                </div>
            </div>

            <x-form-modal id="invoice-payment-modal" title="{{ __('fees.payments.record') }}" action="{{ route('fees.invoices.payments.store', $invoice) }}" size="modal-lg">
                @include('fees.payments._fields', ['key' => $key])
            </x-form-modal>
        @endif
    @endcan

    @can('create', App\Models\Payment::class)
        @if ($mobileMoney && $invoice->currency === 'TZS' && $balance->balance() >= 100)
            <div class="card d-print-none">
                <div class="card-body">
                    <h5 class="mb-1">{{ __('integrations.azampay.request') }}</h5>
                    <p class="text-muted">{{ __('integrations.azampay.request_hint') }}</p>
                    <form action="{{ route('fees.invoices.mobile-money', $invoice) }}" method="POST">
                        @csrf
                        <input type="hidden" name="idempotency_key" value="{{ $key }}">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('integrations.azampay.phone') }}</label>
                                <input class="form-control @error('phone') is-invalid @enderror" type="text" inputmode="tel" name="phone" maxlength="20" autocomplete="off" placeholder="07XXXXXXXX" value="{{ old('phone') }}" required>
                                @error('phone')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">{{ __('integrations.azampay.network') }}</label>
                                <select class="form-select @error('provider') is-invalid @enderror" name="provider" required>
                                    @foreach (config('azampay.providers') as $network)
                                        <option value="{{ $network }}" @selected(old('provider') === $network)>{{ $network }}</option>
                                    @endforeach
                                </select>
                                @error('provider')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">{{ __('fees.structures.amount') }}</label>
                                <input class="form-control @error('amount') is-invalid @enderror" type="text" inputmode="numeric" name="amount" maxlength="14" autocomplete="off" value="{{ old('amount', intdiv($balance->balance(), 100)) }}" required>
                                @error('amount')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('integrations.azampay.send') }}</button>
                    </form>

                    @if ($requests->isNotEmpty())
                        <div class="table-responsive mt-3">
                            <table class="table align-middle mb-0">
                                <thead><tr><th>{{ __('integrations.notices.received') }}</th><th>{{ __('integrations.azampay.network') }}</th><th class="text-end">{{ __('fees.structures.amount') }}</th><th>{{ __('fees.payments.status') }}</th></tr></thead>
                                <tbody>
                                    @foreach ($requests as $row)
                                        <tr>
                                            <td>{{ $row->created_at->format('Y-m-d H:i') }}</td>
                                            <td>{{ $row->provider }}</td>
                                            <td class="text-end">{{ App\Support\Money::of($row->amount, $row->currency)->format() }}</td>
                                            <td>{{ __('integrations.azampay.statuses.'.$row->displayStatus()) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    @endcan

    @can('update', $invoice)
        <div class="card d-print-none">
            <div class="card-body">
                <h5 class="mb-1">{{ __('fees.invoices.control_number') }}</h5>
                <p class="text-muted">{{ __('fees.invoices.control_hint') }}</p>
                <form action="{{ route('fees.invoices.control-number', $invoice) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row align-items-end">
                        <div class="col-md-6 mb-3">
                            <input class="form-control @error('control_number') is-invalid @enderror" type="text" inputmode="numeric" maxlength="30" autocomplete="off" name="control_number" value="{{ old('control_number', $invoice->control_number) }}">
                            @error('control_number')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3 mb-3"><button type="submit" class="btn btn-primary">{{ __('fees.save') }}</button></div>
                    </div>
                </form>
            </div>
        </div>
    @endcan
@endsection
