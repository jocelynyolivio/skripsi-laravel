<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class PatientVerifyEmailController extends Controller
{
    public function __invoke(EmailVerificationRequest $request)
    {
        if ($request->user('patient')->hasVerifiedEmail()) {
            return redirect('/patient/login'); // Redirect ke halaman login pasien
        }

        if ($request->user('patient')->markEmailAsVerified()) {
            event(new Verified($request->user('patient')));
        }

        return redirect('/patient/login')->with('success', 'Email verification successful. Please log in.');
    }
}

