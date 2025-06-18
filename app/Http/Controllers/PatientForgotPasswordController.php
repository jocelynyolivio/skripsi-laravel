<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Ganti baris ini:
// use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

// Menjadi ini:
use Illuminate\Support\Facades\Mail;
use App\Mail\PatientResetPasswordMail;
use Illuminate\Support\Facades\Password; // Tambahkan ini juga untuk broker
use Illuminate\Auth\Passwords\PasswordBroker; // Ini adalah trait yang benar

class PatientForgotPasswordController extends Controller
{
    // Hapus baris ini:
    // use SendsPasswordResetEmails;

    // Dan tambahkan method ini untuk menggunakan trait yang baru
    public function sendResetLinkEmail(Request $request)
    {
        $this->validate($request, ['email' => 'required|email']);

        // Dapatkan broker password untuk guard 'patient'
        $response = Password::broker('patients')->sendResetLink(
            $request->only('email'),
            function ($user, $token) {
                // Kirim email kustom Anda di sini
                // Pastikan user object adalah instance dari model Patient Anda
                Mail::to($user->email)->send(new PatientResetPasswordMail($user, $token));
            }
        );

        return $response == Password::RESET_LINK_SENT
                    ? $this->sendResetLinkResponse($request, $response)
                    : $this->sendResetLinkFailedResponse($request, $response);
    }


    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        return view('auth.patient-forgot-password', [
            'title' => 'Forgot Password',
            'active' => 'forgot-password'
        ]);
    }

    /**
     * Get the response for a successful password reset link.
     *
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkResponse(Request $request, $response)
    {
        return back()->with('status', trans($response));
    }

    /**
     * Get the response for a failed password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return back()->withErrors(['email' => trans($response)]);
    }
}