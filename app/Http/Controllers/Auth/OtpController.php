<?php

namespace App\Http\Controllers\Auth;

use Inertia\Inertia;

class OtpController
{
    public function index()
    {
        // dd(session('email'));
        return Inertia::render('Auth/Otp', [
            'email' => session('email'),
        ]);
    }
}