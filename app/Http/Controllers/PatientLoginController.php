<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.patient-login', [
            'title' => 'Patient Login',
            'active' => 'login'
        ]);
    }

    public function login(Request $request)
{
    // dd('asas');
    // Validasi input
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    // Coba login menggunakan guard 'patient'
    if (Auth::guard('patient')->attempt($credentials)) {
        $user = Auth::guard('patient')->user();

        // Cek apakah email sudah diverifikasi
        if (is_null($user->email_verified_at)) {
            Auth::guard('patient')->logout();
            return back()->with('loginError', 'Please verify your email before logging in.');
        }

        // Login berhasil dan email terverifikasi
        $request->session()->regenerate();
        return redirect()->intended('/');
    }

    // Login gagal
    return back()->with('loginError', 'Login Failed');
}


    public function logout(Request $request)
    {
        Auth::guard('patient')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
