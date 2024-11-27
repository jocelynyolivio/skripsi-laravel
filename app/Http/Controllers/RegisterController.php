<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function index(){
        return view('auth.patient-register',[
            'title' => 'register',
            'active' => 'register'
        ]);
    }

    public function store(Request $request){
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email:dns|unique:patients',
            'password' => 'required|min:5|max:255',
        ]);

        $validatedData['password'] = bcrypt($validatedData['password']);

        $patient = Patient::create($validatedData);

        // Kirim email verifikasi
        $patient->sendEmailVerificationNotification();

        return redirect('/patient/register')->with('success', 'Registration successful. Please check your email to verify your account.');
    }
}
