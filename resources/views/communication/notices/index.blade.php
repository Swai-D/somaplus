@extends('layouts.app')

@section('title', __('menu.notices'))

@section('page')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4>{{ __('menu.notices') }}</h4>
                    <h6>Dashboard / HRM</h6>
                </div>
                <ul class="table-top-head">
                    <li class="me-2"><a href="javascript:void(0);" title="Pdf"><img alt="pdf" src="{{ asset('assets/img/icons/pdf.svg') }}"/></a></li>
                    <li class="me-2"><a href="javascript:void(0);" title="Excel"><img alt="excel" src="{{ asset('assets/img/icons/excel.svg') }}"/></a></li>
                    <li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" href="javascript:location.reload()" title="Refresh"><i class="ti ti-refresh"></i></a></li>
                    <li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" id="collapse-header" title="Collapse"><i class="ti ti-chevron-up"></i></a></li>
                </ul>
                <div class="page-btn">
                    <a class="btn btn-primary d-flex align-items-center gap-1" data-bs-target="#notice-modal" data-bs-toggle="modal" href="javascript:void(0);">
                        <i class="ti ti-circle-plus me-1"></i>{{ __('communication.notices.add') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                    <div class="search-set">
                        <div class="search-input">
                            <span class="btn-searchset"><i class="ti ti-search fs-14 feather-search"></i></span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center flex-wrap gap-2"></div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="datatable table">
                            <thead class="thead-light">
                            <tr>
                                <th scope="col">
                                    <div class="d-flex align-items-center">
                                        <label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label>
                                        <span class="ms-2">S.L</span>
                                    </div>
                                </th>
                                <th scope="col">{{ __('communication.notices.day') }}</th>
                                <th scope="col">{{ __('communication.notices.title') }}</th>
                                <th scope="col">{{ __('communication.notices.description') }}</th>
                                <th scope="col"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($notices as $notice)
                                <tr>
                                    <td class="text-muted">
                                        <div class="d-flex align-items-center">
                                            <label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label>
                                            <span class="ms-2">{{ $loop->iteration }}</span>
                                        </div>
                                    </td>
                                    <td class="text-nowrap">{{ $notice->day->toDateString() }}</td>
                                    <td class="fw-medium">{{ $notice->title }}</td>
                                    <td>{{ $notice->description ? \Illuminate\Support\Str::limit($notice->description, 120) : '—' }}</td>
                                    <td class="text-end">
                                        <div class="d-flex gap-2 justify-content-end">
                                            @can('update', $notice)
                                                <a href="{{ route('communication.notices.edit', $notice) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('communication.notices.edit') }}"><i class="ti ti-edit"></i></a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">{{ __('communication.notices.empty') }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @can('create', App\Models\Notice::class)
            <x-form-modal id="notice-modal" title="{{ __('communication.notices.add') }}" action="{{ route('communication.notices.store') }}">
                @include('communication.notices._fields', ['notice' => null])
            </x-form-modal>
        @endcan
    </div>
</div>
@endsection