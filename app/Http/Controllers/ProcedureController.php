<?php

namespace App\Http\Controllers;

use App\Models\Procedure;
use Illuminate\Http\Request;
use App\Models\ProcedureType;

class ProcedureController extends Controller
{
    public function index()
    {
        $procedures = Procedure::with('procedureType')->get();
        return view('dashboard.procedures.index', compact('procedures'));
    }

    public function create()
    {
        $types = ProcedureType::all();
        return view('dashboard.procedures.create', compact('types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'procedure_type_id' => 'nullable|exists:procedure_types,id',
            'description' => 'nullable|string',
            'requires_tooth' => 'boolean',
        ]);

        // Generate item_code
        if ($validated['procedure_type_id']) {
            $typeId = $validated['procedure_type_id'];

            // Hitung sudah ada berapa prosedur dengan procedure_type_id ini
            $count = Procedure::where('procedure_type_id', $typeId)->count();

            // Konversi angka ke huruf: 0 => A, 1 => B, dst.
            $letter = chr(65 + $count); // 65 adalah ASCII untuk A

            // Format procedure_type_id jadi 2 digit
            $codePrefix = str_pad($typeId, 2, '0', STR_PAD_LEFT);

            // Gabungkan
            $validated['item_code'] = $codePrefix . $letter;
        } else {
            $validated['item_code'] = null;
        }

        Procedure::create($validated);
        return redirect()->route('dashboard.procedures.index')->with('success', 'Procedure created successfully.');
    }

    public function edit(Procedure $procedure)
    {
        $types = ProcedureType::all();
        return view('dashboard.procedures.edit', compact('procedure', 'types'));
    }

    public function update(Request $request, Procedure $procedure)
    {
        $validated = $request->validate([
            'item_code' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'procedure_type_id' => 'nullable|exists:procedure_types,id',
            'description' => 'nullable|string',
            'requires_tooth' => 'boolean',
        ]);

        $procedure->update($validated);
        return redirect()->route('dashboard.procedures.index')->with('success', 'Procedure updated successfully.');
    }

    public function destroy(Procedure $procedure)
    {
        $procedure->delete();
        return redirect()->route('dashboard.procedures.index')->with('success', 'Procedure deleted.');
    }
}
