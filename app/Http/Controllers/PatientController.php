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
    public function store(Request $request)
    {
        // dd($request->all());
        // dd('hai');
        $updated_by = auth()->id();
        // dd($updated_by);
        // Validasi input
        try{
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'gender' => 'required|in:Male,Female',
                'nik' => 'required|string|max:20|unique:patients,nik',
                'blood_type' => 'required|string|max:5',
                'place_of_birth' => 'required|string|max:255',
                'date_of_birth' => 'required|date',
                'religion' => 'nullable|string|max:100',
                'marital_status' => 'nullable|in:Single,Married,Divorced,Widowed',
                'family_status' => 'nullable|string|max:100',
                'occupation' => 'nullable|string|max:255',
                'nationality' => 'nullable|string|max:100',
    
                // Alamat Rumah
                'home_address' => 'required|string',
                'home_city' => 'nullable|string|max:255',
                'home_zip_code' => 'nullable|string|max:10',
                'home_country' => 'nullable|string|max:255',
                'home_phone' => 'nullable|string|max:20',
                'home_mobile' => 'required|string|max:20',
                'home_email' => 'nullable|email|max:255',
    
                // Alamat Kantor (Opsional)
                'office_address' => 'nullable|string',
                'office_city' => 'nullable|string|max:255',
                'office_zip_code' => 'nullable|string|max:10',
                'office_country' => 'nullable|string|max:255',
                'office_phone' => 'nullable|string|max:20',
                'office_mobile' => 'nullable|string|max:20',
                'office_email' => 'nullable|email|max:255',
    
                // Kontak Darurat
                'emergency_contact_name' => 'required|string|max:255',
                'emergency_contact_phone' => 'required|string|max:20',
    
                // Upload Dokumen (Opsional)
                'form_data_awal' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
                'informed_consent' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
    
                // Email & Password
                'email' => 'nullable|email|unique:patients,email',
                'password' => 'nullable|string|min:6',
            ]);
    
            // dd($validatedData);
    
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
    
            // dd($validatedData['patient_id']);
    
            // Handle file upload untuk Form Data Awal
            if ($request->hasFile('form_data_awal')) {
                $validatedData['form_data_awal'] = $request->file('form_data_awal')->store('patients/forms', 'public');
            }
    
            // Handle file upload untuk Informed Consent
            if ($request->hasFile('informed_consent')) {
                $validatedData['informed_consent'] = $request->file('informed_consent')->store('patients/consent', 'public');
            }
    
            // Jika password kosong, set default password
            if (!$request->filled('password')) {
                $validatedData['password'] = bcrypt('123456');
            } else {
                $validatedData['password'] = bcrypt($request->password);
            }

            $validatedData['updated_by'] = $updated_by;
            // dd($validatedData['updated_by']);
    
            // Simpan data ke database
            Patient::create($validatedData);
            return redirect()->route('dashboard.masters.patients')->with('success', 'Patient added successfully!');
    
        }catch (\Exception $e){
            // dd($e);
            return redirect()->back()->with('error', 'Gagal karena ' . $e->getMessage());
        }  
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
        $patient = Patient::findOrFail($id);
        $patient->updated_by = auth()->id();

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|in:Male,Female,Other',
            'nik' => 'required|string|max:20' . $id,
            'blood_type' => 'required|string|max:5',
            'place_of_birth' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'religion' => 'nullable|string|max:100',
            'marital_status' => 'nullable|in:Single,Married,Divorced,Widowed',
            'family_status' => 'nullable|string|max:100',
            'occupation' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:100',

            // Alamat Rumah
            'home_address' => 'required|string',
            'home_city' => 'nullable|string|max:255',
            'home_zip_code' => 'nullable|string|max:10',
            'home_country' => 'nullable|string|max:255',
            'home_phone' => 'nullable|string|max:20',
            'home_mobile' => 'required|string|max:20',
            'home_email' => 'nullable|email|max:255',

            // Alamat Kantor
            'office_address' => 'nullable|string',
            'office_city' => 'nullable|string|max:255',
            'office_zip_code' => 'nullable|string|max:10',
            'office_country' => 'nullable|string|max:255',
            'office_phone' => 'nullable|string|max:20',
            'office_mobile' => 'nullable|string|max:20',
            'office_email' => 'nullable|email|max:255',

            // Kontak Darurat
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',

            // Upload Dokumen
            'form_data_awal' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'informed_consent' => 'nullable|file|mimes:pdf,jpg,png|max:2048',

            // Email & Password
            'email' => 'nullable|email' . $id,
            'password' => 'nullable|string|min:6',
        ]);

        // Jika password tidak diisi, gunakan password lama
        if (!$request->filled('password')) {
            $validatedData['password'] = $patient->password;
        } else {
            $validatedData['password'] = bcrypt($request->password);
        }

        // Handle file upload untuk Form Data Awal
        if ($request->hasFile('form_data_awal')) {
            $validatedData['form_data_awal'] = $request->file('form_data_awal')->store('patients/forms', 'public');
        } else {
            $validatedData['form_data_awal'] = $patient->form_data_awal;
        }

        // Handle file upload untuk Informed Consent
        if ($request->hasFile('informed_consent')) {
            $validatedData['informed_consent'] = $request->file('informed_consent')->store('patients/consent', 'public');
        } else {
            $validatedData['informed_consent'] = $patient->informed_consent;
        }

        // Pastikan `patient_id` tidak berubah
        $validatedData['patient_id'] = $patient->patient_id;

        // Update data pasien
        $patient->update($validatedData);

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
