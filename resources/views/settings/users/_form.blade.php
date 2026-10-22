{{-- Variables: user (or null), roles. --}}
<form method="POST" action="{{ $user ? route('settings.users.update', $user->id) : route('settings.users.store') }}">
    @csrf
    @if ($user)
        @method('PUT')
    @endif

    <div class="card">
        <div class="card-body">
            @include('academics._field', ['name' => 'name', 'label' => __('custom_roles.users.name'), 'value' => $user?->name ?? ''])
            @include('academics._field', ['name' => 'email', 'label' => __('custom_roles.users.email'), 'type' => 'email', 'value' => $user?->email ?? ''])

            <div class="mb-3">
                <label class="form-label" for="role">{{ __('custom_roles.users.role') }}<span class="text-danger"> *</span></label>
                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                    @foreach ($roles as $role)
                        <option value="{{ $role->name }}" @selected(old('role', $user?->getRoleNames()->first() ?? 'teacher') === $role->name)>{{ App\Domain\Settings\RoleCatalog::label($role->name) }}</option>
                    @endforeach
                </select>
                @error('role')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="mb-0">
                <label class="form-label" for="password">{{ __('custom_roles.users.password') }}@unless ($user)<span class="text-danger"> *</span>@endunless</label>
                <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" autocomplete="new-password" @unless ($user) required @endunless>
                <div class="form-text">{{ $user ? __('custom_roles.users.password_keep') : __('custom_roles.users.password_hint') }}</div>
                @error('password')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>{{ __('custom_roles.save') }}</button>
    <a href="{{ route('settings.users.index') }}" class="btn btn-outline-secondary">{{ __('custom_roles.cancel') }}</a>
</form>
