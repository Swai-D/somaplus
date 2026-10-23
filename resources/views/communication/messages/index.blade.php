@extends('layouts.app')

@section('title', __('menu.messages'))

@section('page')
    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
        <div>
            <h3 class="page-title mb-1">{{ __('menu.messages') }}</h3>
            <p class="text-muted mb-0">{{ __('communication.messages.intro') }}</p>
        </div>
        <a href="{{ route('communication.reminders.index') }}" class="btn btn-outline-primary mt-2 mt-md-0">
            <i class="ti ti-bell me-1"></i>{{ __('menu.fee_reminders') }}
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <form action="{{ route('communication.messages.index') }}" method="GET" class="row align-items-end g-2">
                <div class="col-md-3">
                    <label class="form-label" for="status">{{ __('communication.messages.status') }}</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">{{ __('communication.messages.all') }}</option>
                        @foreach (['queued', 'sent', 'failed', 'cancelled'] as $value)
                            <option value="{{ $value }}" @selected($status === $value)>{{ __('communication.statuses.'.$value) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="kind">{{ __('communication.messages.kind') }}</label>
                    <select class="form-select" id="kind" name="kind">
                        <option value="">{{ __('communication.messages.all') }}</option>
                        @foreach ($kinds as $value => $label)
                            <option value="{{ $value }}" @selected($kind === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary w-100">{{ __('communication.messages.show') }}</button>
                </div>
            </form>
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
            @if ($messages->count() === 200)
                <p class="text-muted px-3 py-2 mb-0">{{ __('communication.messages.limited') }}</p>
            @endif
        </div>
    </div>
@endsection
