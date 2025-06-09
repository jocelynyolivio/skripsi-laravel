<?php

namespace App\Http\Controllers;

use App\Models\Pricelist;
use App\Models\Procedure;
use Illuminate\Http\Request;

class PricelistController extends Controller
{
    public function index()
    {
        $pricelists = Pricelist::with('procedure')->paginate(10);
        return view('dashboard.pricelists.index', compact('pricelists'));
    }

    public function create()
    {
        $procedures = Procedure::all();
        return view('dashboard.pricelists.create', compact('procedures'));
    }

    public function store(Request $request)
{
    // Validasi input tetap di luar, karena Laravel akan otomatis menangani
    // redirect jika validasi gagal.
    $validatedData = $request->validate([
        'procedure_id' => 'required|exists:procedures,id',
        'price' => 'required|numeric',
        'is_promo' => 'nullable|boolean',
        'effective_date' => 'nullable|date',
    ]);

    try {
        // Proses yang berpotensi gagal (interaksi database) ditempatkan di sini.
        Pricelist::create([
            'procedure_id' => $validatedData['procedure_id'],
            'price' => $validatedData['price'],
            'is_promo' => $request->has('is_promo'), // Lebih ringkas, hasilnya sudah boolean.
            'effective_date' => $validatedData['effective_date'],
        ]);

        // Jika berhasil, redirect dengan pesan sukses.
        return redirect()->route('dashboard.pricelists.index')
                     ->with('success', 'Pricelist berhasil dibuat.');

    } catch (\Exception $e) {
        // Jika terjadi error apapun di dalam blok 'try', tangkap di sini.
        
        // Opsional: Anda bisa menambahkan log untuk debugging.
        // \Log::error('Error creating pricelist: ' . $e->getMessage());

        // Redirect kembali ke halaman sebelumnya dengan pesan error dan input pengguna.
        return redirect()->back()
                     ->with('error', 'Gagal membuat pricelist. Terjadi kesalahan');
                    //  return redirect()->back()
                    //  ->with('error', 'Gagal membuat pricelist. Terjadi kesalahan: ' . $e->getMessage())
                    //  ->withInput();
    }
}

    public function show(Pricelist $pricelist)
    {
        return view('dashboard.pricelists.show', compact('pricelist'));
    }

    public function edit(Pricelist $pricelist)
    {
        $procedures = Procedure::all();
        return view('dashboard.pricelists.edit', compact('pricelist', 'procedures'));
    }

    public function update(Request $request, Pricelist $pricelist)
{
    // Validasi data input, biarkan di luar karena sudah menangani error-nya sendiri.
    $validatedData = $request->validate([
        'procedure_id' => 'required|exists:procedures,id',
        'price' => 'required|numeric',
        'is_promo' => 'nullable|boolean',
        'effective_date' => 'required|date',
    ]);

    try {
        // Blok untuk menjalankan proses yang mungkin gagal (update database).
        $pricelist->update([
            'procedure_id' => $validatedData['procedure_id'],
            'price' => $validatedData['price'],
            'is_promo' => $request->has('is_promo'), // Versi ringkas untuk boolean
            'effective_date' => $validatedData['effective_date'],
        ]);

        // Jika update berhasil, redirect ke halaman index dengan pesan sukses.
        return redirect()->route('dashboard.pricelists.index')
                     ->with('success', 'Pricelist berhasil diperbarui.');

    } catch (\Exception $e) {
        // Jika terjadi kesalahan selama proses update, blok ini akan dieksekusi.
        
        // Opsional: Log error untuk developer.
        // \Log::error('Error updating pricelist ID ' . $pricelist->id . ': ' . $e->getMessage());

        // Redirect kembali ke halaman edit dengan pesan error dan data input sebelumnya.
        return redirect()->back()
                     ->with('error', 'Gagal memperbarui pricelist. Terjadi kesalahan: ' . $e->getMessage())
                     ->withInput();
    }
}

    public function destroy(Pricelist $pricelist)
    {
        $pricelist->delete();
        return redirect()->route('dashboard.pricelists.index')->with('success', 'Pricelist berhasil dihapus');
    }
}
