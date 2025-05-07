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
        // dd($request);

        $invoice = PurchaseInvoice::findOrFail($request->purchase_invoice_id);
        // dd($invoice);
        $latestPayment = $invoice->latestPayment()->first();
        // dd($latestPayment);
        $totalDebt = $latestPayment ? $latestPayment->total_debt : $invoice->grand_total;
        // dd($totalDebt);

        if ($request->purchase_amount > $totalDebt) {
            return back()->with('error', 'Payment amount exceeds the remaining debt.');
        }
        // dd('dor');

        $purchaseAmount = $request->purchase_amount;
        // dd($purchaseAmount);
        $grandTotal = $invoice->grand_total;
        // dd($grandTotal);
        $totalDebt = $grandTotal - $purchaseAmount;
        // dd($totalDebt);
        $paymentStatus = ($totalDebt > 0) ? 'partial' : 'paid';

        // dd($purchaseAmount);

        if ($purchaseAmount > 0) {
            PurchasePayment::create([
                'purchase_invoice_id' => $invoice->id,
                'coa_id'              => $request->coa_id,
                'purchase_amount'     => $request->purchase_amount,
                'total_debt'          => max(0, $totalDebt),
                'payment_status'      => (max(0, $totalDebt - $request->purchase_amount) > 0) ? 'partial' : 'paid',
                'notes'               => $request->notes,
                'payment_date'        => $request->payment_date,
                'payment_method' => $request->payment_method
            ]);
        }
        // Create Journal Entry
$journalEntry = JournalEntry::create([
    'entry_date'  => $request->payment_date,
    'description' => 'Pembayaran atas Invoice Pembelian: ' . $invoice->invoice_number,
]);

$accountsPayable = ChartOfAccount::where('name', 'Utang Usaha')->value('id');
$cashOrBank      = $request->coa_id; // sudah dipilih user di form (Kas atau Bank)

// Debit: Utang Usaha (mengurangi hutang)
JournalDetail::create([
    'journal_entry_id' => $journalEntry->id,
    'coa_id'           => $accountsPayable,
    'debit'            => $purchaseAmount,
    'credit'           => 0,
]);

// Credit: Kas/Bank (mengurangi uang kas/bank)
JournalDetail::create([
    'journal_entry_id' => $journalEntry->id,
    'coa_id'           => $cashOrBank,
    'debit'            => 0,
    'credit'           => $purchaseAmount,
]);


        // // Create Journal Entry
        // $journalEntry = JournalEntry::create([
        //     'entry_date'  => $request->payment_date,
        //     'description' => 'Payment for Purchase Invoice: ' . $invoice->invoice_number,
        // ]);

        // $inventoryAccount = ChartOfAccount::where('name', 'Persediaan Barang Medis')->value('id');
        // $accountsPayable  = ChartOfAccount::where('name', 'Utang Usaha')->value('id');

        // // Debit: Persediaan
        // JournalDetail::create([
        //     'journal_entry_id' => $journalEntry->id,
        //     'coa_id'           => $inventoryAccount,
        //     'debit'            => $invoice->grand_total,
        //     'credit'           => 0,
        // ]);

        // // Jika ada diskon, catat sebagai kredit ke akun diskon
        // if ($request->discount > 0) {
        //     $purchaseDiscountAccount = ChartOfAccount::where('name', 'Diskon Pembelian')->value('id');
        //         JournalDetail::create([
        //         'journal_entry_id' => $journalEntry->id,
        //         'coa_id'           => $purchaseDiscountAccount,
        //         'debit'            => 0,
        //         'credit'           => $request->discount,
        //     ]);
        // }

        // // Jika ada ongkos kirim, catat sebagai debit ke akun ongkos kirim
        // if ($request->ongkos_kirim > 0) {
        //     $shippingCostAccount = ChartOfAccount::where('name', 'Beban Pengiriman Pembelian')->value('id');

        //     JournalDetail::create([
        //         'journal_entry_id' => $journalEntry->id,
        //         'coa_id'           => $shippingCostAccount,
        //         'debit'            => $request->ongkos_kirim,
        //         'credit'           => 0,
        //     ]);
        // }

        // // Credit: Kas (jika ada pembayaran)
        // if ($purchaseAmount > 0) {
        //     JournalDetail::create([
        //         'journal_entry_id' => $journalEntry->id,
        //         'coa_id'           => $request->coa_id,
        //         'debit'            => 0,
        //         'credit'           => $purchaseAmount,
        //     ]);
        // }

        // // Credit: Utang Usaha (jika masih ada sisa pembayaran)
        // if ($totalDebt > 0) {
        //     JournalDetail::create([
        //         'journal_entry_id' => $journalEntry->id,
        //         'coa_id'           => $accountsPayable,
        //         'debit'            => 0,
        //         'credit'           => $totalDebt,
        //     ]);
        // }
// AAAAA
        // JournalDetail::create([
        //     'journal_entry_id' => $journalEntry->id,
        //     'coa_id'           => $accountsPayable,
        //     'debit'            => $request->purchase_amount,
        //     'credit'           => 0,
        // ]);

        // JournalDetail::create([
        //     'journal_entry_id' => $journalEntry->id,
        //     'coa_id'           => $request->coa_id,
        //     'debit'            => 0,
        //     'credit'           => $request->purchase_amount,
        // ]);

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
