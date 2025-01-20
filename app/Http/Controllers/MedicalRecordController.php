<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Patient;
use App\Models\Procedure;
use App\Models\Odontogram;
use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Models\MedicalRecord;
use App\Models\DentalMaterial;
use App\Models\ProcedureOdontogram;

class MedicalRecordController extends Controller
{
    public function index($patientId)
{
    // Menambahkan relasi 'procedures' untuk mengambil prosedur yang terhubung dengan odontogram
    $medicalRecords = MedicalRecord::with(['doctor', 'procedures', 'odontograms', 'procedureOdontograms.procedure'])
        ->where('patient_id', $patientId)
        ->latest()
        ->get();
    
    $patientName = Patient::findOrFail($patientId)->name;

    return view('dashboard.medical_records.index', compact('medicalRecords', 'patientId', 'patientName'));
}

    

    public function create(Request $request, $patientId)
    {
        // Mengambil data pasien berdasarkan patientId
        $patient = Patient::findOrFail($patientId);

        // Mengambil semua reservasi yang dimiliki oleh pasien tertentu. 
        $reservations = Reservation::where('patient_id', $patientId)
    ->whereDoesntHave('medicalRecord') // Tambahkan filter ini
    ->get();


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
    $validatedData = $request->validate([
        'reservation_id' => 'required|exists:reservations,id',
        'procedure_id' => 'required|array',
        'procedure_id.*' => 'exists:procedures,id',
        'teeth_condition' => 'required|string',
        'treatment' => 'required|string',
        'notes' => 'nullable|string',
        'tooth_numbers' => 'required|array', // Pastikan array
        'tooth_numbers.*' => 'integer|min:1|max:32',
        'procedure_notes' => 'nullable|array',
        'procedure_notes.*' => 'nullable|string',
    ]);

    $reservation = Reservation::findOrFail($validatedData['reservation_id']);
    $existingRecord = MedicalRecord::where('reservation_id', $reservation->id)->first();

    if ($existingRecord) {
        return redirect()->back()->with('error', 'A medical record already exists for this reservation.');
    }

    // Simpan Medical Record
    $medicalRecord = new MedicalRecord();
    $medicalRecord->patient_id = $patientId;
    $medicalRecord->reservation_id = $reservation->id;
    $medicalRecord->doctor_id = $reservation->doctor_id;
    $medicalRecord->date = $reservation->tanggal_reservasi;
    $medicalRecord->teeth_condition = $validatedData['teeth_condition'];
    $medicalRecord->treatment = $validatedData['treatment'];
    $medicalRecord->notes = $validatedData['notes'];
    $medicalRecord->save();

    // Prosedur
    $medicalRecord->procedures()->attach($validatedData['procedure_id']);

    // Simpan atau Perbarui Odontogram dan ProcedureOdontogram
    $uniqueCombinations = [];
    foreach (range(1, 32) as $toothNumber) {
        $index = !empty($validatedData['tooth_numbers']) ? array_search($toothNumber, $validatedData['tooth_numbers']) : null;
        $condition = $index !== false && isset($validatedData['odontogram_condition'][$index])
            ? $validatedData['odontogram_condition'][$index]
            : 'Healthy';
        $notes = $index !== false && isset($validatedData['odontogram_notes'][$index])
            ? $validatedData['odontogram_notes'][$index]
            : null;

        // Simpan ke ProcedureOdontogram
        if (!empty($validatedData['procedure_id']) && !empty($validatedData['tooth_numbers'])) {
            foreach ($validatedData['procedure_id'] as $procedureIndex => $procedureId) {
                $currentToothNumber = $validatedData['tooth_numbers'][$procedureIndex] ?? null;
                $procedureNotes = $validatedData['procedure_notes'][$procedureIndex] ?? null;

                // Buat kombinasi unik berdasarkan procedure_id dan tooth_number
                $combinationKey = $procedureId . '-' . $currentToothNumber;

                if (!in_array($combinationKey, $uniqueCombinations) && $currentToothNumber == $toothNumber) {
                    // Simpan kombinasi ke dalam array unik
                    $uniqueCombinations[] = $combinationKey;

                    // Simpan ke database
                    ProcedureOdontogram::create([
                        'medical_record_id' => $medicalRecord->id,
                        'procedure_id' => $procedureId,
                        'tooth_number' => $currentToothNumber,
                        'notes' => $procedureNotes,
                    ]);
                }
            }
        }

        // Update atau buat odontogram baru
        Odontogram::updateOrCreate(
            [
                'patient_id' => $patientId,
                'tooth_number' => $toothNumber,
            ],
            [
                'medical_record_id' => $medicalRecord->id,
                'condition' => $condition,
                'notes' => $notes,
            ]
        );
    }

    return redirect()->route('dashboard.medical_records.selectMaterials', ['medicalRecordId' => $medicalRecord->id])
                     ->with('success', 'Medical Record and Odontogram have been saved successfully.');
}

    
    
    
    public function selectMaterials($medicalRecordId)
{
    // Ambil rekam medis berdasarkan ID
    $medicalRecord = MedicalRecord::findOrFail($medicalRecordId);

    // Ambil semua prosedur yang terkait dengan rekam medis ini
    $procedures = $medicalRecord->procedures;

    // Menyimpan bahan yang dibutuhkan untuk setiap prosedur
    $materials = [];

    foreach ($procedures as $procedure) {
        foreach ($procedure->dentalMaterials as $material) {
            if (!isset($materials[$material->id])) {
                // Menambahkan bahan hanya sekali, jika belum ada dalam array $materials
                $materials[$material->id] = [
                    'name' => $material->name,
                    'stock_quantity' => $material->stock_quantity,
                    'quantity' => $material->pivot->quantity, // Menyimpan jumlah bahan yang diperlukan untuk prosedur
                    'procedure_id' => $procedure->id // Menyimpan id prosedur untuk menghubungkan bahan ke prosedur
                ];
            } else {
                // Jika bahan sudah ada, tambah jumlah kuantitas untuk prosedur yang sama
                $materials[$material->id]['quantity'] += $material->pivot->quantity;
            }
        }
    }

    // Kirim bahan dan prosedur ke view
    return view('dashboard.medical_records.selectMaterials', [
        'medicalRecordId' => $medicalRecordId,
        'procedures' => $procedures,
        'materials' => $materials, // kirim bahan dan kuantitas yang diperlukan
    ]);
}


public function saveMaterials(Request $request, $medicalRecordId)
{
    $medicalRecord = MedicalRecord::findOrFail($medicalRecordId);

    // Validasi input bahan dan kuantitas
    $validatedData = $request->validate([
        'quantities' => 'required|array',
    ]);

    // Loop melalui bahan-bahan yang dipilih
    foreach ($validatedData['quantities'] as $materialId => $quantity) {
        if ($quantity > 0) {
            $material = DentalMaterial::findOrFail($materialId);

            // Periksa apakah stok cukup
            if ($material->stock_quantity < $quantity) {
                return redirect()->back()->with('error', 'Not enough stock for ' . $material->name);
            }

            // Kurangi stok bahan
            $material->stock_quantity -= $quantity;
            $material->save();

            // Simpan hubungan antara rekam medis dan bahan
            $medicalRecord->dentalMaterials()->attach($materialId, ['quantity' => $quantity]);
        }
    }

    // Redirect ke halaman rekam medis
    return redirect()->route('dashboard.medical_records.index', ['patientId' => $medicalRecord->patient_id])
                     ->with('success', 'Dental materials have been successfully saved.');
}
public function removeMaterial($medicalRecordId, $materialId)
{
    // Temukan rekam medis berdasarkan ID
    $medicalRecord = MedicalRecord::findOrFail($medicalRecordId);

    // Periksa apakah bahan terkait dengan rekam medis tersebut
    if ($medicalRecord->dentalMaterials()->where('dental_material_id', $materialId)->exists()) {
        // Menghapus hubungan antara rekam medis dan bahan dental
        $medicalRecord->dentalMaterials()->detach($materialId);
    }

    // Redirect kembali ke halaman dengan pesan sukses
    return redirect()->route('dashboard.medical_records.selectMaterials', ['medicalRecordId' => $medicalRecordId])
                     ->with('success', 'Dental material removed successfully.');
}

public function procedureMaterialsPage()
{
    // Ambil semua prosedur beserta bahan materialnya
    $procedures = Procedure::with('dentalMaterials')->get();

    // Kirim data ke view
    return view('dashboard.procedure_materials', compact('procedures'));
}

public function edit($patientId, $recordId)
{
    // Retrieve the medical record using the provided recordId
    $medicalRecord = MedicalRecord::findOrFail($recordId);

    // Pass the record to the view, along with the patientId
    return view('dashboard.medical_records.edit', [
        'medicalRecord' => $medicalRecord,
        'patientId' => $patientId
    ]);
}


public function update(Request $request, $patientId, $recordId)
{
    $validatedData = $request->validate([
        'teeth_condition' => 'required|string',
        'treatment' => 'required|string',
        'notes' => 'nullable|string',
    ]);

    $medicalRecord = MedicalRecord::findOrFail($recordId);
    $medicalRecord->update($validatedData);

    return redirect()->route('dashboard.medical_records.index', ['patientId' => $patientId])
                     ->with('success', 'Medical record updated successfully.');
}

public function destroy($patientId, $recordId)
{
    $medicalRecord = MedicalRecord::findOrFail($recordId);
    $medicalRecord->delete();

    return redirect()->route('dashboard.medical_records.index', ['patientId' => $patientId])
                     ->with('success', 'Medical record deleted successfully.');
}

public function selectForTransaction()
{
    // Ambil semua rekam medis yang belum memiliki transaksi
    $medicalRecords = MedicalRecord::doesntHave('transaction')
        ->with(['patient', 'doctor', 'reservation'])
        ->get();

    return view('dashboard.medical_records.selectForTransaction', [
        'title' => 'Select Medical Record',
        'medicalRecords' => $medicalRecords,
    ]);
}


}
