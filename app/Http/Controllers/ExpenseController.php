<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use App\Models\JournalDetail;
use App\Models\ChartOfAccount;
use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;

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

        return view('dashboard.expenses.create', compact('coa','suppliers'));
    }

    public function store(Request $request)
    {

        try{
        $request->validate([
            'expense_date' => 'required|date',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'coa_out' => 'required|exists:chart_of_accounts,id',
            'coa_in' => 'required|exists:chart_of_accounts,id',
            'reference_number' => 'nullable|string|max:50',
            'supplier_id' => 'required|numeric',
            'payment_method' => 'required'
        ]);

        // dd($request);

        $referenceNumber = 'EXP-' . strtoupper(uniqid());


        Expense::create([
            'expense_date' => $request->expense_date,
            'created_by' => auth()->id(),
            'description' => $request->description,
            'amount' => $request->amount,
            'coa_out' => $request->coa_out,
            'coa_in' => $request->coa_in,
            'reference_number' => $referenceNumber,
            $request->reference_number,
            'supplier_id' => $request->supplier_id,
            'payment_method' => $request->payment_method
        ]);

        // Simpan Journal Entry
        $journal = JournalEntry::create([
            'transaction_id' => $request->id,
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

        return redirect()->route('dashboard.expenses.index')->with('success', 'Expense recorded successfully!');
    } catch (\Exception $e) {
        // dd($e);
        return redirect()->back()->with('error', 'Gagal memuat laporan: ' . $e->getMessage());
    }
}

public function edit(Expense $expense)
{
    $coa = ChartOfAccount::all();
    $suppliers = Supplier::all();

    return view('dashboard.expenses.edit', compact('expense', 'coa', 'suppliers'));
}

public function update(Request $request, Expense $expense)
{
    $request->validate([
        'expense_date' => 'required|date',
        'description' => 'required|string|max:255',
        'amount' => 'required|numeric|min:0',
        'coa_out' => 'required|exists:chart_of_accounts,id',
        'coa_in' => 'required|exists:chart_of_accounts,id',
        'supplier_id' => 'nullable|exists:suppliers,id',
        'payment_method' => 'required|string|max:100',
        'reference_number' => 'nullable|string|max:50',
    ]);

    $expense->update([
        'expense_date' => $request->expense_date,
        'description' => $request->description,
        'amount' => $request->amount,
        'coa_out' => $request->coa_out,
        'coa_in' => $request->coa_in,
        'supplier_id' => $request->supplier_id,
        'payment_method' => $request->payment_method,
        'reference_number' => $request->reference_number,
    ]);

    return redirect()->route('dashboard.expenses.index')->with('success', 'Expense updated successfully!');
}

    // public function destroy(Expense $expense)
    // {
    //     $expense->delete();
    //     return redirect()->route('dashboard.expenses.index')->with('success', 'Expense deleted successfully!');
    // }
    public function destroy(Expense $expense)
    {
        // Cari JournalEntry yang sesuai dengan deskripsi Expense (atau sesuaikan jika pakai relasi atau transaction_id)
        $journal = JournalEntry::where('entry_date', $expense->expense_date)
            ->where('description', 'Expense: ' . $expense->description)
            ->whereHas('details', function ($query) use ($expense) {
                $query->where('debit', $expense->amount);
            })
            ->first();

        if ($journal) {
            // Hapus JournalDetail yang terkait
            JournalDetail::where('journal_entry_id', $journal->id)->delete();

            // Hapus JournalEntry
            $journal->delete();
        }

        // Hapus Expense
        $expense->delete();

        return redirect()->route('dashboard.expenses.index')->with('success', 'Expense deleted successfully!');
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
