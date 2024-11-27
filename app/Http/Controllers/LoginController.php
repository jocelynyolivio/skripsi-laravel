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
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::guard('patient')->attempt($credentials)) {
            $patient = Auth::guard('patient')->user();
        
            if (is_null($patient->email_verified_at)) {
                Auth::guard('patient')->logout();
                return back()->with('loginError', 'Please verify your email before logging in.');
            }
        
            $request->session()->regenerate();
            return redirect()->intended('/');
        }
        if (Auth::guard('patient')->attempt($credentials)) {
            $patient = Auth::guard('patient')->user();
        
            if (is_null($patient->email_verified_at)) {
                Auth::guard('patient')->logout();
                return back()->with('loginError', 'Please verify your email before logging in.');
            }
        
            $request->session()->regenerate();
            return redirect()->intended('/');
        }
        }        

    public function logout(Request $request)
    {
        Auth::logout();

        // supaya gabisa dipake
        $request->session()->invalidate();

        // bikin token baru supaya gak di bajak
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
