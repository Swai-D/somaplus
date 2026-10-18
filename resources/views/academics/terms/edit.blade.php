@extends('layouts.app')

@section('title', __('academics.terms.edit'))

@section('page')
    <div class="mb-3">
        <a href="{{ route('academics.years.show', $year) }}" class="text-muted"><i class="ti ti-arrow-left me-1"></i>{{ $year->name }}</a>
        <h3 class="page-title mt-2">{{ __('academics.terms.edit') }}</h3>
    </div>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('academics.terms.update', $term) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-4">@include('academics._field', ['name' => 'name', 'label' => __('academics.terms.name'), 'value' => $term->name])</div>
                    <div class="col-md-4">@include('academics._field', ['name' => 'starts_on', 'label' => __('academics.starts_on'), 'type' => 'date', 'value' => $term->starts_on->toDateString()])</div>
                    <div class="col-md-4">@include('academics._field', ['name' => 'ends_on', 'label' => __('academics.ends_on'), 'type' => 'date', 'value' => $term->ends_on->toDateString()])</div>
                </div>
                <button type="submit" class="btn btn-primary">{{ __('academics.save') }}</button>
            </form>
        </div>
    </div>
@endsection
