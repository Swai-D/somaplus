<?php

namespace App\Http\Controllers\Auth;

use App\Domain\Auth\AttemptLogin;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Tenancy\TenantContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(TenantContext $context): View
    {
        return view('auth.login', ['school' => $context->current()]);
    }

    public function store(LoginRequest $request, AttemptLogin $attempt): RedirectResponse
    {
        $attempt->handle(
            $request,
            $request->string('email')->toString(),
            $request->string('password')->toString(),
            $request->boolean('remember'),
        );

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        // The chosen language is not private; keep it across the sign-out.
        $locale = $request->session()->get('locale');

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($locale !== null) {
            $request->session()->put('locale', $locale);
        }

        return redirect()->route('login');
    }
}
