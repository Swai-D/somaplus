@extends('layouts.app')

@section('title', $class->name)

@section('page')
    <div class="mb-3">
        <a href="{{ route('academics.classes.index') }}" class="text-muted"><i class="ti ti-arrow-left me-1"></i>{{ __('menu.classes') }}</a>
        <h3 class="page-title mt-2">{{ $class->name }}</h3>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="mb-3">{{ __('academics.classes.streams') }}</h5>
            @if ($streams->isEmpty())
                <p class="text-muted mb-0">{{ __('academics.streams.empty') }}</p>
            @else
                <ul class="list-group">
                    @foreach ($streams as $stream)
                        <li class="list-group-item d-flex align-items-center justify-content-between">
                            <span class="fw-medium">{{ $stream->name }}</span>
                            <span>
                                @can('update', $stream)
                                    <a href="{{ route('academics.streams.edit', $stream) }}" class="btn btn-sm btn-outline-secondary">{{ __('academics.edit') }}</a>
                                @endcan
                                @can('delete', $stream)
                                    @include('academics._delete', ['action' => route('academics.streams.destroy', $stream)])
                                @endcan
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    @can('create', App\Models\Stream::class)
        <div class="card">
            <div class="card-body">
                <h5 class="mb-3">{{ __('academics.streams.add') }}</h5>
                <form action="{{ route('academics.streams.store', $class) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">@include('academics._field', ['name' => 'name', 'label' => __('academics.streams.name'), 'hint' => __('academics.streams.name_hint')])</div>
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('academics.save') }}</button>
                </form>
            </div>
        </div>
    @endcan

    @can('update', $class)
        <a href="{{ route('academics.classes.edit', $class) }}" class="btn btn-outline-secondary">{{ __('academics.classes.edit') }}</a>
    @endcan
@endsection
