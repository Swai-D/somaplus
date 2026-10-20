@extends('layouts.app')

@section('title', __('menu.invoices'))

@section('page')
    <div class="mb-3">
        <h3 class="page-title mb-1">{{ __('menu.invoices') }}</h3>
        <p class="text-muted mb-0">{{ __('fees.invoices.intro') }}</p>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('fees.invoices.index') }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="term_id">{{ __('fees.structures.term') }}</label>
                        <select class="form-select @error('term_id') is-invalid @enderror" id="term_id" name="term_id">
                            <option value="">{{ __('fees.invoices.all') }}</option>
                            @foreach ($terms as $term)
                                <option value="{{ $term->id }}" @selected($termId === $term->id)>{{ $term->academicYear->name }} – {{ $term->name }}</option>
                            @endforeach
                        </select>
                        @error('term_id')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="school_class_id">{{ __('fees.structures.class') }}</label>
                        <select class="form-select @error('school_class_id') is-invalid @enderror" id="school_class_id" name="school_class_id">
                            <option value="">{{ __('fees.invoices.all') }}</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}" @selected($classId === $class->id)>{{ $class->name }}</option>
                            @endforeach
                        </select>
                        @error('school_class_id')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3 mb-3"><button type="submit" class="btn btn-primary w-100">{{ __('fees.invoices.show_button') }}</button></div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @if ($invoices->isEmpty())
                <p class="text-muted mb-0">{{ __('fees.invoices.empty') }}</p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr>
                            <th>{{ __('fees.invoices.number') }}</th><th>{{ __('fees.invoices.student') }}</th><th>{{ __('fees.structures.term') }}</th><th>{{ __('fees.structures.class') }}</th><th class="text-end">{{ __('fees.structures.total') }}</th><th class="text-end">{{ __('fees.payments.balance') }}</th><th>{{ __('fees.payments.status') }}</th><th></th>
                        </tr></thead>
                        <tbody>
                            @foreach ($invoices as $invoice)
                                <tr>
                                    <td class="fw-medium">{{ $invoice->reference() }}</td>
                                    <td>{{ $invoice->student->fullName() }}<div class="text-muted fs-13">{{ $invoice->student->admission_number }}</div></td>
                                    <td>{{ $invoice->term->name }}</td>
                                    <td>{{ $invoice->schoolClass->name }}</td>
                                    @php($b = App\Domain\Fees\InvoiceBalance::fromLoaded($invoice))
                                    <td class="text-end">{{ $invoice->totalMoney()->format() }}</td>
                                    <td class="text-end">{{ $b->balanceMoney()->format() }}</td>
                                    <td>{{ __('fees.payments.statuses.'.$b->status()) }}</td>
                                    <td class="text-end"><a href="{{ route('fees.invoices.show', $invoice) }}" class="btn btn-sm btn-outline-secondary">{{ __('academics.open') }}</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($invoices->count() === 200)
                    <p class="text-muted fs-13 mt-3 mb-0">{{ __('fees.invoices.limited') }}</p>
                @endif
            @endif
        </div>
    </div>
@endsection
