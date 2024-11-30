<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Patient;
use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Models\MedicalRecord;

class MedicalRecordController extends Controller
{
    public function index($patientId)
{
    $medicalRecords = MedicalRecord::where('patient_id', $patientId)->get();
    $patient = Patient::findOrFail($patientId);
    return view('dashboard.medical_records.index', [
        'medicalRecords' => $medicalRecords,
        'patientName' => $patient->name,
        'patientId' => $patientId
    ]);
}

public function create($patientId)
{
    // Mengambil data pasien berdasarkan patientId
    $patient = Patient::findOrFail($patientId);

    // Mengambil semua reservasi yang dimiliki oleh pasien tertentu
    $reservations = Reservation::where('patient_id', $patientId)->get();

    // Mengirim data ke view create
    return view('dashboard.medical_records.create', [
        'patientName' => $patient->name,
        'patientId' => $patientId,
        'reservations' => $reservations // Mengirim daftar reservasi ke view
    ]);
}


public function store(Request $request, $patientId)
{
    // Validasi input dari form
    $validatedData = $request->validate([
        'reservation_id' => 'required|exists:reservations,id',
        'teeth_condition' => 'required|string',
        'treatment' => 'required|string',
        'notes' => 'nullable|string'
    ]);

    // Mengambil reservasi yang dipilih untuk mendapatkan doctor_id dan date
    $reservation = Reservation::findOrFail($validatedData['reservation_id']);

    // Menambahkan informasi patient_id, doctor_id, dan tanggal dari reservasi
    $medicalRecord = new MedicalRecord();
    $medicalRecord->patient_id = $patientId;
    $medicalRecord->reservation_id = $reservation->id;
    $medicalRecord->doctor_id = $reservation->doctor_id;
    $medicalRecord->date = $reservation->tanggal_reservasi;
    $medicalRecord->teeth_condition = $validatedData['teeth_condition'];
    $medicalRecord->treatment = $validatedData['treatment'];
    $medicalRecord->notes = $validatedData['notes'];

    // Simpan rekam medis baru
    $medicalRecord->save();

    // Redirect ke halaman rekam medis dengan pesan sukses
    return redirect()->route('dashboard.medical_records.index', ['patientId' => $patientId])
                     ->with('success', 'Medical Record has been added successfully.');
}




}
