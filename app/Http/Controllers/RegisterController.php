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
            'nomor_telepon' => 'required|max:255'
        ]);

        $validatedData['password'] = bcrypt($validatedData['password']);

        // Generate Patient ID
        $initialLetter = strtoupper(substr($request->name, 0, 1)); // Ambil huruf pertama dari nama
        $lastPatient = Patient::where('patient_id', 'like', "$initialLetter%")->orderBy('id', 'desc')->first();

        if ($lastPatient) {
            $lastNumber = (int) substr($lastPatient->patient_id, 1); // Ambil angka setelah huruf
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT); // Format angka jadi 3 digit
        } else {
            $newNumber = '001';
        }

        $validatedData['patient_id'] = $initialLetter . $newNumber; // Contoh: Y001, A002

        $patient = Patient::create($validatedData);

        // Kirim email verifikasi
        $patient->sendEmailVerificationNotification();

        return redirect('/patient/register')->with('success', 'Registration successful. Please check your email to verify your account.');
    }
}
