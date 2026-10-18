@extends('layouts.app')

@section('title', __('academics.streams.edit'))

@section('page')
    <div class="mb-3">
        <a href="{{ route('academics.classes.show', $class) }}" class="text-muted"><i class="ti ti-arrow-left me-1"></i>{{ $class->name }}</a>
        <h3 class="page-title mt-2">{{ __('academics.streams.edit') }}</h3>
    </div>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('academics.streams.update', $stream) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">@include('academics._field', ['name' => 'name', 'label' => __('academics.streams.name'), 'value' => $stream->name])</div>
                </div>
                <button type="submit" class="btn btn-primary">{{ __('academics.save') }}</button>
            </form>
        </div>
    </div>
@endsection
