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
        // Ambil data odontogram yang diinput secara manual
        $odontograms = Odontogram::where('patient_id', $patientId)->get()->keyBy('tooth_number');
    
        // Ambil semua prosedur yang terkait dengan
        $procedureOdontograms = DB::table('procedure_odontogram')
        ->join('procedures', 'procedure_odontogram.procedure_id', '=', 'procedures.id')
        ->whereIn('procedure_odontogram.tooth_number', range(1, 32))
        ->whereIn('procedure_odontogram.medical_record_id', function ($query) use ($patientId) {
            $query->select('id')->from('medical_records')
                ->whereIn('reservation_id', function ($q) use ($patientId) {
                    $q->select('id')->from('reservations')->where('patient_id', $patientId);
                });
        })
        ->select('procedure_odontogram.tooth_number', 'procedures.id as procedure_id', 'procedures.name as procedure_name')
        ->get()
        ->groupBy('tooth_number');

    $procedures = Procedure::all();
    $patient = Patient::findOrFail($patientId);

    return view('dashboard.odontograms.index', compact('odontograms', 'procedureOdontograms', 'procedures', 'patient'));
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
