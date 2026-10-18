<?php

namespace App\Http\Controllers\Auth;

use App\Domain\Auth\ResetPassword;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    public function create(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->string('email')->toString(),
        ]);
    }

    public function store(ResetPasswordRequest $request, ResetPassword $reset): RedirectResponse
    {
        $status = $reset->handle(
            $request->string('email')->toString(),
            $request->string('token')->toString(),
            $request->string('password')->toString(),
        );

        if ($status !== Password::PASSWORD_RESET) {
            // One message for a wrong address and a wrong or expired link alike.
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __('passwords.invalid')]);
        }

        return redirect()->route('login')->with('status', __('passwords.reset'));
    }
}
