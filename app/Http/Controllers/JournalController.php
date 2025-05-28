<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // dd('jhai');
        $journals = JournalEntry::with('details.account')->get();

        return view('dashboard.journals.index', [
            'title' => 'Journal Entries',
            'journals' => $journals
        ]);
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

    public function show($id)
    {
        // dd('haui');
        // Ambil Journal Entry Berdasarkan ID
        $journal = JournalEntry::with('details.account')->findOrFail($id);

        // Ambil Semua Detail dari Journal Entry
        $journalDetails = $journal->details()->with('account')->get();

        $hppDetails = [];
        $totalHPP = 0;
        $isHPP = false;

        // Cek apakah ini adalah Journal Entry untuk HPP
        foreach ($journalDetails as $detail) {
            if (strpos($detail->account->name, 'HPP') !== false) {
                $isHPP = true;
                break;
            }
        }

        // dd('hau');

        // dd($isHPP);

        if ($isHPP && $journal->medical_record_id) {
            if ($journal->medical_record_id) {
                dd('masuk if');

                // Cari semua stock keluar yang berhubungan dengan medical record ini
                $stockCards = \App\Models\StockCard::where('reference_number', 'LIKE', 'MR-' . $journal->medical_record_id)
                    ->whereNotNull('price_out') // Pastikan ini adalah stok yang keluar
                    ->get();

                foreach ($stockCards as $stock) {
                    $quantityUsed = $stock->quantity_out;
                    $unitPrice = $stock->average_price;
                    $totalPrice = $quantityUsed * $unitPrice;

                    // Simpan ke HPP Details
                    $hppDetails[] = [
                        'name' => $stock->dentalMaterial->name, // Ambil nama material dari relasi
                        'quantity' => $quantityUsed,
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice
                    ];

                    // Akumulasi Total HPP
                    $totalHPP += $totalPrice;
                }
            }
            return view('dashboard.journals.show', [
                'title' => 'Journal Details',
                'journal' => $journal,
                'journalDetails' => $journalDetails,
                'hppDetails' => $hppDetails,
                'totalHPP' => $totalHPP,
                'isHPP' => $isHPP
            ]);
        } else {
            return view('dashboard.journals.show', [
                'title' => 'Journal Details',
                'journal' => $journal,
                'journalDetails' => $journalDetails,
                'isHPP' => $isHPP
            ]);
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
