<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // dd('a'); 
        // Ambil Semua Data Supplier
        $suppliers = Supplier::all();
        return view('dashboard.suppliers.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Tampilkan Form Create Supplier
        return view('dashboard.suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // dd($request);
        // Validasi Input
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string|max:255',
            'nomor_telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);
        // dd($validated);

        // Simpan Data Supplier
        Supplier::create($validated);

        // Redirect ke Index Supplier dengan Pesan Sukses
        return redirect()->route('dashboard.suppliers.index')->with('success', 'Supplier Created');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        // Tampilkan Detail Supplier
        return view('dashboard.suppliers.show', compact('supplier'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        return view('dashboard.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'nomor_telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $supplier->update($validated);

        return redirect()->route('dashboard.suppliers.index')->with('success', 'Supplier berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        // Hapus Data Supplier
        $supplier->delete();

        // Redirect ke Index Supplier dengan Pesan Sukses
        return redirect()->route('dashboard.suppliers.index')->with('success', 'Suppplier Deleted');
    }
}
