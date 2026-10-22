<?php

namespace App\Http\Controllers\Settings;

use App\Domain\Settings\RoleCatalog;
use App\Domain\Settings\SaveSchoolUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\SchoolUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/** The people who sign in to the school and the role each one has. */
class UserController extends Controller
{
    public function index(): View
    {
        Gate::authorize('settings.manage');

        return view('settings.users.index', ['users' => User::query()->with('roles')->orderBy('name')->get()]);
    }

    public function create(): View
    {
        Gate::authorize('settings.manage');

        return view('settings.users.create', ['user' => null, 'roles' => RoleCatalog::all()]);
    }

    public function store(SchoolUserRequest $request, SaveSchoolUser $save): RedirectResponse
    {
        /** @var array{name: string, email: string, role: string, password?: string|null} $data */
        $data = $request->validated();
        $save->handle(null, $data);

        return redirect()->route('settings.users.index')->with('status', __('custom_roles.users.saved'));
    }

    public function edit(User $user): View
    {
        Gate::authorize('settings.manage');

        return view('settings.users.edit', ['user' => $user, 'roles' => RoleCatalog::all()]);
    }

    public function update(SchoolUserRequest $request, User $user, SaveSchoolUser $save): RedirectResponse
    {
        /** @var array{name: string, email: string, role: string, password?: string|null} $data */
        $data = $request->validated();
        $save->handle($user, $data);

        return redirect()->route('settings.users.index')->with('status', __('custom_roles.users.saved'));
    }
}
