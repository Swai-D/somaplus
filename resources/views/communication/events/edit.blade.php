@extends('layouts.app')

@section('title', __('communication.events.edit'))

@section('page')
    <div class="mb-3">
        <a href="{{ route('communication.events.index', ['month' => $event->starts_at->format('Y-m')]) }}" class="text-muted"><i class="ti ti-arrow-left me-1"></i>{{ __('communication.events.back') }}</a>
        <h3 class="page-title mt-2">{{ __('communication.events.edit') }}</h3>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('communication.events.update', $event) }}" method="POST">
                @csrf
                @method('PUT')
                @include('communication.events._fields', ['event' => $event, 'prefill' => null])
                <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>{{ __('app.save') }}</button>
                <a href="{{ route('communication.events.index', ['month' => $event->starts_at->format('Y-m')]) }}" class="btn btn-outline-secondary">{{ __('app.cancel') }}</a>
            </form>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body d-flex align-items-center justify-content-between">
            <span>{{ __('communication.events.confirm_delete') }}</span>
            <form action="{{ route('communication.events.destroy', $event) }}" method="POST"
                onsubmit="return confirm(@js(__('communication.events.confirm_delete')))">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger"><i class="ti ti-trash me-1"></i>{{ __('communication.events.delete') }}</button>
            </form>
        </div>
    </div>
@endsection
