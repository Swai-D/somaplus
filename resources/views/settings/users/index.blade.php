@extends('layouts.app')

@section('title', __('custom_roles.users.title'))

@section('page')
    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3 gap-2">
        <div>
            <h3 class="page-title mb-1">{{ __('custom_roles.users.title') }}</h3>
            <p class="text-muted mb-0">{{ __('custom_roles.users.intro') }}</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-target="#user-modal" data-bs-toggle="modal">
            <i class="ti ti-circle-plus me-1"></i>{{ __('custom_roles.users.add') }}
        </button>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>{{ __('custom_roles.users.name') }}</th><th>{{ __('custom_roles.users.email') }}</th><th>{{ __('custom_roles.users.role') }}</th><th></th></tr></thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td class="fw-medium">{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>@foreach ($user->getRoleNames() as $name)<span class="badge bg-light text-dark">{{ App\Domain\Settings\RoleCatalog::label($name) }}</span> @endforeach</td>
                                <td class="text-end"><a href="{{ route('settings.users.edit', $user->id) }}" class="btn btn-sm btn-outline-secondary">{{ __('custom_roles.users.edit') }}</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-muted">{{ __('custom_roles.users.empty') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <x-form-modal id="user-modal" title="{{ __('custom_roles.users.add') }}" action="{{ route('settings.users.store') }}" size="modal-lg">
        @include('settings.users._fields', ['user' => null, 'roles' => $roles])
    </x-form-modal>
@endsection
