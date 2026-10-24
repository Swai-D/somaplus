@extends('layouts.app')

@section('title', __('menu.messages'))

@php
    // One badge per status, as the template colours its tables.
    $badges = [
        App\Models\SmsMessage::QUEUED => 'bg-info',
        App\Models\SmsMessage::SENT => 'bg-success',
        App\Models\SmsMessage::FAILED => 'bg-danger',
        App\Models\SmsMessage::CANCELLED => 'bg-secondary',
    ];

    // The states the filter offers, the order a person reads them in.
    $statuses = [App\Models\SmsMessage::SENT, App\Models\SmsMessage::QUEUED, App\Models\SmsMessage::FAILED, App\Models\SmsMessage::CANCELLED];
@endphp

@section('page')
<div class="page-header">
    <div class="add-item d-flex">
        <div class="page-title">
            <h4>{{ __('menu.messages') }}</h4>
            <h6>{{ __('menu.sections.communication') }}</h6>
        </div>
    </div>
    <ul class="table-top-head">
        <li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" href="javascript:location.reload()" title="Refresh"><i class="ti ti-refresh"></i></a></li>
        <li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" id="collapse-header" title="Collapse"><i class="ti ti-chevron-up"></i></a></li>
    </ul>
    <div class="page-btn">
        <a class="btn btn-primary d-flex align-items-center gap-1" href="{{ route('communication.reminders.index') }}">
            <i class="ti ti-bell me-1"></i>{{ __('menu.fee_reminders') }}
        </a>
    </div>
</div>

<p class="text-muted mt-2 mb-0">
    {{ __('communication.messages.intro') }}
</p>

<div class="mt-4">
    {{-- Messages are a history only: nothing is written from here, what is filtered travels in the address. --}}
    <div class="card">
        <div class="card-body">
            <form action="{{ route('communication.messages.index') }}" method="GET">
                <div class="row align-items-end gy-3">
                    <div class="col-md-3 col-6">
                        <label class="form-label" for="message-status">{{ __('communication.messages.status') }}</label>
                        <select class="form-select" id="message-status" name="status">
                            <option value="">{{ __('communication.messages.all') }}</option>
                            @foreach ($statuses as $state)
                                <option value="{{ $state }}" @selected($status === $state)>{{ __('communication.statuses.'.$state) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-6">
                        <label class="form-label" for="message-kind">{{ __('communication.messages.kind') }}</label>
                        <select class="form-select" id="message-kind" name="kind">
                            <option value="">{{ __('communication.messages.all') }}</option>
                            @foreach ($kinds as $code => $label)
                                <option value="{{ $code }}" @selected($kind === $code)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3"><button type="submit" class="btn btn-outline-primary">{{ __('communication.messages.show') }}</button></div>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-4">
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
                <table class="datatable table align-middle">
                    <thead class="thead-light">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">{{ __('communication.notices.day') }}</th>
                        <th scope="col">{{ __('communication.messages.kind') }}</th>
                        <th scope="col">{{ __('communication.messages.student') }}</th>
                        <th scope="col">{{ __('communication.messages.guardian') }}</th>
                        <th scope="col">{{ __('communication.messages.status') }}</th>
                        <th scope="col">{{ __('communication.messages.sent_at') }}</th>
                        <th scope="col">{{ __('communication.messages.reason') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($messages as $message)
                        <tr>
                            <td class="text-muted">{{ $loop->iteration }}</td>
                            <td class="text-nowrap">
                                <time datetime="{{ $message->day->toDateString() }}">{{ $message->day->translatedFormat('d M Y') }}</time>
                            </td>
                            <td>{{ $kinds[$message->kind] ?? $message->kind }}</td>
                            <td>{{ $message->invoice?->student?->fullName() ?? '—' }}</td>
                            <td>{{ $message->guardian?->name ?? '—' }}</td>
                            <td><span class="badge {{ $badges[$message->status] ?? 'bg-secondary' }}">{{ __('communication.statuses.'.$message->status) }}</span></td>
                            <td class="text-nowrap">{{ $message->sent_at?->format('d M Y H:i') ?? '—' }}</td>
                            @php($reason = $message->error ? __('communication.reasons.'.$message->error) : null)
                            @php($reason = $message->error && $reason === 'communication.reasons.'.$message->error ? $message->error : $reason)
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

    @if ($messages->count() === 200)
        <p class="text-muted fs-13 mt-3">{{ __('communication.messages.limited') }}</p>
    @endif
</div>
@endsection
