<?php

namespace App\Http\Controllers;

use App\Models\Procedure;
use Illuminate\Http\Request;
use App\Models\DentalMaterial;
use App\Models\ProcedureMaterial;

class ProcedureMaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $procedureMaterials = ProcedureMaterial::with(['procedure', 'dentalMaterial'])->get(); // Eager load relationships

    return view('dashboard.procedure_materials.index', compact('procedureMaterials'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $procedures = Procedure::all();
        $dentalMaterials = DentalMaterial::all();
    
        return view('dashboard.procedure_materials.create', compact('procedures', 'dentalMaterials'));
    }
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'procedure_id' => 'required|exists:procedures,id',
            'dental_material_id' => 'required|exists:dental_materials,id',
            'quantity' => 'required|integer|min:1',
        ]);
    
        ProcedureMaterial::create($validatedData);
    
        return redirect()->route('dashboard.procedure_materials.index')
                         ->with('success', 'Procedure material relationship added successfully.');
    }
    

    /**
     * Display the specified resource.
     */
    public function show(ProcedureMaterial $procedureMaterial)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $procedureMaterial = ProcedureMaterial::findOrFail($id);
        $procedures = Procedure::all();
        $dentalMaterials = DentalMaterial::all();
    
        return view('dashboard.procedure_materials.edit', compact('procedureMaterial', 'procedures', 'dentalMaterials'));
    }
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'procedure_id' => 'required|exists:procedures,id',
            'dental_material_id' => 'required|exists:dental_materials,id',
            'quantity' => 'required|integer|min:1',
        ]);
    
        $procedureMaterial = ProcedureMaterial::findOrFail($id);
        $procedureMaterial->update($validatedData);
    
        return redirect()->route('dashboard.procedure_materials.index')
                         ->with('success', 'Procedure material relationship updated successfully.');
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $procedureMaterial = ProcedureMaterial::findOrFail($id);
        $procedureMaterial->delete();
    
        return redirect()->route('dashboard.procedure_materials.index')
                         ->with('success', 'Procedure material relationship deleted successfully.');
    }
    
}
