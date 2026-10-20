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
                <div class="card-body">
                    <h5 class="mb-3">{{ __('fees.payments.record') }}</h5>
                    <form action="{{ route('fees.invoices.payments.store', $invoice) }}" method="POST">
                        @csrf
                        @include('fees.payments._fields', ['key' => $key])
                        <button type="submit" class="btn btn-primary">{{ __('fees.payments.record_button') }}</button>
                    </form>
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
