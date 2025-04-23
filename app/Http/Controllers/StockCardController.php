<?php

namespace App\Http\Controllers;

use App\Models\StockCard;
use Illuminate\Http\Request;
use App\Models\DentalMaterial;

class StockCardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = StockCard::with('dentalMaterial')->latest();

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        $stockCards = $query->get();

        return view('dashboard.stock_cards.index', compact('stockCards'));
    }

    public function adjustForm()
    {
        // dd('hai');
        $materials = DentalMaterial::all();
        return view('dashboard.stock_cards.adjust', compact('materials'));
    }

    public function storeAdjustment(Request $request)
    {
        $request->validate([
            'dental_material_id' => 'required|exists:dental_materials,id',
            'date' => 'required|date',
            'quantity_in' => 'required|integer',
            'note' => 'nullable|string',
        ]);

        // Ambil adjustment terakhir untuk dental_material_id tertentu
        $lastAdjustment = StockCard::where('type', 'adjustment')
            ->where('dental_material_id', $request->dental_material_id)
            ->orderByDesc('id')
            ->first();

        // Ambil angka urutan terakhir dari reference_number
        $nextNumber = 1;
        if ($lastAdjustment && preg_match('/ADJ-(\d+)-' . $request->dental_material_id . '/', $lastAdjustment->reference_number, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        }

        // Buat reference number
        $reference_number = 'ADJ-' . $nextNumber . '-' . $request->dental_material_id;

        // Ambil stok terakhir untuk hitung remaining_stock
        $last = StockCard::where('dental_material_id', $request->dental_material_id)
            ->orderByDesc('date')
            ->orderByDesc('id') // jaga-jaga kalau tanggal sama, pakai ID
            ->first();

        $prev_remaining = $last ? $last->remaining_stock : 0;
        $quantity = $request->quantity_in;

        $remaining = $prev_remaining + $quantity;
        $avg_price = $last ? $last->average_price : 0;

        StockCard::create([
            'dental_material_id' => $request->dental_material_id,
            'date' => $request->date,
            'reference_number' => $reference_number,
            'price_in' => null,
            'price_out' => null,
            'quantity_in' => $quantity > 0 ? $quantity : 0,
            'quantity_out' => $quantity < 0 ? abs($quantity) : 0,
            'remaining_stock' => $remaining,
            'average_price' => $avg_price,
            'type' => 'adjustment',
            'note' => $request->note,
            'updated_by' => auth()->id(), 
        ]);

        return redirect()->route('dashboard.stock_cards.index')->with('success', 'Stock adjusted successfully.');
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(StockCard $stockCard)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StockCard $stockCard)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StockCard $stockCard)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockCard $stockCard)
    {
        //
    }
}
