@extends('layouts.app')

@section('title', $student->fullName())

@section('page')
    <div class="mb-3">
        <a href="{{ route('family.index') }}" class="text-muted"><i class="ti ti-arrow-left me-1"></i>{{ __('family.title') }}</a>
        <h3 class="page-title mt-2 mb-1">{{ $student->fullName() }}</h3>
        <p class="text-muted mb-0">{{ $student->admission_number }}</p>
    </div>

    <div class="card">
        <div class="card-header"><h5 class="mb-0">{{ __('family.results') }}</h5></div>
        <div class="card-body">
            <p class="text-muted">{{ __('family.results_hint') }}</p>
            <div class="d-flex flex-wrap gap-2">
                @foreach ($terms as $term)
                    <a class="btn btn-outline-primary btn-sm" href="{{ route('family.children.report', [$student->id, $term->id]) }}">{{ $term->academicYear->name }} – {{ $term->name }}</a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5 class="mb-0">{{ __('family.invoices') }}</h5></div>
        <div class="card-body">
            @if ($invoices->isEmpty())
                <p class="text-muted mb-0">{{ __('family.no_invoices') }}</p>
            @else
                <div class="table-responsive"><table class="table align-middle mb-0">
                    <thead><tr><th>{{ __('fees.invoices.number') }}</th><th>{{ __('fees.structures.term') }}</th><th class="text-end">{{ __('family.total') }}</th><th class="text-end">{{ __('family.paid') }}</th><th class="text-end">{{ __('fees.payments.balance') }}</th><th>{{ __('fees.invoices.control_number') }}</th></tr></thead>
                    <tbody>
                        @foreach ($invoices as $row)
                            <tr>
                                <td>{{ $row['invoice']->reference() }}</td>
                                <td>{{ $row['invoice']->term->academicYear->name }} – {{ $row['invoice']->term->name }}</td>
                                <td class="text-end">{{ $row['invoice']->totalMoney()->format() }}</td>
                                <td class="text-end">{{ $row['balance']->paidMoney()->format() }}</td>
                                <td class="text-end fw-medium">{{ $row['balance']->balanceMoney()->format() }}</td>
                                <td>{{ $row['invoice']->control_number }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table></div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5 class="mb-0">{{ __('family.payments') }}</h5></div>
        <div class="card-body">
            @if ($payments->isEmpty())
                <p class="text-muted mb-0">{{ __('family.no_payments') }}</p>
            @else
                <div class="table-responsive"><table class="table align-middle mb-0">
                    <thead><tr><th>{{ __('fees.payments.date') }}</th><th>{{ __('fees.receipts.receipt') }}</th><th>{{ __('fees.payments.method') }}</th><th class="text-end">{{ __('fees.receipts.amount_received') }}</th></tr></thead>
                    <tbody>
                        @foreach ($payments as $payment)
                            <tr>
                                <td>{{ $payment->paid_on->toDateString() }}</td>
                                <td>{{ $payment->receipt?->reference() }}@if ($payment->reversal) <span class="badge bg-secondary">{{ __('family.reversed') }}</span>@endif</td>
                                <td>{{ __('fees.payments.methods.'.$payment->method) }}</td>
                                <td class="text-end">{{ $payment->money()->format() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table></div>
            @endif
        </div>
    </div>
@endsection
