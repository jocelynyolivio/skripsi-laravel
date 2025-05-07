<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicalRecord;
use App\Models\MedicalRecordAdjustment;

class MedicalRecordAdjustmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $medicalRecordId)
    {
        $request->validate([
            'notes' => 'required|string',
        ]);

        $medicalRecord = MedicalRecord::findOrFail($medicalRecordId);

        $adjustment = new MedicalRecordAdjustment([
            'notes' => $request->notes,
            'adjusted_by' => auth()->id(),
            'adjusted_at' => now(),
        ]);

        $medicalRecord->adjustments()->save($adjustment);

        return redirect()
            ->back()
            ->with('success', 'Adjustment note added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MedicalRecordAdjustment $medicalRecordAdjustment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MedicalRecordAdjustment $medicalRecordAdjustment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MedicalRecordAdjustment $medicalRecordAdjustment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MedicalRecordAdjustment $medicalRecordAdjustment)
    {
        //
    }
}
