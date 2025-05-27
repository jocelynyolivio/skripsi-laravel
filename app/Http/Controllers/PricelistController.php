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
        $request->validate([
            'procedure_id' => 'required|exists:procedures,id',
            'price' => 'required|numeric',
            'is_promo' => 'nullable|boolean',
            'effective_date' => 'required|date',
        ]);

        Pricelist::create([
            'procedure_id' => $request->procedure_id,
            'price' => $request->price,
            'is_promo' => $request->has('is_promo') ? true : false,
            'effective_date' => $request->effective_date,
        ]);

        return redirect()->route('dashboard.pricelists.index')->with('success', 'Pricelist berhasil dibuat');
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
        $request->validate([
            'procedure_id' => 'required|exists:procedures,id',
            'price' => 'required|numeric',
            'is_promo' => 'nullable|boolean',
            'effective_date' => 'required|date',
        ]);

        $pricelist->update([
            'procedure_id' => $request->procedure_id,
            'price' => $request->price,
            'is_promo' => $request->has('is_promo') ? true : false,
            'effective_date' => $request->effective_date,
        ]);

        return redirect()->route('dashboard.pricelists.index')->with('success', 'Pricelist berhasil diperbarui');
    }

    public function destroy(Pricelist $pricelist)
    {
        $pricelist->delete();
        return redirect()->route('dashboard.pricelists.index')->with('success', 'Pricelist berhasil dihapus');
    }
}
