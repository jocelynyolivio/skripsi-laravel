<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DentalMaterial;

class DentalMaterialController extends Controller
{
    public function index()
    {
        $dentalMaterials = DentalMaterial::all(); // Ambil semua bahan dental
        return view('dashboard.dental-materials.index', compact('dentalMaterials'));
    }

    public function create()
    {
        return view('dashboard.dental-materials.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'stock_quantity' => 'required|integer',
            'unit_price' => 'nullable|numeric',
        ]);

        DentalMaterial::create($validatedData);
        return redirect()->route('dashboard.dental-materials.index')->with('success', 'Dental material added successfully.');
    }

    public function edit($id)
    {
        $dentalMaterial = DentalMaterial::findOrFail($id);
        return view('dashboard.dental-materials.edit', compact('dentalMaterial'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'stock_quantity' => 'required|integer',
            'unit_price' => 'nullable|numeric',
        ]);

        $dentalMaterial = DentalMaterial::findOrFail($id);
        $dentalMaterial->update($validatedData);

        return redirect()->route('dashboard.dental-materials.index')->with('success', 'Dental material updated successfully.');
    }

    public function destroy($id)
    {
        $dentalMaterial = DentalMaterial::findOrFail($id);
        $dentalMaterial->delete();
        return redirect()->route('dashboard.dental-materials.index')->with('success', 'Dental material deleted successfully.');
    }
}
