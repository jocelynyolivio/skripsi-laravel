<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Supplier;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use App\Models\JournalDetail;
use App\Models\ChartOfAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with(['coaIn', 'coaOut', 'admin'])->latest()->get();


        return view('dashboard.expenses.index', compact('expenses'));
    }

    public function create()
    {
        $coa = ChartOfAccount::all();
        $suppliers = Supplier::all();

        return view('dashboard.expenses.create', compact('coa', 'suppliers'));
    }

    public function store(Request $request)
    {
        // dd('masuk store');
        $request->validate([
            'expense_date' => 'required|date',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'coa_out' => 'required|exists:chart_of_accounts,id',
            'coa_in' => 'required|exists:chart_of_accounts,id',
            'reference_number' => 'nullable|string|max:50',
            'supplier_id' => 'required|numeric',
            'payment_method' => 'required',
            'attachment_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);
        // dd($request);

        DB::beginTransaction();
        try {
            $attachmentPath = null; // Default path adalah null

            // Cek jika ada file yang di-upload
            if ($request->hasFile('attachment_file')) {
                // Simpan file di dalam folder 'storage/app/public/expenses'
                // dan dapatkan path-nya untuk disimpan ke database
                $attachmentPath = $request->file('attachment_file')->store('expenses', 'public');
            }

            $referenceNumber = 'EXP-' . strtoupper(uniqid());

            $expense = Expense::create([
                'expense_date' => $request->expense_date,
                'created_by' => auth()->id(),
                'description' => $request->description,
                'amount' => $request->amount,
                'status' => 'ACTIVE',
                'attachment_path' => $attachmentPath, // <-- SIMPAN PATH FILE DI SINI
                'coa_out' => $request->coa_out,
                'coa_in' => $request->coa_in,
                'reference_number' => $referenceNumber,
                'supplier_id' => $request->supplier_id,
                'payment_method' => $request->payment_method
            ]);

            // Simpan Journal Entry
            $journal = JournalEntry::create([
                'expense_id' => $request->id,
                'entry_date' => $request->expense_date,
                'description' => "Expense: " . $request->description,
            ]);

            // Simpan Journal Details (Debit - Beban)
            JournalDetail::create([
                'journal_entry_id' => $journal->id,
                'coa_id' => $request->coa_in, // Akun Beban
                'debit' => $request->amount,
                'credit' => 0,
            ]);

            // Simpan Journal Details (Kredit - Kas/Bank)
            JournalDetail::create([
                'journal_entry_id' => $journal->id,
                'coa_id' => $request->coa_out, // Akun Kas/Bank
                'debit' => 0,
                'credit' => $request->amount,
            ]);
            DB::commit();
            return redirect()->route('dashboard.expenses.index')->with('success', 'Expense recorded successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mencatat expense: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Expense $expense)
    {

        // Periksa apakah expense sudah pernah dibatalkan sebelumnya untuk menghindari duplikasi
        if ($expense->status === 'cancelled') {
            return redirect()->back()->with('error', 'Expense ini sudah pernah dibatalkan.');
        }

        // Gunakan DB Transaction untuk memastikan semua proses berhasil atau semua dibatalkan
        DB::beginTransaction();
        try {
            // Hapus file dari storage jika adaF
            if ($expense->attachment_path) {
                Storage::disk('public')->delete($expense->attachment_path);
            }
            // Langkah 1: Ubah status expense menjadi 'CANCELLED'
            $expense->update(['status' => 'cancelled']);

            // Langkah 2: Buat Jurnal Balik (Reverse Journal)
            $reverseJournal = JournalEntry::create([
                'expense_id' => $expense->id,
                'entry_date' => now(), // Tanggal pembatalan adalah hari ini
                'description' => "Jurnal balik untuk pembatalan expense #{$expense->reference_number}: " . $expense->description,
            ]);

            // Langkah 3: Buat detail jurnal baliknya

            // Asli: (Kredit) Kas/Bank -> Dibalik menjadi (DEBIT)
            JournalDetail::create([
                'journal_entry_id' => $reverseJournal->id,
                'coa_id' => $expense->coa_out, // coa_out adalah akun Kas/Bank
                'debit' => $expense->amount,   // Debit sejumlah nilai expense
                'credit' => 0,
            ]);

            // Asli: (Debit) Beban -> Dibalik menjadi (KREDIT)
            JournalDetail::create([
                'journal_entry_id' => $reverseJournal->id,
                'coa_id' => $expense->coa_in, // coa_in adalah akun Beban
                'debit' => 0,
                'credit' => $expense->amount, // Kredit sejumlah nilai expense
            ]);

            // Jika semua proses di atas berhasil, konfirmasi perubahan ke database
            DB::commit();

            return redirect()->route('dashboard.expenses.index')->with('success', 'Expense berhasil dibatalkan dan jurnal balik telah dibuat.');
        } catch (\Exception $e) {
            // Jika terjadi error di salah satu proses, batalkan semua perubahan
            DB::rollBack();

            return redirect()->back()->with('error', 'Gagal membatalkan expense: ' . $e->getMessage());
        }
    }

    public function duplicate(Expense $expense)
    {
        return view('dashboard.expenses.create', [
            'coa' => ChartOfAccount::all(),
            'suppliers' => Supplier::all(),
            'expense' => $expense,
        ]);
    }

    public function show(Expense $expense)
    {
        $expense->load(['coaOut', 'coaIn', 'admin']);

        return view('dashboard.expenses.show', compact('expense'));
    }
}
