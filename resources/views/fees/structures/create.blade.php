@extends('layouts.app')

@section('title', __('fees.structures.add'))

@section('page')
    <div class="mb-3">
        <a href="{{ route('fees.structures.index') }}" class="text-muted"><i class="ti ti-arrow-left me-1"></i>{{ __('menu.fee_structures') }}</a>
        <h3 class="page-title mt-2">{{ __('fees.structures.add') }}</h3>
    </div>
    <div class="card"><div class="card-body">@include('fees.structures._form', ['structure' => null])</div></div>
@endsection
