<?php

namespace App\Http\Controllers;

use App\Domain\Settings\RoleCatalog;
use App\Domain\Settings\TeacherDashboardFigures;
use App\Models\User;
use App\Tenancy\TenantContext;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeacherDashboardController extends Controller
{
    public function __invoke(Request $request, TeacherDashboardFigures $figures): View
    {
        $school = app(TenantContext::class)->current();
        abort_if($school === null, 404);

        /** @var User $user */
        $user = $request->user();

        return view('dashboard.teacher', [
            'user' => $user,
            'school' => $school,
            'figures' => $figures->handle($user),
            'roles' => $user->getRoleNames()->map(fn ($role) => RoleCatalog::label($role))->values()->all(),
        ]);
    }
}
