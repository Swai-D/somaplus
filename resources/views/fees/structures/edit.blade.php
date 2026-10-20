@extends('layouts.app')

@section('title', __('fees.structures.edit'))

@section('page')
    <div class="mb-3">
        <a href="{{ route('fees.structures.show', $structure) }}" class="text-muted"><i class="ti ti-arrow-left me-1"></i>{{ $structure->name }}</a>
        <h3 class="page-title mt-2">{{ __('fees.structures.edit') }}</h3>
    </div>
    @if ($used)
        <div class="alert alert-warning">{{ __('fees.structures.used') }}</div>
    @endif
    <div class="card"><div class="card-body">@include('fees.structures._form')</div></div>
@endsection
