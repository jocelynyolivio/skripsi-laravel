<?php

namespace App\Http\Controllers;

use App\Models\StockCard;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use App\Models\JournalDetail;
use App\Models\ChartOfAccount;
use App\Models\DentalMaterial;
use Illuminate\Support\Facades\DB;

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
    // dd('masuk adjust');
    // Validasi input dari form
    $request->validate([
        'dental_material_id' => 'required|exists:dental_materials,id',
        'date' => 'required|date',
        'quantity_in' => 'required|integer', // Bisa positif (menambah) atau negatif (mengurangi)
        'note' => 'nullable|string',
    ]);

    // dd($request);

    // Memulai Database Transaction untuk menjaga konsistensi data
    DB::beginTransaction();
    try {
        // --- Bagian ini adalah kode asli Anda untuk membuat Reference Number ---
        $lastAdjustment = StockCard::where('type', 'adjustment')
            ->where('dental_material_id', $request->dental_material_id)
            ->orderByDesc('id')
            ->first();

            // dd($lastAdjustment);

        $nextNumber = 1;
        if ($lastAdjustment && preg_match('/ADJ-(\d+)-' . $request->dental_material_id . '/', $lastAdjustment->reference_number, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        }
        $reference_number = 'ADJ-' . $nextNumber . '-' . $request->dental_material_id;

        // --- Bagian ini adalah kode asli Anda untuk menghitung stok ---
        $last = StockCard::where('dental_material_id', $request->dental_material_id)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->first();

            // dd($last);

        $prev_remaining = $last ? $last->remaining_stock : 0;
        $quantity = (int) $request->quantity_in; // Kuantitas penyesuaian (+ atau -)
        $remaining = $prev_remaining + $quantity;
        $avg_price = $last ? $last->average_price : 0; // Menggunakan harga rata-rata terakhir

        // Langkah 1: Buat Stock Card baru (seperti kode Anda)
        // Kita simpan hasilnya ke variabel $stockCard untuk referensi
        $stockCard = StockCard::create([
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

        // dd('tersimpan stock card');

        // Langkah 2: Hitung nilai finansial dari penyesuaian & buat jurnal jika perlu
        $adjustmentValue = abs($quantity) * $avg_price;
        // dd($adjustmentValue);

        if ($adjustmentValue > 0) {
            // Ambil ID Akun dari database
            $inventoryCoaId = ChartOfAccount::where('name', 'Persediaan Barang Medis')->value('id');
            // dd($inventoryCoaId);

            if (!$inventoryCoaId) {
                throw new \Exception('Akun "Persediaan Bahan Medis" tidak ditemukan di Chart of Accounts.');
            }

            // dd('mas');

            // Buat Journal Entry
            $journal = JournalEntry::create([
                'entry_date' => $request->date,
                'description' => "Penyesuaian Stok: " . ($request->note ?? $reference_number),
            ]);
            // dd($journal);

            // Tentukan jurnal berdasarkan jenis penyesuaian (negatif/positif)
            if ($quantity < 0) { // STOK BERKURANG -> BEBAN
                $expenseCoaId = ChartOfAccount::where('name', 'Beban Penyesuaian Persediaan')->value('id');
                if (!$expenseCoaId) {
                    throw new \Exception('Akun "Beban Penyesuaian Persediaan" tidak ditemukan.');
                }
                
                // (Debit) Beban bertambah, (Kredit) Aset Persediaan berkurang
                JournalDetail::create(['journal_entry_id' => $journal->id, 'coa_id' => $expenseCoaId, 'debit' => $adjustmentValue, 'credit' => 0]);
                JournalDetail::create(['journal_entry_id' => $journal->id, 'coa_id' => $inventoryCoaId, 'debit' => 0, 'credit' => $adjustmentValue]);

            } else { // STOK BERTAMBAH -> PENDAPATAN
                $incomeCoaId = ChartOfAccount::where('name', 'Pendapatan Penyesuaian Persediaan')->value('id');
                if (!$incomeCoaId) {
                    throw new \Exception('Akun "Pendapatan Penyesuaian Persediaan" tidak ditemukan.');
                }

                // (Debit) Aset Persediaan bertambah, (Kredit) Pendapatan bertambah
                JournalDetail::create(['journal_entry_id' => $journal->id, 'coa_id' => $inventoryCoaId, 'debit' => $adjustmentValue, 'credit' => 0]);
                JournalDetail::create(['journal_entry_id' => $journal->id, 'coa_id' => $incomeCoaId, 'debit' => 0, 'credit' => $adjustmentValue]);
            }
        }

        // dd('ya');

        // Jika semua proses di atas berhasil, simpan semua perubahan ke database
        DB::commit();

        return redirect()->route('dashboard.stock_cards.index')->with('success', 'Stock adjusted successfully and journal entry created.');

    } catch (\Exception $e) {
        // Jika ada error di tengah jalan, batalkan semua proses yang sudah berjalan
        DB::rollBack();
        return redirect()->back()->with('error', 'Gagal menyesuaikan stok: ' . $e->getMessage())->withInput();
    }
}

    public function storeAdjustmentXXXXXXZ(Request $request)
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
