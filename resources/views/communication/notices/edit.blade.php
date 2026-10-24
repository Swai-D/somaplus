@extends('layouts.app')

@section('title', __('communication.notices.edit'))

@section('page')
<div class="page-header">
    <div class="add-item d-flex">
        <div class="page-title">
            <h4>{{ __('communication.notices.edit') }}</h4>
            <h6>{{ __('menu.sections.communication') }} / {{ __('menu.notice_board') }}</h6>
        </div>
    </div>
    <ul class="table-top-head">
        <li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" href="{{ route('communication.notices.index') }}" title="{{ __('communication.notices.back') }}"><i class="ti ti-arrow-back"></i></a></li>
    </ul>
</div>

<div class="mt-4">
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

    <div class="card mt-4">
        <div class="card-body p-4 text-center">
            <span class="rounded-circle d-inline-flex p-3 bg-danger-transparent mb-2"><i class="ti ti-trash fs-24 text-danger"></i></span>
            <h4 class="fs-20 fw-bold mb-2">{{ __('communication.notices.delete') }}</h4>
            <p class="mb-3">{{ __('communication.notices.confirm_delete') }}</p>
            <form action="{{ route('communication.notices.destroy', $notice) }}" method="POST"
                onsubmit="return confirm(@js(__('communication.notices.confirm_delete')));">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger"><i class="ti ti-trash me-1"></i>{{ __('communication.notices.delete') }}</button>
            </form>
        </div>
    </div>
</div>
@endsection
