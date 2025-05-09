<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Procedure;
use App\Models\Odontogram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OdontogramController extends Controller
{
    public function index($patientId)
    {
        // Ambil data pasien
        $patient = Patient::findOrFail($patientId);

        // Ambil semua data odontogram pasien ini
        $odontograms = Odontogram::with('procedures') // Eager load relasi procedures jika ada
            ->where('patient_id', $patientId)
            ->get()
            ->keyBy('tooth_number');

        // Format data untuk ditampilkan
        $formattedOdontograms = [];
        foreach ($odontograms as $toothNumber => $odontogram) {
            $formattedOdontograms[$toothNumber] = [
                'condition' => $odontogram->condition,
                'notes' => $odontogram->notes,
                'surfaces' => $odontogram->surface ? explode(',', $odontogram->surface) : [],
                'procedures' => $odontogram->procedures->map(function ($procedure) {
                    return [
                        'id' => $procedure->id,
                        'name' => $procedure->name
                    ];
                })
            ];
        }

        // Ambil daftar semua prosedur yang tersedia
        $procedures = Procedure::all();

        return view('dashboard.odontograms.index', [
            'odontograms' => $formattedOdontograms,
            'procedures' => $procedures,
            'patient' => $patient
        ]);
    }



    public function store(Request $request, $patientId)
    {
        // Validasi input
        $validated = $request->validate([
            'tooth_number' => 'required|string',
            'condition' => 'required|string',
            'notes' => 'nullable|string',
            'procedure_id' => 'nullable|array',
            'procedure_id.*' => 'exists:procedures,id',
            'surface' => 'nullable|array',
            'surface.*' => 'in:M,O,L,D,B,I,C',
        ], [
            'tooth_number.required' => 'Nomor gigi harus diisi.',
            'condition.required' => 'Kondisi gigi harus diisi.',
            'condition.in' => 'Kondisi gigi tidak valid.',
            'procedure_id.*.exists' => 'Prosedur yang dipilih tidak valid.',
        ]);

        DB::beginTransaction();
        try {
            // Simpan atau update data odontogram
            $odontogram = Odontogram::updateOrCreate(
                [
                    'patient_id' => $patientId,
                    'tooth_number' => $validated['tooth_number']
                ],
                [
                    'condition' => $validated['condition'],
                    'notes' => $validated['notes'],
                    'surface' => !empty($validated['surface']) ? implode(',', $validated['surface']) : null
                ]
            );

            // // Handle prosedur terkait
            // if (!empty($validated['procedure_id'])) {
            //     // Untuk many-to-many dengan pivot table medical_record_procedure
            //     $syncData = [];
            //     foreach ($validated['procedure_id'] as $procedureId) {
            //         $syncData[$procedureId] = [
            //             'surface' => !empty($validated['surface']) ? implode(',', $validated['surface']) : null,
            //             'created_at' => now(),
            //             'updated_at' => now()
            //         ];
            //     }

            //     $odontogram->procedures()->sync($syncData);
            // } else {
            //     // Jika tidak ada prosedur, hapus semua relasi
            //     $odontogram->procedures()->detach();
            // }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data odontogram berhasil disimpan',
                'data' => $odontogram
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }
    //     public function index($patientId)
    // {
    //     // Ambil data odontogram yang diinput secara manual
    //     $odontograms = Odontogram::where('patient_id', $patientId)->get()->keyBy('tooth_number');

    //     // Ambil semua prosedur yang terkait dengan rekam medis pasien ini
    //     $procedureOdontograms = DB::table('medical_record_procedure')
    //         ->join('procedures', 'medical_record_procedure.procedure_id', '=', 'procedures.id')
    //         ->whereIn('medical_record_procedure.tooth_number', range(1, 32))
    //         ->whereIn('medical_record_procedure.medical_record_id', function ($query) use ($patientId) {
    //             $query->select('id')->from('medical_records')->where('patient_id', $patientId);
    //         })
    //         ->select('medical_record_procedure.tooth_number', 'procedures.id as procedure_id', 'procedures.name as procedure_name')
    //         ->get()
    //         ->groupBy('tooth_number');

    //     $procedures = Procedure::all();
    //     $patient = Patient::findOrFail($patientId);

    //     return view('dashboard.odontograms.index', compact('odontograms', 'procedureOdontograms', 'procedures', 'patient'));
    // }

    //     public function store(Request $request, $patientId)
    // {
    //     // dd('fa');
    //     try {
    //         $validated = $request->validate([
    //             'tooth_number' => 'required|string',
    //             'condition' => 'required|string',
    //             'notes' => 'nullable|string',
    //             'procedure_id' => 'nullable|array',
    //             'procedure_id.*' => 'exists:procedures,id',
    //             'surface' => 'nullable|array',
    //             'surface.*' => 'in:M,O,L,D,B,I,C',
    //         ]);

    //         DB::beginTransaction();

    //         $odontogram = Odontogram::updateOrCreate(
    //             ['patient_id' => $patientId, 'tooth_number' => $validated['tooth_number']],
    //             [
    //                 'condition' => $validated['condition'],
    //                 'notes' => $validated['notes'],
    //                 'surfaces' => !empty($validated['surface']) ? implode(',', $validated['surface']) : null
    //             ]
    //         );

    //         if (!empty($validated['procedure_id'])) {
    //             $odontogram->procedures()->sync($validated['procedure_id']);
    //         }

    //         DB::commit();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Data berhasil disimpan'
    //         ]);

    //     } catch (\Illuminate\Validation\ValidationException $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'success' => false,
    //             'errors' => $e->errors()
    //         ], 422);

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         // \Log::error('Error saving odontogram: '.$e->getMessage());
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Terjadi kesalahan server: '.$e->getMessage()
    //         ], 500);
    //     }
    // }
}
