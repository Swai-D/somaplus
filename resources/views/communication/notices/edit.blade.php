@extends('layouts.app')

@section('title', __('communication.notices.edit'))

@section('page')
    <div class="mb-3">
        <a href="{{ route('communication.notices.index') }}" class="text-muted"><i class="ti ti-arrow-left me-1"></i>{{ __('communication.notices.back') }}</a>
        <h3 class="page-title mt-2">{{ __('communication.notices.edit') }}</h3>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('communication.notices.update', $notice) }}" method="POST">
                @csrf
                @method('PUT')
                @include('communication.notices._fields', ['notice' => $notice])
                <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>{{ __('app.save') }}</button>
                <a href="{{ route('communication.notices.index') }}" class="btn btn-outline-secondary">{{ __('app.cancel') }}</a>
            </form>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body d-flex align-items-center justify-content-between">
            <span>{{ __('communication.notices.confirm_delete') }}</span>
            <form action="{{ route('communication.notices.destroy', $notice) }}" method="POST"
                onsubmit="return confirm(@js(__('communication.notices.confirm_delete')))">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger"><i class="ti ti-trash me-1"></i>{{ __('communication.notices.delete') }}</button>
            </form>
        </div>
    </div>
@endsection
