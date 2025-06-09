<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Patient;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

    public function birthday()
    {
        // dd('bday');
        $today = Carbon::now();
        $patientBirthday = Patient::whereRaw('MONTH(date_of_birth) = ? AND DAY(date_of_birth) = ?', [
            $today->month,
            $today->day
        ])->get();

        return view('dashboard.masters.patient-birthday', [
            'title' => 'Patients Birthday',
            'patients' => $patientBirthday,
        ]);
    }

    public function sendVoucherBirthday($id)
    {
        $today = Carbon::now();

        $patientBirthday = Patient::whereRaw('MONTH(date_of_birth) = ? AND DAY(date_of_birth) = ?', [
            $today->month,
            $today->day
        ])
            ->where('id', $id)
            ->firstOrFail();

        // Nomor telepon pasien
        $phoneNumber = $patientBirthday->home_mobile ?? '8120000000'; // fallback biar gak error

        // Pesan template
        $message = "Halo {$patientBirthday->fname}, selamat ulang tahun! Anda mendapatkan voucher spesial dari kami. Terima kasih telah menjadi pasien kami. Berikut kode voucher anda : {$patientBirthday->birthday_voucher_code} dapat digunakan sampai {$patientBirthday->birthday_voucher_expired_at}";

        // Redirect ke wa.me dengan pesan template
        return redirect("https://wa.me/{$phoneNumber}?text=" . urlencode($message));
    }

    public function generateVoucherBirthday($id)
    {

        $today = Carbon::now();

        $patientBirthday = Patient::whereRaw('MONTH(date_of_birth) = ? AND DAY(date_of_birth) = ?', [
            $today->month,
            $today->day
        ])
            ->where('id', $id)
            ->firstOrFail();

        // Generate voucher jika belum ada
        if (!$patientBirthday->birthday_voucher_code & !$patientBirthday->birthday_voucher_expired_at) {
            $voucherCode = 'BDAY-' . strtoupper(Str::random(6));
            $expiredAt = $today->copy()->addDays(7); // Misal voucher berlaku 7 hari

            $patientBirthday->update([
                'birthday_voucher_code' => $voucherCode,
                'birthday_voucher_expired_at' => $expiredAt,
            ]);
        } else {
            $voucherCode = $patientBirthday->birthday_voucher_code;
            $expiredAt = $patientBirthday->birthday_voucher_expired_at;
        }

        return redirect()->back()->with('success', 'Voucher Generated!');
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
        try {
            $validatedData = $request->validate([
                'fname' => 'required|string|max:255',
                'mname' => 'nullable|string|max:255',
                'lname' => 'nullable|string|max:255',
                'gender' => 'required|in:Male,Female,Other',
                'nik' => 'required|string|max:20',
                'blood_type' => 'required|string|max:5',
                'parent_name' => 'nullable|string|max:255',
                'place_of_birth' => 'required|string|max:255',
                'date_of_birth' => 'required|date',
                'religion' => 'nullable|string|max:100',
                'marital_status' => 'nullable|in:Single,Married,Divorced,Widowed',
                'family_status' => 'nullable|string|max:100',
                'occupation' => 'nullable|string|max:255',
                'nationality' => 'nullable|string|max:100',
                'home_address' => 'required|string',
                'home_address_domisili' => 'nullable|string',
                'home_RT' => 'nullable|string|max:10',
                'home_RW' => 'nullable|string|max:10',
                'home_kelurahan' => 'nullable|string|max:100',
                'home_kecamatan' => 'nullable|string|max:100',
                'home_city' => 'nullable|string|max:255',
                'home_zip_code' => 'nullable|string|max:10',
                'home_country' => 'nullable|string|max:255',
                'home_phone' => 'nullable|string|max:20',
                'home_mobile' => 'required|string|max:20',
                'home_email' => 'nullable|email|max:255',
                'office_address' => 'nullable|string',
                'office_city' => 'nullable|string|max:255',
                'office_zip_code' => 'nullable|string|max:10',
                'office_country' => 'nullable|string|max:255',
                'office_phone' => 'nullable|string|max:20',
                'office_mobile' => 'nullable|string|max:20',
                'office_email' => 'nullable|email|max:255',
                'emergency_contact_name' => 'required|string|max:255',
                'emergency_contact_relation' => 'required|string|max:100',
                'emergency_contact_phone' => 'required|string|max:20',
                'form_data_awal' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
                'informed_consent' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
                'email' => 'nullable|email',
                'password' => 'nullable|string|min:6',
            ]);

            // dd($validatedData);

            // Daftar field nomor ponsel yang perlu ditambahkan prefix '62'
            $phoneFields = ['home_mobile', 'office_mobile', 'emergency_contact_phone'];

            foreach ($phoneFields as $field) {
                // Cek apakah field tersebut ada di request dan tidak kosong
                if (!empty($validatedData[$field])) {
                    $number = $validatedData[$field];

                    // Menghapus karakter non-numerik untuk kebersihan data (opsional tapi disarankan)
                    $number = preg_replace('/[^0-9]/', '', $number);

                    // Jika nomor diawali dengan '0', hapus '0' tersebut
                    if (substr($number, 0, 1) === '0') {
                        $number = substr($number, 1);
                    }

                    // Tambahkan prefix '62' dan simpan kembali ke array
                    $validatedData[$field] = '62' . $number;
                }
            }

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
        } catch (\Exception $e) {
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
   public function update(Request $request, Patient $patient)
    {
        // Aturan validasi yang disesuaikan untuk proses update
        $rules = [
            'fname' => 'required|string|max:255',
            'mname' => 'nullable|string|max:255',
            'lname' => 'nullable|string|max:255',
            'gender' => 'required|in:Male,Female,Other',
            // Pastikan NIK unik, kecuali untuk pasien ini sendiri
            'nik' => 'required|string|max:20|unique:patients,nik,' . $patient->id,
            'blood_type' => 'required|string|max:10',
            'parent_name' => 'nullable|string|max:255',
            'place_of_birth' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'religion' => 'nullable|string|max:100',
            'marital_status' => 'nullable|in:Single,Married,Divorced,Widowed',
            'family_status' => 'nullable|string|max:100',
            'occupation' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:100',
            'home_address' => 'required|string',
            'home_address_domisili' => 'nullable|string',
            'home_RT' => 'nullable|string|max:10',
            'home_RW' => 'nullable|string|max:10',
            'home_kelurahan' => 'nullable|string|max:100',
            'home_kecamatan' => 'nullable|string|max:100',
            'home_city' => 'nullable|string|max:255',
            'home_zip_code' => 'nullable|string|max:10',
            'home_country' => 'nullable|string|max:255',
            'home_phone' => 'nullable|string|max:20',
            'home_mobile' => 'required|string|max:20',
            'home_email' => 'nullable|email|max:255',
            'office_address' => 'nullable|string',
            'office_city' => 'nullable|string|max:255',
            'office_zip_code' => 'nullable|string|max:10',
            'office_country' => 'nullable|string|max:255',
            'office_phone' => 'nullable|string|max:20',
            'office_mobile' => 'nullable|string|max:20',
            'office_email' => 'nullable|email|max:255',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_relation' => 'required|string|max:100',
            'emergency_contact_phone' => 'required|string|max:20',
            'form_data_awal' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'informed_consent' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            // Email dan password bersifat opsional saat update
            'email' => 'nullable|email|unique:patients,email,' . $patient->id,
            'password' => 'nullable|string|min:6',
        ];

        try {
            // Lakukan validasi
            $validatedData = $request->validate($rules);

            // --- Normalisasi Nomor Telepon (Menambahkan '62') ---
            $phoneFields = ['home_phone', 'home_mobile', 'office_phone', 'office_mobile', 'emergency_contact_phone'];
            foreach ($phoneFields as $field) {
                if (!empty($validatedData[$field])) {
                    $number = preg_replace('/[^0-9]/', '', $validatedData[$field]);
                    if (substr($number, 0, 1) === '0') {
                        $number = substr($number, 1);
                    }
                    $validatedData[$field] = '62' . $number;
                }
            }

            // --- Penanganan File Upload ---
            // Cek jika ada file baru untuk 'form_data_awal'
            if ($request->hasFile('form_data_awal')) {
                // Hapus file lama jika ada
                if ($patient->form_data_awal) {
                    Storage::disk('public')->delete($patient->form_data_awal);
                }
                // Simpan file baru
                $validatedData['form_data_awal'] = $request->file('form_data_awal')->store('patients/forms', 'public');
            }

            // Cek jika ada file baru untuk 'informed_consent'
            if ($request->hasFile('informed_consent')) {
                // Hapus file lama jika ada
                if ($patient->informed_consent) {
                    Storage::disk('public')->delete($patient->informed_consent);
                }
                // Simpan file baru
                $validatedData['informed_consent'] = $request->file('informed_consent')->store('patients/consent', 'public');
            }

            // --- Penanganan Password ---
            // Hanya update password jika field diisi
            if ($request->filled('password')) {
                $validatedData['password'] = bcrypt($request->password);
            } else {
                // Jika tidak diisi, hapus dari data yang akan di-update
                unset($validatedData['password']);
            }

            // Tambahkan ID pengguna yang melakukan update
            $validatedData['updated_by'] = auth()->id();

            // Lakukan update pada data pasien
            $patient->update($validatedData);

            return redirect()->route('dashboard.masters.patients')->with('success', 'Patient data updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update patient: ' . $e->getMessage())->withInput();
        }
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
