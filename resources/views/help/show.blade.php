@extends('layouts.app')

@section('title', $title)

@section('page')
    <div class="mb-3">
        <a href="{{ route('help.index') }}" class="text-muted"><i class="ti ti-arrow-left me-1"></i>{{ __('help.back') }}</a>
        <h3 class="page-title mt-2">{{ $title }}</h3>
    </div>

    <div class="card">
        <div class="card-body">
            @foreach ($paragraphs as $paragraph)
                <p class="{{ $loop->last ? 'mb-0' : '' }}">{{ $paragraph }}</p>
            @endforeach
        </div>
    </div>
@endsection
