@extends('layouts.app')

@section('title', $receipt->reference())

@section('page')
    @php($payment = $receipt->payment)
    @php($invoice = $payment->invoice)
    <div class="mb-3 d-print-none">
        <a href="{{ route('fees.invoices.show', $invoice) }}" class="text-muted"><i class="ti ti-arrow-left me-1"></i>{{ $invoice->reference() }}</a>
        <div class="d-flex justify-content-between align-items-start mt-2">
            <h3 class="page-title mb-0">{{ __('fees.receipts.receipt') }} {{ $receipt->reference() }}</h3>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">{{ __('fees.invoices.print') }}</button>
        </div>
    </div>

    @if ($payment->reversal)
        <div class="alert alert-warning">{{ __('fees.receipts.reversed', ['date' => $payment->reversal->reversed_on->toDateString()]) }} {{ $payment->reversal->reason }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            @include('partials.letterhead')
            <dl class="row mb-0">
                <dt class="col-sm-4">{{ __('fees.receipts.receipt') }}</dt><dd class="col-sm-8">{{ $receipt->reference() }} · {{ $receipt->issued_on->toDateString() }}</dd>
                <dt class="col-sm-4">{{ __('fees.invoices.student') }}</dt><dd class="col-sm-8">{{ $invoice->student->fullName() }} ({{ $invoice->student->admission_number }})</dd>
                <dt class="col-sm-4">{{ __('fees.invoices.number') }}</dt><dd class="col-sm-8">{{ $invoice->reference() }} · {{ $invoice->term->academicYear->name }} – {{ $invoice->term->name }}</dd>
                <dt class="col-sm-4">{{ __('fees.payments.date') }}</dt><dd class="col-sm-8">{{ $payment->paid_on->toDateString() }}</dd>
                <dt class="col-sm-4">{{ __('fees.payments.method') }}</dt><dd class="col-sm-8">{{ __('fees.payments.methods.'.$payment->method) }}@if ($payment->reference) · {{ $payment->reference }}@endif</dd>
                @if ($payment->control_number)
                    <dt class="col-sm-4">{{ __('fees.invoices.control_number') }}</dt><dd class="col-sm-8">{{ $payment->control_number }}</dd>
                @endif
                <dt class="col-sm-4">{{ __('fees.receipts.amount_received') }}</dt><dd class="col-sm-8 fw-bold">{{ $payment->money()->format() }}</dd>
            </dl>
        </div>
    </div>
@endsection
