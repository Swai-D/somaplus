<?php

namespace App\Http\Controllers;

use App\Domain\Settings\BuildDashboardSummary;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, BuildDashboardSummary $summary): View
    {
        /** @var User $user */
        $user = $request->user();

        return view('dashboard', ['summary' => $summary->handle($user)]);
    }
}
