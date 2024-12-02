<?php

namespace App\Http\Controllers;

use App\Models\Odontogram;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;

class OdontogramController extends Controller
{
    // Menampilkan odontogram untuk sebuah rekam medis
    public function show($medicalRecordId)
    {
        $odontogram = Odontogram::where('medical_record_id', $medicalRecordId)->get();
        return view('dashboard.odontogram.show', [
            'title' => 'odontogram',
            'odontogram' => $odontogram,
            'medicalRecordId' => $medicalRecordId
        ]);
        
    }

    // Menyimpan atau memperbarui odontogram
    public function store(Request $request, $medicalRecordId)
    {
        $odontogramData = $request->odontogram;

        foreach ($odontogramData as $toothNumber => $data) {
            Odontogram::updateOrCreate(
                [
                    'medical_record_id' => $medicalRecordId,
                    'tooth_number' => $toothNumber,
                ],
                [
                    'status' => $data['status'],
                    'notes' => $data['notes'] ?? null,
                ]
            );
        }

        return redirect()->back()->with('success', 'Odontogram updated successfully!');
    }
}
