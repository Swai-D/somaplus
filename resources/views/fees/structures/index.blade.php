@extends('layouts.app')

@section('title', __('menu.fee_structures'))

@section('page')
    @php($currency = app(App\Tenancy\TenantContext::class)->current()?->currency ?? App\Support\Money::DEFAULT_CURRENCY)
    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
        <div>
            <h3 class="page-title mb-1">{{ __('menu.fee_structures') }}</h3>
            <p class="text-muted mb-0">{{ __('fees.structures.intro') }}</p>
        </div>
        @can('create', App\Models\FeeStructure::class)
            <button type="button" class="btn btn-primary mt-2 mt-md-0" data-bs-target="#structure-modal" data-bs-toggle="modal">
                <i class="ti ti-circle-plus me-1"></i>{{ __('fees.structures.add') }}
            </button>
        @endcan
    </div>
    <div class="card">
        <div class="card-body">
            @if ($structures->isEmpty())
                <p class="text-muted mb-0">{{ __('fees.structures.empty') }}</p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr>
                            <th>{{ __('fees.structures.name') }}</th><th>{{ __('fees.structures.term') }}</th><th>{{ __('fees.structures.class') }}</th><th>{{ __('fees.structures.total') }}</th><th>{{ __('fees.structures.invoices') }}</th><th></th>
                        </tr></thead>
                        <tbody>
                            @foreach ($structures as $structure)
                                <tr>
                                    <td class="fw-medium">{{ $structure->name }}</td>
                                    <td>{{ $structure->term->academicYear->name }} – {{ $structure->term->name }}</td>
                                    <td>{{ $structure->schoolClass->name }}</td>
                                    <td>{{ App\Support\Money::of($structure->total(), $currency)->format() }}</td>
                                    <td>{{ $structure->invoices_count }}</td>
                                    <td class="text-end"><a href="{{ route('fees.structures.show', $structure) }}" class="btn btn-sm btn-outline-secondary">{{ __('academics.open') }}</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    @can('create', App\Models\FeeStructure::class)
        <x-form-modal id="structure-modal" title="{{ __('fees.structures.add') }}" action="{{ route('fees.structures.store') }}" size="modal-xl">
            @include('fees.structures._fields', ['structure' => null, 'terms' => $terms, 'classes' => $classes])
        </x-form-modal>
    @endcan
@endsection
