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
        // dd('hai');
        $validatedData = $request->validate([
            'fname' => 'required|max:255',
            'mname' => 'nullable|max:255',
            'lname' => 'nullable|max:255',
            'email' => 'required|email:dns|unique:patients',
            'password' => 'required|min:5|max:255',
            'home_mobile' => 'required|max:255'
        ]);

        // dd($validatedData);

        $validatedData['password'] = bcrypt($validatedData['password']);

        // Generate Patient ID
        $initialLetter = strtoupper(substr($request->fname, 0, 1)); // Ambil huruf pertama dari nama
        $lastPatient = Patient::where('patient_id', 'like', "$initialLetter%")->orderBy('id', 'desc')->first();

        if ($lastPatient) {
            $lastNumber = (int) substr($lastPatient->patient_id, 1); // Ambil angka setelah huruf
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT); // Format angka jadi 3 digit
        } else {
            $newNumber = '001';
        }

        $validatedData['patient_id'] = $initialLetter . $newNumber; // Contoh: Y001, A002

        $patient = Patient::create($validatedData);

        // dd('masuk database');
        // Kirim email verifikasi
        $patient->sendEmailVerificationNotification();
        // dd('verif');
        return redirect('/patient/register')->with('success', 'Registration successful. Please check your email to verify your account.');
    }
}
