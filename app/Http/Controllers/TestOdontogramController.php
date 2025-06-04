<?php

namespace App\Http\Controllers;

use App\Models\Odontogram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestOdontogramController extends Controller
{
    public function show($patientId)
    {
        $conditions = Odontogram::where('patient_id', $patientId)
            ->get()
            ->mapWithKeys(function ($item) {
                $key = strtolower($item->surface) . ' ' . $item->tooth_number;
                return [$key => $item->condition];
            });

        return view('dashboard.test-odontogram.full-view', [
            'patientId' => $patientId,
            'toothConditions' => $conditions
        ]);
    }

    public function store(Request $request, $patientId)
    {
        $validated = $request->validate([
            'tooth_number' => 'required|string',
            'surface'      => 'required|string',
            'condition'    => 'required|string',
            'notes'        => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            Odontogram::updateOrCreate(
                [
                    'patient_id'   => $patientId,                 
                    'tooth_number' => $validated['tooth_number'], 
                    'surface'      => $validated['surface'],      
                ],
                [
                    'condition' => $validated['condition'],        
                    'notes'     => $validated['notes'],            
                ]
            );

            DB::commit();

            return redirect()->back()->with('success', 'Data kondisi gigi berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();

            // Opsional: Catat error ke log untuk debugging
            // Pastikan sudah `use Illuminate\Support\Facades\Log;` di atas
            // Log::error('Gagal menyimpan data odontogram: ' . $e->getMessage(), [
            //     'patient_id' => $patientId,
            //     'request_data' => $validated,
            //     'trace' => $e->getTraceAsString() // Untuk detail error jika diperlukan
            // ]);
            
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
        }
    }
}
