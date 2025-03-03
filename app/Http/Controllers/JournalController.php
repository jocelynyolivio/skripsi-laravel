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

    /**
     * Display the specified resource.
     */
    // public function show($id)
    // {
    //     $journal = JournalEntry::with('details.account')->findOrFail($id);

    //     return view('dashboard.journals.show', [
    //         'title' => 'Journal Details',
    //         'journal' => $journal
    //     ]);
    // }

    public function show($id)
    {
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

        // ini haruse nti ambil dari stock card
        // Jika ini adalah Journal untuk HPP, cari dari procedure_materials
        if ($isHPP && $journal->transaction_id) {
            $transaction = \App\Models\Transaction::find($journal->transaction_id);

            // Ambil Medical Record dari Transaction
            $medicalRecord = \App\Models\MedicalRecord::where('id', $transaction->medical_record_id)->first();

            // Cek apakah Medical Record ada
            if ($medicalRecord) {
                // Ambil Semua Prosedur yang terkait dengan Medical Record
                $procedures = $medicalRecord->procedures;

                // Loop Semua Prosedur untuk Ambil Bahan Dental yang Digunakan
                foreach ($procedures as $procedure) {
                    foreach ($procedure->dentalMaterials as $material) {
                        $quantityUsed = $material->pivot->quantity;
                        $unitPrice = $material->unit_price;
                        $totalPrice = $quantityUsed * $unitPrice;

                        // Simpan Detail ke Array
                        $hppDetails[] = [
                            'name' => $material->name,
                            'quantity' => $quantityUsed,
                            'unit_price' => $unitPrice,
                            'total_price' => $totalPrice
                        ];

                        // Akumulasi Total HPP
                        $totalHPP += $totalPrice;
                    }
                }
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
