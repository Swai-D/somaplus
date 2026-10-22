{{-- Variables: role (or null), groups, implies, copyFrom, chosen. --}}
@php($ticked = old('permissions', $chosen))
@if ($errors->any())
    <div class="alert alert-danger" role="alert"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label" for="name">{{ __('custom_roles.name') }}<span class="text-danger"> *</span></label>
        <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" maxlength="60" value="{{ old('name', $role?->name) }}" required>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label" for="copy">{{ __('custom_roles.copy_from') }}</label>
        <select class="form-select" id="copy">
            <option value="">{{ __('custom_roles.copy_none') }}</option>
            @foreach ($copyFrom as $source)
                <option value="{{ $source->id }}" data-permissions='@json($source->permissions->pluck('name')->values())'>{{ App\Domain\Settings\RoleCatalog::label($source->name) }}</option>
            @endforeach
        </select>
    </div>
</div>

<p class="fw-semibold mb-1">{{ __('custom_roles.pick') }}</p>
<p class="text-muted fs-13">{{ __('custom_roles.pick_hint') }}</p>

<div class="row">
    @foreach ($groups as $area => $permissions)
        <div class="col-md-6 mb-3">
            <div class="border rounded p-3 h-100">
                <div class="fw-semibold mb-2">{{ __('custom_roles.area.'.$area) }}</div>
                @foreach ($permissions as $permission)
                    @php($parts = explode('.', $permission, 2))
                    @php($id = 'perm-'.str_replace('.', '-', $permission))
                    <div class="form-check mb-1">
                        <input class="form-check-input js-permission" type="checkbox" name="permissions[]" value="{{ $permission }}" id="{{ $id }}" data-implies='@json($implies[$permission] ?? [])' @checked(in_array($permission, $ticked, true))>
                        <label class="form-check-label" for="{{ $id }}">{{ __('custom_roles.perm.'.$parts[0].'.'.$parts[1]) }}</label>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
<p class="text-muted fs-13 mb-0"><i class="ti ti-lock me-1"></i>{{ __('custom_roles.excluded') }}</p>

@push('page-scripts')
    <script>
        (function () {
            var boxes = Array.prototype.slice.call(document.querySelectorAll('.js-permission'));
            var byValue = {};
            boxes.forEach(function (b) { byValue[b.value] = b; });

            // A ticked permission keeps the ones it needs ticked (and locked); the server does the same.
            function settle() {
                var needed = {};
                boxes.forEach(function (b) { if (b.checked) JSON.parse(b.getAttribute('data-implies')).forEach(function (n) { needed[n] = true; }); });
                boxes.forEach(function (b) {
                    if (needed[b.value]) { b.checked = true; }
                    b.setAttribute('data-locked', needed[b.value] ? '1' : '');
                });
                // repeat once so a chain (a needs b needs c) is complete
                boxes.forEach(function (b) { if (b.checked) JSON.parse(b.getAttribute('data-implies')).forEach(function (n) { if (byValue[n]) byValue[n].checked = true; }); });
            }
            boxes.forEach(function (b) {
                b.addEventListener('click', function (e) {
                    if (!b.checked && b.getAttribute('data-locked') === '1') { b.checked = true; e.preventDefault(); return; }
                    settle();
                });
            });
            var copy = document.getElementById('copy');
            if (copy) {
                copy.addEventListener('change', function () {
                    var o = this.options[this.selectedIndex];
                    if (!o.value) return;
                    var names = JSON.parse(o.getAttribute('data-permissions'));
                    boxes.forEach(function (b) { b.checked = names.indexOf(b.value) !== -1; });
                    settle();
                });
            }
            settle();
        })();
    </script>
@endpush
