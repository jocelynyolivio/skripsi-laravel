<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Procedure;
use App\Models\Odontogram;
use Illuminate\Http\Request;

class OdontogramController extends Controller
{
    public function index($patientId)
    {
        $odontograms = Odontogram::where('patient_id', $patientId)->with('procedures')->get();
        $procedures = Procedure::all();
        $patient = Patient::findOrFail($patientId);

        return view('dashboard.odontograms.index', compact('odontograms', 'procedures', 'patient'));
    }

    public function store(Request $request, $patientId)
    {
        $validated = $request->validate([
            'tooth_number' => 'required|string',
            'condition' => 'required|string',
            'notes' => 'nullable|string',
            'procedure_id' => 'nullable|array',
            'procedure_id.*' => 'exists:procedures,id',
        ], [
            'tooth_number.required' => 'Nomor gigi harus diisi.',
            'condition.required' => 'Kondisi gigi harus diisi.',
            'procedure_id.*.exists' => 'Prosedur yang dipilih tidak valid.',
        ]);
        
    
        $odontogram = Odontogram::updateOrCreate(
            ['patient_id' => $patientId, 'tooth_number' => $validated['tooth_number']],
            ['condition' => $validated['condition'], 'notes' => $validated['notes']]
        );
    
        if (!empty($validated['procedure_id'])) {
            $odontogram->procedures()->sync($validated['procedure_id']);
        }
    
        return redirect()->back()->with('success', 'Odontogram updated successfully.');
    }
    
}
