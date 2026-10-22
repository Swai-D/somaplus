@extends('layouts.app')

@section('title', __('menu.subjects'))

@section('page')
    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
        <div>
            <h3 class="page-title mb-1">{{ __('menu.subjects') }}</h3>
            <p class="text-muted mb-0">{{ __('academics.subjects.intro') }}</p>
        </div>
        @can('create', App\Models\Subject::class)
            <button type="button" class="btn btn-primary mt-2 mt-md-0" data-bs-target="#subject-modal" data-bs-toggle="modal">
                <i class="ti ti-circle-plus me-1"></i>{{ __('academics.subjects.add') }}
            </button>
        @endcan
    </div>

    <div class="card">
        <div class="card-body">
            @if ($subjects->isEmpty())
                <p class="text-muted mb-0">{{ __('academics.subjects.empty') }}</p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('academics.subjects.name') }}</th>
                                <th>{{ __('academics.subjects.code') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($subjects as $subject)
                                <tr>
                                    <td class="fw-medium">{{ $subject->name }}</td>
                                    <td>{{ $subject->code }}</td>
                                    <td class="text-end">
                                        @can('update', $subject)
                                            <a href="{{ route('academics.subjects.edit', $subject) }}" class="btn btn-sm btn-outline-secondary">{{ __('academics.edit') }}</a>
                                        @endcan
                                        @can('delete', $subject)
                                            @include('academics._delete', ['action' => route('academics.subjects.destroy', $subject)])
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

    @can('create', App\Models\Subject::class)
        <x-form-modal id="subject-modal" title="{{ __('academics.subjects.add') }}" action="{{ route('academics.subjects.store') }}" size="modal-lg">
            @include('academics.subjects._fields', ['subject' => null])
        </x-form-modal>
    @endcan
@endsection
