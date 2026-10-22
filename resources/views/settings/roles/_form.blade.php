{{-- Variables: role (or null), groups, implies, copyFrom, chosen. --}}
<form method="POST" action="{{ $role ? route('settings.roles.update', $role->id) : route('settings.roles.store') }}" id="role-form">
    @csrf
    @if ($role)
        @method('PUT')
    @endif

    @include('settings.roles._fields', ['role' => $role, 'groups' => $groups, 'implies' => $implies, 'copyFrom' => $copyFrom, 'chosen' => $chosen])

    <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>{{ __('custom_roles.save') }}</button>
    <a href="{{ route('settings.roles.index') }}" class="btn btn-outline-secondary">{{ __('custom_roles.cancel') }}</a>
</form>
