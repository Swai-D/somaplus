@extends('layouts.app')

@section('title', __('menu.classes'))

@section('page')
    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
        <div>
            <h3 class="page-title mb-1">{{ __('menu.classes') }}</h3>
            <p class="text-muted mb-0">{{ __('academics.classes.intro') }}</p>
        </div>
        @can('create', App\Models\SchoolClass::class)
            <button type="button" class="btn btn-primary mt-2 mt-md-0" data-bs-target="#class-modal" data-bs-toggle="modal">
                <i class="ti ti-circle-plus me-1"></i>{{ __('academics.classes.add') }}
            </button>
        @endcan
    </div>

    <div class="card">
        <div class="card-body">
            @if ($classes->isEmpty())
                <p class="text-muted mb-0">{{ __('academics.classes.empty') }}</p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('academics.classes.name') }}</th>
                                <th>{{ __('academics.classes.streams') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($classes as $class)
                                <tr>
                                    <td><a href="{{ route('academics.classes.show', $class) }}" class="fw-medium">{{ $class->name }}</a></td>
                                    <td>{{ $class->streams_count }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('academics.classes.show', $class) }}" class="btn btn-sm btn-outline-primary">{{ __('academics.open') }}</a>
                                        @can('update', $class)
                                            <a href="{{ route('academics.classes.edit', $class) }}" class="btn btn-sm btn-outline-secondary">{{ __('academics.edit') }}</a>
                                        @endcan
                                        @can('delete', $class)
                                            @include('academics._delete', ['action' => route('academics.classes.destroy', $class)])
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    @can('create', App\Models\SchoolClass::class)
        <x-form-modal id="class-modal" title="{{ __('academics.classes.add') }}" action="{{ route('academics.classes.store') }}" size="modal-lg">
            @include('academics.classes._fields', ['class' => null])
        </x-form-modal>
    @endcan
@endsection
