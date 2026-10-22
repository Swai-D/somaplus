<?php

namespace App\Http\Controllers;

use App\Domain\Settings\BuildDashboardSummary;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, BuildDashboardSummary $summary): View|RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        // A parent has no school dashboard: their home is the page of their children.
        if ($user->can('family.portal')) {
            return redirect()->route('family.index');
        }

        return view('dashboard', ['user' => $user, 'summary' => $summary->handle($user)]);
    }
}
