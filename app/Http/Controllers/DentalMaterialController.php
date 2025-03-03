<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DentalMaterial;
use App\Models\StockCard;

class DentalMaterialController extends Controller
{
    public function index()
{
    $dentalMaterials = DentalMaterial::all(); // Ambil semua bahan dental

    // Ambil latest remaining_stock untuk setiap dental_material_id
    $stockCards = StockCard::select('dental_material_id', 'remaining_stock', 'average_price')
        ->whereIn('dental_material_id', $dentalMaterials->pluck('id'))
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')
        ->get()
        ->unique('dental_material_id'); // Ambil yang terbaru per material

    return view('dashboard.dental-materials.index', compact('dentalMaterials', 'stockCards'));
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

    public function report()
    {
        $materials = DentalMaterial::with(['procedures'])->get();

        return view('dashboard.dental-materials.report', compact('materials'));
    }
}
