<?php

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

                        $medicalRecord->procedures()->attach($procedureId, [
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

                    $medicalRecord->procedures()->attach($procedureId, [
                        'tooth_number' => null, // Tidak perlu gigi
                        'notes' => is_array($procedureNotes) ? implode(', ', $procedureNotes) : $procedureNotes,
                    ]);
                }
            }
        }

        return redirect()->route('dashboard.medical_records.index', ['patientId' => $patientId])
            ->with('success', 'Medical Record and Odontogram have been saved successfully.');
    }