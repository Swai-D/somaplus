<?php

namespace App\Http\Controllers\Settings;

use App\Domain\Settings\DeleteCustomRole;
use App\Domain\Settings\RoleCatalog;
use App\Domain\Settings\SaveCustomRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\RoleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

/** Custom roles of a school (Enterprise plan). The five default roles are listed but never changed. */
class RoleController extends Controller
{
    public function index(): View
    {
        Gate::authorize('settings.manage');

        return view('settings.roles.index', [
            'roles' => RoleCatalog::all(),
            'holders' => RoleCatalog::holders(),
        ] + $this->form(null));
    }

    /**
     * The form of a new role is a pop-up of the list, as in the template, so this address only
     * opens the list with the pop-up ready.
     */
    public function create(): RedirectResponse
    {
        Gate::authorize('settings.manage');

        return redirect()->route('settings.roles.index', ['add' => 'role-modal']);
    }

    public function store(RoleRequest $request, SaveCustomRole $save): RedirectResponse
    {
        /** @var array{name: string, permissions: array<int, string>} $data */
        $data = $request->validated();
        $save->handle(null, $data);

        return redirect()->route('settings.roles.index')->with('status', __('custom_roles.saved'));
    }

    public function edit(string $role): View
    {
        Gate::authorize('settings.manage');

        return view('settings.roles.edit', $this->form(RoleCatalog::findCustom($role) ?? abort(404)));
    }

    public function update(RoleRequest $request, string $role, SaveCustomRole $save): RedirectResponse
    {
        /** @var array{name: string, permissions: array<int, string>} $data */
        $data = $request->validated();
        $save->handle(RoleCatalog::findCustom($role) ?? abort(404), $data);

        return redirect()->route('settings.roles.index')->with('status', __('custom_roles.saved'));
    }

    public function destroy(string $role, DeleteCustomRole $delete): RedirectResponse
    {
        Gate::authorize('settings.manage');

        $delete->handle(RoleCatalog::findCustom($role) ?? abort(404));

        return redirect()->route('settings.roles.index')->with('status', __('custom_roles.deleted'));
    }

    /**
     * @return array<string, mixed>
     */
    private function form(?Role $role): array
    {
        return [
            'role' => $role,
            'groups' => RoleCatalog::groups(),
            'implies' => (array) config('roles.implies', []),
            'copyFrom' => RoleCatalog::all(),
            'chosen' => $role?->permissions->pluck('name')->all() ?? [],
        ];
    }
}
