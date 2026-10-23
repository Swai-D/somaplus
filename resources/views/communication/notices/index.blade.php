@extends('layouts.app')

@section('title', __('menu.notices'))

@section('page')
    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
        <div>
            <h3 class="page-title mb-1">{{ __('menu.notice_board') }}</h3>
            <p class="text-muted mb-0">{{ __('communication.notices.intro') }}</p>
        </div>
        @can('create', App\Models\Notice::class)
            <button type="button" class="btn btn-primary mt-2 mt-md-0" data-bs-target="#notice-modal" data-bs-toggle="modal">
                <i class="ti ti-circle-plus me-1"></i>{{ __('communication.notices.add') }}
            </button>
        @endcan
    </div>

    @if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="datatable table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('communication.notices.day') }}</th>
                            <th>{{ __('communication.notices.title') }}</th>
                            <th>{{ __('communication.notices.description') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($notices as $notice)
                            <tr>
                                <td class="text-muted">{{ $loop->iteration }}</td>
                                <td class="text-nowrap">{{ $notice->day->toDateString() }}</td>
                                <td class="fw-medium">{{ $notice->title }}</td>
                                <td>{{ $notice->description ? \Illuminate\Support\Str::limit($notice->description, 120) : '—' }}</td>
                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        @can('update', $notice)
                                            <a href="{{ route('communication.notices.edit', $notice) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('communication.notices.edit') }}"><i class="ti ti-edit"></i></a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">{{ __('communication.notices.empty') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @can('create', App\Models\Notice::class)
        <x-form-modal id="notice-modal" title="{{ __('communication.notices.add') }}" action="{{ route('communication.notices.store') }}">
            @include('communication.notices._fields', ['notice' => null])
        </x-form-modal>
    @endcan
@endsection
