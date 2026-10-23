@extends('layouts.app')

@section('title', __('menu.messages'))

@section('page')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4>{{ __('menu.messages') }}</h4>
                    <h6>Dashboard / HRM</h6>
                </div>
                <ul class="table-top-head">
                    <li class="me-2"><a href="javascript:void(0);" title="Pdf"><img alt="pdf" src="{{ asset('assets/img/icons/pdf.svg') }}"/></a></li>
                    <li class="me-2"><a href="javascript:void(0);" title="Excel"><img alt="excel" src="{{ asset('assets/img/icons/excel.svg') }}"/></a></li>
                    <li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" href="javascript:location.reload()" title="Refresh"><i class="ti ti-refresh"></i></a></li>
                    <li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" id="collapse-header" title="Collapse"><i class="ti ti-chevron-up"></i></a></li>
                </ul>
                <div class="page-btn">
                    <a class="btn btn-primary d-flex align-items-center gap-1" data-bs-target="#message-modal" data-bs-toggle="modal" href="javascript:void(0);">
                        <i class="ti ti-circle-plus me-1"></i>{{ __('communication.messages.add') }}
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
                        <table class="datatable table align-middle mb-0">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('communication.notices.day') }}</th>
                                <th>{{ __('communication.messages.kind') }}</th>
                                <th>{{ __('communication.messages.student') }}</th>
                                <th>{{ __('communication.messages.guardian') }}</th>
                                <th>{{ __('communication.messages.status') }}</th>
                                <th>{{ __('communication.messages.sent_at') }}</th>
                                <th>{{ __('communication.messages.reason') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($messages as $message)
                                @php
                                    $reason = $message->error ? __('communication.reasons.'.$message->error) : null;
                                    if ($reason === 'communication.reasons.'.$message->error) {
                                        $reason = $message->error;
                                    }
                                @endphp
                                <tr>
                                    <td class="text-muted">{{ $loop->iteration }}</td>
                                    <td class="text-nowrap">{{ $message->day->toDateString() }}</td>
                                    <td>{{ $kinds[$message->kind] ?? $message->kind }}</td>
                                    <td>{{ $message->invoice?->student?->fullName() ?? '—' }}</td>
                                    <td>{{ $message->guardian?->name ?? '—' }}</td>
                                    <td>{{ __('communication.statuses.'.$message->status) }}</td>
                                    <td class="text-nowrap">{{ $message->sent_at?->format('d M Y H:i') ?? '—' }}</td>
                                    <td>{{ $reason ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">{{ __('communication.messages.empty') }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @can('create', App\Models\SmsMessage::class)
            <x-form-modal id="message-modal" title="{{ __('communication.messages.add') }}" action="{{ route('communication.reminders.store') }}">
                @include('communication.notices._fields', ['notice' => null])
            </x-form-modal>
        @endcan
    </div>
</div>
@endsection