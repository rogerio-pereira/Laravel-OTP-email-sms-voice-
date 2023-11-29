<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\LoginOtpRequest;
use Illuminate\Http\RedirectResponse;
use App\Providers\RouteServiceProvider;
use Inertia\Inertia;

class OtpController
{
    public function index()
    {
        return Inertia::render('Auth/Otp', [
            'email' => session('email'),
        ]);
    }

    public function login(LoginOtpRequest $request): RedirectResponse
    {
        //$request->authenticate is inside LoginRequest
        if($request->authenticate()) {
            $request->session()->regenerate();

            return redirect()->intended(RouteServiceProvider::HOME);
        }
        else {
            return redirect()
                        ->back()
                        ->with([
                            'email' => $request->email
                        ])
                        ->withMessages([
                            'otp' => 'Invalid OTP code',
                        ]);
        }
    }
}