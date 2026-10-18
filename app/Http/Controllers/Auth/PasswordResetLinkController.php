<?php

namespace App\Http\Controllers\Auth;

use App\Domain\Auth\SendPasswordResetLink;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Tenancy\TenantContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    public function create(TenantContext $context): View
    {
        return view('auth.forgot-password', ['school' => $context->current()]);
    }

    public function store(ForgotPasswordRequest $request, SendPasswordResetLink $send): RedirectResponse
    {
        $send->handle($request->string('email')->toString());

        // The same answer whether or not the address exists, so this page cannot be
        // used to find out who has an account at the school.
        return back()->with('status', __('passwords.sent'));
    }
}
