<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        return view('login.index', [
            'title' => 'login',
            'active' => 'login'
        ]);
    }

    public function authenticate(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Login untuk pasien
        if (Auth::guard('patient')->attempt($credentials)) {
            $patient = Auth::guard('patient')->user();

            // Periksa apakah email sudah diverifikasi
            if (is_null($patient->email_verified_at)) {
                Auth::guard('patient')->logout();
                return back()->with('loginError', 'Please verify your email before logging in.');
            }

            $request->session()->regenerate();
            return redirect()->intended('/patient/dashboard'); // Sesuaikan halaman tujuan pasien
        }

        // Login untuk user (admin, dokter, manager)
        if (Auth::guard('web')->attempt($credentials)) {
            $user = Auth::guard('web')->user();

            $request->session()->regenerate();

            // Redirect berdasarkan role
            if ($user->role_id == 1) {
                return redirect('/dashboard'); // Admin
            } elseif ($user->role_id == 2) {
                return redirect('/dashboard'); // Dokter
            } elseif ($user->role_id == 3) {
                return redirect('/dashboard'); // Manager
            } else {
                return redirect('/dashboard'); // Default
            }
        }

        // Jika login gagal
        return back()->with('loginError', 'The provided credentials do not match our records.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        // Supaya session tidak bisa digunakan
        $request->session()->invalidate();

        // Regenerate token untuk keamanan
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
