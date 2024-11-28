<?php

namespace App\Http\Controllers;
use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil semua data pasien dari tabel patients
        $patients = Patient::all();

        // Kirim data ke view
        return view('dashboard.masters.patients', [
            'title' => 'Master Patients',
            'patients' => $patients,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.masters.add-patient', [
            'title' => 'Create Patient',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    // Menyimpan data pasien baru
    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:patients,email',
        'nomor_telepon' => 'required|string|max:20',
    ]);

    Patient::create([
        'name' => $request->name,
        'email' => $request->email,
        'nomor_telepon' => $request->nomor_telepon,
        'password' => bcrypt('123456'), // Tambahkan password default
    ]);

    return redirect()->route('dashboard.masters.patients')->with('success', 'Patient added successfully!');
}


    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $patient = Patient::findOrFail($id);

        return view('dashboard.masters.edit-patient', [
            'title' => 'Edit Patient',
            'patient' => $patient,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:patients,email,' . $id,
            'nomor_telepon' => 'required|string|max:20',
        ]);

        $patient = Patient::findOrFail($id);
        $patient->update($request->only(['name', 'email', 'nomor_telepon']));

        return redirect()->route('dashboard.masters.patients')->with('success', 'Patient updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);
        $patient->delete();

        return redirect()->route('dashboard.masters.patients')->with('success', 'Patient deleted successfully!');
    }
}
