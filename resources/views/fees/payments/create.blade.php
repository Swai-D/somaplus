@extends('layouts.app')

@section('title', __('fees.payments.record'))

@section('page')
    <div class="mb-3">
        <a href="{{ route('fees.payments.index') }}" class="text-muted"><i class="ti ti-arrow-left me-1"></i>{{ __('menu.payments') }}</a>
        <h3 class="page-title mt-2">{{ __('fees.payments.record_by_control') }}</h3>
        <p class="text-muted mb-0">{{ __('fees.payments.record_by_control_hint') }}</p>
    </div>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('fees.payments.store') }}" method="POST">
                @csrf
                @include('fees.payments._fields', ['key' => $key, 'withControlNumber' => true])
                <button type="submit" class="btn btn-primary">{{ __('fees.payments.record_button') }}</button>
            </form>
        </div>
    </div>
@endsection
