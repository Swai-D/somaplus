@extends('layouts.app')

@section('title', __('custom_roles.title'))

@section('page')
    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3 gap-2">
        <div>
            <h3 class="page-title mb-1">{{ __('custom_roles.title') }}</h3>
            <p class="text-muted mb-0">{{ __('custom_roles.intro') }}</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-target="#role-modal" data-bs-toggle="modal">
            <i class="ti ti-circle-plus me-1"></i>{{ __('custom_roles.add') }}
        </button>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">@foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('custom_roles.name') }}</th>
                            <th>{{ __('custom_roles.type') }}</th>
                            <th>{{ __('custom_roles.people') }}</th>
                            <th>{{ __('custom_roles.permissions') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $role)
                            @php($default = App\Domain\Settings\RoleCatalog::isDefault($role->name))
                            <tr>
                                <td class="fw-medium">{{ App\Domain\Settings\RoleCatalog::label($role->name) }}</td>
                                <td><span class="badge {{ $default ? 'bg-light text-dark' : 'bg-success' }}">{{ $default ? __('custom_roles.default') : __('custom_roles.custom') }}</span></td>
                                <td>{{ $holders[$role->id] ?? 0 }}</td>
                                <td>{{ __('custom_roles.permissions_count', ['count' => $role->permissions->count()]) }}</td>
                                <td class="text-end">
                                    @unless ($default)
                                        <a href="{{ route('settings.roles.edit', $role->id) }}" class="btn btn-sm btn-outline-secondary">{{ __('custom_roles.edit') }}</a>
                                        <form action="{{ route('settings.roles.destroy', $role->id) }}" method="POST" class="d-inline" onsubmit="return confirm(@js(__('custom_roles.confirm_delete')))">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('custom_roles.delete') }}</button>
                                        </form>
                                    @endunless
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-muted fs-13">{{ __('custom_roles.default_locked') }}</div>
    </div>

    @can('settings.manage')
        <x-form-modal id="role-modal" title="{{ __('custom_roles.add') }}" action="{{ route('settings.roles.store') }}" size="modal-xl">
            @include('settings.roles._fields', ['role' => null, 'groups' => $groups, 'implies' => $implies, 'copyFrom' => $copyFrom, 'chosen' => []])
        </x-form-modal>
    @endcan
@endsection
