@extends('layouts.app')

@section('title', $structure->name)

@section('page')
    @php($currency = app(App\Tenancy\TenantContext::class)->current()?->currency ?? App\Support\Money::DEFAULT_CURRENCY)
    <div class="mb-3">
        <a href="{{ route('fees.structures.index') }}" class="text-muted"><i class="ti ti-arrow-left me-1"></i>{{ __('menu.fee_structures') }}</a>
        <div class="d-flex justify-content-between align-items-start mt-2">
            <h3 class="page-title mb-0">{{ $structure->name }}</h3>
            @can('update', $structure)
                @unless ($invoiceCount > 0)
                    <a href="{{ route('fees.structures.edit', $structure) }}" class="btn btn-sm btn-outline-secondary">{{ __('fees.edit') }}</a>
                @endunless
            @endcan
        </div>
        <p class="text-muted mb-0">{{ $structure->term->academicYear->name }} – {{ $structure->term->name }} · {{ $structure->schoolClass->name }}</p>
    </div>

    @error('delete')<div class="alert alert-danger">{{ $message }}</div>@enderror

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>{{ __('fees.structures.item') }}</th><th class="text-end">{{ __('fees.structures.amount') }}</th></tr></thead>
                    <tbody>
                        @foreach ($structure->items as $item)
                            <tr><td>{{ $item->name }}</td><td class="text-end">{{ App\Support\Money::of($item->amount, $currency)->format() }}</td></tr>
                        @endforeach
                        <tr class="fw-bold"><td>{{ __('fees.structures.total') }}</td><td class="text-end">{{ App\Support\Money::of($structure->total(), $currency)->format() }}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @can('generate', $structure)
        <div class="card">
            <div class="card-body">
                <h5 class="mb-1">{{ __('fees.structures.generate') }}</h5>
                <p class="text-muted">{{ __('fees.structures.generate_hint') }}</p>
                <form action="{{ route('fees.structures.generate', $structure) }}" method="POST">
                    @csrf
                    <div class="row align-items-end">
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="stream_id">{{ __('fees.structures.stream') }}</label>
                            <select class="form-select @error('stream_id') is-invalid @enderror" id="stream_id" name="stream_id">
                                <option value="">{{ __('fees.structures.all_streams') }}</option>
                                @foreach ($streams as $stream)
                                    <option value="{{ $stream->id }}">{{ $stream->name }}</option>
                                @endforeach
                            </select>
                            @error('stream_id')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3"><button type="submit" class="btn btn-primary">{{ __('fees.structures.generate_button') }}</button></div>
                    </div>
                </form>
                <p class="text-muted fs-13 mb-0">{{ trans_choice('fees.structures.invoices_made', $invoiceCount, ['count' => $invoiceCount]) }}</p>
            </div>
        </div>
    @endcan

    @can('delete', $structure)
        @if ($invoiceCount === 0)
            <div class="card"><div class="card-body">@include('academics._delete', ['action' => route('fees.structures.destroy', $structure)])</div></div>
        @endif
    @endcan
@endsection
