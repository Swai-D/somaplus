@extends('layouts.app')

@section('title', __('menu.payments'))

@section('page')
    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
        <div>
            <h3 class="page-title mb-1">{{ __('menu.payments') }}</h3>
            <p class="text-muted mb-0">{{ __('fees.payments.intro') }}</p>
        </div>
        @can('create', App\Models\Payment::class)
            <button type="button" class="btn btn-primary mt-2 mt-md-0" data-bs-target="#payment-modal" data-bs-toggle="modal">
                <i class="ti ti-circle-plus me-1"></i>{{ __('fees.payments.record_by_control') }}
            </button>
        @endcan
    </div>
    <div class="card">
        <div class="card-body">
            @if ($payments->isEmpty())
                <p class="text-muted mb-0">{{ __('fees.payments.none') }}</p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr>
                            <th>{{ __('fees.receipts.receipt') }}</th><th>{{ __('fees.payments.date') }}</th><th>{{ __('fees.invoices.student') }}</th><th>{{ __('fees.invoices.number') }}</th><th>{{ __('fees.payments.method') }}</th><th class="text-end">{{ __('fees.structures.amount') }}</th><th></th>
                        </tr></thead>
                        <tbody>
                            @foreach ($payments as $payment)
                                <tr @class(['text-decoration-line-through text-muted' => $payment->reversal])>
                                    <td class="fw-medium">{{ $payment->receipt->reference() }}</td>
                                    <td>{{ $payment->paid_on->toDateString() }}</td>
                                    <td>{{ $payment->invoice->student->fullName() }}</td>
                                    <td>{{ $payment->invoice->reference() }}</td>
                                    <td>{{ __('fees.payments.methods.'.$payment->method) }}</td>
                                    <td class="text-end">{{ $payment->money()->format() }}</td>
                                    <td class="text-end"><a href="{{ route('fees.receipts.show', $payment->receipt) }}" class="btn btn-sm btn-outline-secondary">{{ __('academics.open') }}</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($payments->count() === 200)
                    <p class="text-muted fs-13 mt-3 mb-0">{{ __('fees.payments.limited') }}</p>
                @endif
            @endif
        </div>
    </div>

    @can('create', App\Models\Payment::class)
        <x-form-modal id="payment-modal" title="{{ __('fees.payments.record_by_control') }}" action="{{ route('fees.payments.store') }}" size="modal-lg">
            @include('fees.payments._fields', ['key' => $paymentKey, 'withControlNumber' => true])
        </x-form-modal>
    @endcan
@endsection
