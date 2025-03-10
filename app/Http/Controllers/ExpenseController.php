<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use App\Models\JournalDetail;
use App\Models\ChartOfAccount;
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
        return view('dashboard.expenses.create', compact('coa'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'expense_date' => 'required|date',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'coa_out' => 'required|exists:chart_of_accounts,id',
            'coa_in' => 'required|exists:chart_of_accounts,id',
            'reference_number' => 'nullable|string|max:50',
        ]);
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
    }

    public function edit(Expense $expense)
    {
        $coa = ChartOfAccount::all();
        return view('dashboard.expenses.edit', compact('expense', 'coa'));
    }

    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'expense_date' => 'required|date',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'coa_out' => 'required|exists:chart_of_accounts,id',
            'coa_in' => 'required|exists:chart_of_accounts,id',
            'reference_number' => 'nullable|string|max:50',
        ]);

        $expense->update($request->all());

        return redirect()->route('dashboard.expenses.index')->with('success', 'Expense updated successfully!');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->route('dashboard.expenses.index')->with('success', 'Expense deleted successfully!');
    }
}
