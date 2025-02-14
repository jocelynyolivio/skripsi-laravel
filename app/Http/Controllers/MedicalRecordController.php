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
        $medicalRecords = MedicalRecord::with(['reservation.patient', 'reservation.doctor', 'procedureOdontograms.procedure'])->whereHas('reservation', function ($query) use ($patientId) {
            $query->where('patient_id', $patientId);
        })->latest()->get();

        $patientName = Patient::findOrFail($patientId)->name;
        $proceduress = Procedure::all();

        return view('dashboard.medical_records.index', compact('medicalRecords', 'patientId', 'patientName', 'proceduress'));
    }



    public function create(Request $request, $patientId)
    {
        // Mengambil data pasien berdasarkan patientId
        $patient = Patient::findOrFail($patientId);

        // Mengambil semua reservasi yang dimiliki oleh pasien tertentu. 
        $reservations = Reservation::where('patient_id', $patientId)->whereDoesntHave('medicalRecord')->get();


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
            'tooth_numbers' => 'nullable|array',
            'procedure_notes' => 'nullable|array',
        ]);
    
        // Temukan reservasi pasien
        $reservation = Reservation::findOrFail($validatedData['reservation_id']);
    
        // Simpan rekam medis
        $medicalRecord = new MedicalRecord();
        $medicalRecord->reservation_id = $reservation->id;
        $medicalRecord->teeth_condition = $validatedData['teeth_condition'];
        $medicalRecord->save();
    
        // Ambil daftar prosedur yang memerlukan nomor gigi
        $proceduresRequiringTeeth = Procedure::where('requires_tooth', 1)->pluck('id')->toArray();
        $uniqueCombinations = [];
    
        foreach ($validatedData['procedure_id'] as $procedureId) {
            if (in_array($procedureId, $proceduresRequiringTeeth)) {
                // Jika prosedur memerlukan nomor gigi, pastikan ada data
                if (!isset($validatedData['tooth_numbers'][$procedureId]) || !is_array($validatedData['tooth_numbers'][$procedureId])) {
                    return redirect()->back()->with('error', "Tooth number is required for procedure ID: $procedureId");
                }
    
                foreach ($validatedData['tooth_numbers'][$procedureId] as $toothNumber) {
                    $procedureNotes = $validatedData['procedure_notes'][$procedureId][$toothNumber] ?? null;
    
                    $combinationKey = $procedureId . '-' . $toothNumber;
                    if (!in_array($combinationKey, $uniqueCombinations)) {
                        $uniqueCombinations[] = $combinationKey;
    
                        ProcedureOdontogram::create([
                            'medical_record_id' => $medicalRecord->id,
                            'procedure_id' => $procedureId,
                            'tooth_number' => $toothNumber,
                            'notes' => $procedureNotes,
                        ]);
                    }
                }
            } else {
                // Jika prosedur tidak membutuhkan gigi, cukup simpan prosedurnya dengan notes (jika ada)
                $procedureNotes = $validatedData['procedure_notes'][$procedureId] ?? null;
    
                if (!in_array($procedureId, $uniqueCombinations)) {
                    $uniqueCombinations[] = $procedureId;
    
                    ProcedureOdontogram::create([
                        'medical_record_id' => $medicalRecord->id,
                        'procedure_id' => $procedureId,
                        'tooth_number' => null, // Tidak perlu gigi
                        'notes' => is_array($procedureNotes) ? implode(', ', $procedureNotes) : $procedureNotes,
                    ]);
                }
            }
        }
    
        return redirect()->route('dashboard.medical_records.index', ['patientId' => $patientId])
            ->with('success', 'Medical Record and Odontogram have been saved successfully.');
    }
    

    public function selectMaterials($medicalRecordId)
    {
        // Ambil rekam medis berdasarkan ID
        $medicalRecord = MedicalRecord::findOrFail($medicalRecordId);
    
        // Ambil semua prosedur dari procedure_odontograms
        $procedureIds = $medicalRecord->procedureOdontograms->pluck('procedure_id')->unique();
        $procedures = Procedure::whereIn('id', $procedureIds)->get();
    
        // Cek apakah rekam medis ini sudah memiliki bahan tersimpan
        $hasMaterials = $medicalRecord->dentalMaterials()->exists(); // True jika sudah ada bahan tersimpan
    
        // Menyimpan bahan yang dibutuhkan untuk setiap prosedur
        $materials = [];
    
        foreach ($procedures as $procedure) {
            // Hitung jumlah gigi yang terkait dengan prosedur ini
            $toothCount = $medicalRecord->procedureOdontograms->where('procedure_id', $procedure->id)->count();
    
            foreach ($procedure->dentalMaterials as $material) {
                $requiredQuantity = $material->pivot->quantity * max(1, $toothCount); // Kalikan dengan jumlah gigi jika diperlukan
    
                if (!isset($materials[$material->id])) {
                    // Menambahkan bahan hanya sekali, jika belum ada dalam array $materials
                    $materials[$material->id] = [
                        'name' => $material->name,
                        'stock_quantity' => $material->stock_quantity,
                        'quantity' => $requiredQuantity, // Menyimpan jumlah bahan yang diperlukan untuk prosedur
                        'procedure_id' => $procedure->id // Menyimpan id prosedur untuk menghubungkan bahan ke prosedur
                    ];
                } else {
                    // Jika bahan sudah ada, tambah jumlah kuantitas untuk prosedur yang sama
                    $materials[$material->id]['quantity'] += $requiredQuantity;
                }
            }
        }
    
        return view('dashboard.medical_records.selectMaterials', [
            'medicalRecordId' => $medicalRecordId,
            'procedures' => $procedures,
            'materials' => $materials,
            'hasMaterials' => $hasMaterials, // Kirim status apakah sudah tersimpan atau belum
        ]);
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
        // Ambil rekam medis beserta semua prosedur yang telah tercatat dalam procedure_odontogram
        $medicalRecord = MedicalRecord::with(['procedureOdontograms.procedure'])->findOrFail($recordId);

        // Ambil semua prosedur yang tersedia
        $procedures = Procedure::all();

        // Ambil semua prosedur yang telah dipilih dalam rekam medis
        $selectedProcedures = $medicalRecord->procedureOdontograms->pluck('procedure_id')->toArray();

        // Ambil semua nomor gigi yang terkait dengan rekam medis ini
        $procedureOdontograms = $medicalRecord->procedureOdontograms->map(function ($po) {
            return [
                'id' => $po->id, // ID untuk update data yang sudah ada
                'procedure_id' => $po->procedure_id,
                'tooth_number' => $po->tooth_number,
                'notes' => $po->notes,
            ];
        });

        return view('dashboard.medical_records.edit', compact(
            'medicalRecord', 'patientId', 'procedures', 'selectedProcedures', 'procedureOdontograms'
        ));
    }

    public function update(Request $request, $patientId, $recordId)
    {
        // Debugging: Cek apakah request masuk dengan benar
        // dd($request->all());

        $validatedData = $request->validate([
            'teeth_condition' => 'required|string',
            'id' => 'required|array', // ID dari ProcedureOdontogram yang diedit
            'id.*' => 'exists:procedure_odontogram,id', // Sesuaikan dengan nama tabel
            'tooth_numbers' => 'nullable|array',
            'tooth_numbers.*' => 'nullable|string',
            'procedure_notes' => 'nullable|array',
            'procedure_notes.*' => 'nullable|string',
        ]);

        // Ambil rekam medis berdasarkan ID
        $medicalRecord = MedicalRecord::findOrFail($recordId);

        // Update kondisi gigi pada rekam medis
        $medicalRecord->teeth_condition = $validatedData['teeth_condition'];
        $medicalRecord->save();

        // Loop berdasarkan `id[]` untuk update data yang sudah ada
        foreach ($validatedData['id'] as $index => $odontogramId) {
            $odontogram = ProcedureOdontogram::find($odontogramId);

            if ($odontogram) {
                // Periksa apakah nomor gigi ada dalam request atau gunakan nilai lama
                $newToothNumber = $validatedData['tooth_numbers'][$index] ?? $odontogram->tooth_number;
                $newNotes = $validatedData['procedure_notes'][$index] ?? $odontogram->notes;

                // Update hanya jika ada perubahan data
                if ($newToothNumber != $odontogram->tooth_number || $newNotes != $odontogram->notes) {
                    $odontogram->update([
                        'tooth_number' => $newToothNumber,
                        'notes' => $newNotes,
                    ]);
                }
            }
        }

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
            ->with(['reservation.patient', 'reservation.doctor'])
            ->get();


        return view('dashboard.medical_records.selectForTransaction', [
            'title' => 'Select Medical Record',
            'medicalRecords' => $medicalRecords,
        ]);
    }
}
