<?php

namespace App\Http\Controllers;

use App\Models\ProcedureType;
use Illuminate\Http\Request;

class ProcedureTypeController extends Controller
{
    public function index()
    {
        $procedureTypes = ProcedureType::all();
        return view('dashboard.procedure_types.index', compact('procedureTypes'));
    }

    public function create()
    {
        return view('dashboard.procedure_types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        ProcedureType::create($validated);
        return redirect()->route('dashboard.procedure_types.index')->with('success', 'Procedure type created successfully.');
    }

    public function edit(ProcedureType $procedureType)
    {
        return view('dashboard.procedure_types.edit', compact('procedureType'));
    }

    public function update(Request $request, ProcedureType $procedureType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $procedureType->update($validated);
        return redirect()->route('dashboard.procedure_types.index')->with('success', 'Procedure type updated successfully.');
    }

    public function destroy(ProcedureType $procedureType)
    {
        $procedureType->delete();
        return redirect()->route('dashboard.procedure_types.index')->with('success', 'Procedure type deleted successfully.');
    }
}
