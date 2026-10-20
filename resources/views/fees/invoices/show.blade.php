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
