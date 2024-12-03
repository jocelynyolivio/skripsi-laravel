<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Patient;
use App\Models\Procedure;
use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Models\MedicalRecord;
use App\Models\DentalMaterial;

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

public function create(Request $request, $patientId)
{
    // Mengambil data pasien berdasarkan patientId
    $patient = Patient::findOrFail($patientId);

    // Mengambil semua reservasi yang dimiliki oleh pasien tertentu
    $reservations = Reservation::where('patient_id', $patientId)->get();

    // Mengambil semua prosedur yang tersedia
    $procedures = Procedure::all();

    // Mengambil prosedur yang dipilih (jika ada)
    $selectedProcedureIds = $request->input('procedure_id', []);

    // Mengumpulkan bahan dental yang terkait dengan prosedur yang dipilih
    $selectedMaterials = [];
    if (!empty($selectedProcedureIds)) {
        // Mengambil semua prosedur yang dipilih
        $selectedProcedures = Procedure::whereIn('id', $selectedProcedureIds)->with('dentalMaterials')->get();

        // Menggabungkan bahan dental dari prosedur yang dipilih
        foreach ($selectedProcedures as $procedure) {
            foreach ($procedure->dentalMaterials as $material) {
                if (!isset($selectedMaterials[$material->id])) {
                    // Jika bahan dental belum ada di daftar, tambahkan
                    $selectedMaterials[$material->id] = [
                        'name' => $material->name,
                        'quantity' => $material->pivot->quantity,
                    ];
                } else {
                    // Jika sudah ada, tambahkan jumlahnya
                    $selectedMaterials[$material->id]['quantity'] += $material->pivot->quantity;
                }
            }
        }
    }

    // Mengirim data ke view create
    return view('dashboard.medical_records.create', [
        'patientName' => $patient->name,
        'patientId' => $patientId,
        'reservations' => $reservations,
        'procedures' => $procedures,
        'selectedMaterials' => $selectedMaterials,
    ]);
}




public function store(Request $request, $patientId)
{
    // Validasi input dari form
    $validatedData = $request->validate([
        'reservation_id' => 'required|exists:reservations,id',
        'procedure_id' => 'required|array',
        'procedure_id.*' => 'exists:procedures,id',
        'teeth_condition' => 'required|string',
        'treatment' => 'required|string',
        'notes' => 'nullable|string',
    ]);

    // Mengambil reservasi yang dipilih untuk mendapatkan doctor_id dan tanggal reservasi
    $reservation = Reservation::findOrFail($validatedData['reservation_id']);

    // Buat rekam medis baru
    $medicalRecord = new MedicalRecord();
    $medicalRecord->patient_id = $patientId;
    $medicalRecord->reservation_id = $reservation->id;
    $medicalRecord->doctor_id = $reservation->doctor_id;
    $medicalRecord->date = $reservation->tanggal_reservasi;
    $medicalRecord->teeth_condition = $validatedData['teeth_condition'];
    $medicalRecord->treatment = $validatedData['treatment'];
    $medicalRecord->notes = $validatedData['notes'];
    $medicalRecord->save();

    // Menghubungkan prosedur yang dipilih dengan rekam medis
    $medicalRecord->procedures()->attach($validatedData['procedure_id']);

    // Mengurangi stok bahan dental sesuai dengan prosedur yang dipilih
    $selectedProcedures = Procedure::whereIn('id', $validatedData['procedure_id'])->with('dentalMaterials')->get();

    // Membuat array untuk mengumpulkan kuantitas bahan dental yang harus dikurangi
    $materialsUsage = [];

    // Loop melalui setiap prosedur yang dipilih
    foreach ($selectedProcedures as $procedure) {
        foreach ($procedure->dentalMaterials as $material) {
            // Jika bahan dental sudah ada dalam array, tambahkan kuantitasnya
            if (isset($materialsUsage[$material->id])) {
                $materialsUsage[$material->id]['quantity'] += $material->pivot->quantity;
            } else {
                // Jika belum ada, buat entri baru di array
                $materialsUsage[$material->id] = [
                    'name' => $material->name,
                    'quantity' => $material->pivot->quantity,
                ];
            }
        }
    }

    // Lakukan pengurangan stok untuk setiap bahan dental berdasarkan array $materialsUsage
    foreach ($materialsUsage as $materialId => $materialData) {
        $material = DentalMaterial::findOrFail($materialId);

        // Kurangi stok bahan dental
        $material->stock_quantity -= $materialData['quantity'];

        // Periksa apakah stok cukup sebelum menyimpannya
        if ($material->stock_quantity < 0) {
            return redirect()->back()->with('error', 'Not enough stock for dental material: ' . $material->name);
        }

        // Simpan perubahan stok ke database
        $material->save();
    }

    // Redirect ke halaman rekam medis dengan pesan sukses
    return redirect()->route('dashboard.medical_records.index', ['patientId' => $patientId])
                     ->with('success', 'Medical Record has been added successfully.');
}











}
