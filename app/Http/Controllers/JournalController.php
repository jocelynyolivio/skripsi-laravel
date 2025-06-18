<?php

namespace App\Http\Controllers;

use App\Models\StockCard;
use Illuminate\Support\Str;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use App\Models\JournalDetail;
use App\Models\ChartOfAccount;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class JournalController extends Controller
{
    public function index()
    {
        // dd('jhai');
        $journals = JournalEntry::with('details.account')->get();

        return view('dashboard.journals.index', [
            'title' => 'Journal Entries',
            'journals' => $journals
        ]);
    }

    public function create()
    {
        $coas = ChartOfAccount::all();
        return view('dashboard.journals.create', compact('coas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'entry_date' => 'required|date',
            'description' => 'nullable|string',
            'coa_in' => 'required|exists:chart_of_accounts,id',
            'coa_out' => 'required|exists:chart_of_accounts,id|different:coa_in',
            'amount' => 'required|numeric|min:0.01',
        ]);

        // Buat journal entry utama
        $entry = JournalEntry::create([
            'entry_date' => $request->entry_date,
            'description' => $request->description,
        ]);

        // Tambah detail debit
        JournalDetail::create([
            'journal_entry_id' => $entry->id,
            'coa_id' => $request->coa_in,
            'debit' => $request->amount,
            'credit' => 0,
        ]);

        // Tambah detail kredit
        JournalDetail::create([
            'journal_entry_id' => $entry->id,
            'coa_id' => $request->coa_out,
            'debit' => 0,
            'credit' => $request->amount,
        ]);

        return redirect()->route('dashboard.journals.index')->with('success', 'Jurnal berhasil dibuat.');
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
                // dd('masuk if');

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
}
