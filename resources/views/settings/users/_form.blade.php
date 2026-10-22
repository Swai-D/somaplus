{{-- Variables: user (or null), roles. --}}
<form method="POST" action="{{ $user ? route('settings.users.update', $user->id) : route('settings.users.store') }}">
    @csrf
    @if ($user)
        @method('PUT')
    @endif

    <div class="card">
        <div class="card-body">
            @include('settings.users._fields', ['user' => $user, 'roles' => $roles])
        </div>
    </div>

    <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>{{ __('custom_roles.save') }}</button>
    <a href="{{ route('settings.users.index') }}" class="btn btn-outline-secondary">{{ __('custom_roles.cancel') }}</a>
</form>
