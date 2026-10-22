<?php

namespace App\Http\Controllers\Students;

use App\Domain\Family\GrantPortalAccess;
use App\Domain\Family\RevokePortalAccess;
use App\Http\Controllers\Controller;
use App\Models\Guardian;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

/** Gives a guardian an account for the family pages, or takes it away (owner only, see GuardianPolicy::managePortal). */
class PortalAccessController extends Controller
{
    public function store(Guardian $guardian, GrantPortalAccess $grant): RedirectResponse
    {
        Gate::authorize('managePortal', $guardian);

        $sent = $grant->handle($guardian);

        return redirect()->route('guardians.show', $guardian)->with('status', __($sent ? 'family.granted_sent' : 'family.granted_not_sent'));
    }

    public function destroy(Guardian $guardian, RevokePortalAccess $revoke): RedirectResponse
    {
        Gate::authorize('managePortal', $guardian);

        $revoke->handle($guardian);

        return redirect()->route('guardians.show', $guardian)->with('status', __('family.revoked'));
    }
}
