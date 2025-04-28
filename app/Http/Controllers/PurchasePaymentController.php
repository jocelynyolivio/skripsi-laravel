<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use Illuminate\Http\Request;
use App\Models\JournalDetail;
use App\Models\ChartOfAccount;
use App\Models\PurchaseInvoice;
use App\Models\PurchasePayment;

class PurchasePaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($purchaseInvoiceId)
    {
        $invoice = PurchaseInvoice::findOrFail($purchaseInvoiceId);
        $coas = ChartOfAccount::all();

        return view('dashboard.purchase_payments.create', compact('invoice', 'coas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd('masuk store purchase payment');
        $request->validate([
            'purchase_invoice_id' => 'required|exists:purchase_invoices,id',
            'purchase_amount'     => 'required|numeric|min:1',
            'payment_date'        => 'required|date',
            'coa_id'              => 'required|exists:chart_of_accounts,id',
            'notes'               => 'nullable|string',
            'payment_method'      => 'nullable|string',
        ]);

        $invoice = PurchaseInvoice::findOrFail($request->purchase_invoice_id);

        $latestPayment = $invoice->payments()->latest()->first();
        $totalDebt = $latestPayment ? $latestPayment->total_debt : $invoice->grand_total;

        if ($request->purchase_amount > $totalDebt) {
            return back()->with('error', 'Payment amount exceeds the remaining debt.');
        }

        // Create Payment
        $payment = PurchasePayment::create([
            'purchase_invoice_id' => $invoice->id,
            'coa_id'              => $request->coa_id,
            'purchase_amount'     => $request->purchase_amount,
            'total_debt'          => max(0, $totalDebt - $request->purchase_amount),
            'payment_status'      => (max(0, $totalDebt - $request->purchase_amount) > 0) ? 'partial' : 'paid',
            'notes'               => $request->notes,
            'payment_date'        => $request->payment_date,
        ]);

        // Create Journal Entry
        $journalEntry = JournalEntry::create([
            'entry_date'  => $request->payment_date,
            'description' => 'Payment for Purchase Invoice: ' . $invoice->invoice_number,
        ]);

        $accountsPayable = ChartOfAccount::where('name', 'Utang Usaha')->value('id');

        JournalDetail::create([
            'journal_entry_id' => $journalEntry->id,
            'coa_id'           => $accountsPayable,
            'debit'            => $request->purchase_amount,
            'credit'           => 0,
        ]);

        JournalDetail::create([
            'journal_entry_id' => $journalEntry->id,
            'coa_id'           => $request->coa_id,
            'debit'            => 0,
            'credit'           => $request->purchase_amount,
        ]);

        return redirect()->route('dashboard.purchases.index')->with('success', 'Payment added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchasePayment $purchasePayment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PurchasePayment $purchasePayment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchasePayment $purchasePayment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchasePayment $purchasePayment)
    {
        //
    }
}
