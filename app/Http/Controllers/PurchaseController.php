<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\StockCard;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use App\Models\JournalDetail;
use App\Models\ChartOfAccount;
use App\Models\DentalMaterial;
use App\Models\PurchaseInvoice;
use App\Models\PurchasePayment;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = PurchaseInvoice::with(['supplier', 'payments' => function ($query) {
            $query->latest();
        }])->get();

        $coa = ChartOfAccount::all(); // Ambil semua COA

        return view('dashboard.purchases.index', compact('purchases', 'coa'));
    }

    public function create()
    {
        $materials = DentalMaterial::all();
        $suppliers = Supplier::all();
        $cashAccounts = ChartOfAccount::all();
        return view('dashboard.purchases.create', compact('suppliers', 'materials', 'cashAccounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id'    => 'required|exists:suppliers,id',
            'purchase_date'  => 'required|date',
            'total_amount'   => 'required|numeric',
            'payment_amount'   => 'required|numeric',
        ]);

        // Generate nomor invoice otomatis
        $lastInvoice = PurchaseInvoice::orderByDesc('id')->first();
        $nextNumber = $lastInvoice ? ((int) substr($lastInvoice->invoice_number, 4)) + 1 : 1;
        $invoiceNumber = 'INV-' . $nextNumber;

        // Simpan Purchase Invoice
        $invoice = PurchaseInvoice::create([
            'invoice_number' => $invoiceNumber,
            'supplier_id'    => $request->supplier_id,
            'purchase_date'  => $request->purchase_date,
            'total_amount'   => $request->total_amount,
            'grand_total'    => $request->total_amount,
        ]);

        // Simpan Purchase Details
        foreach ($request->dental_material_id as $index => $material_id) {
            \App\Models\PurchaseDetail::create([
                'purchase_invoice_id' => $invoice->id,
                'dental_material_id'  => $material_id,
                'quantity'            => $request->quantity[$index],
                'unit'                => $request->unit[$index],
                'unit_price'          => $request->unit_price[$index],
                'subtotal'            => $request->subtotal[$index],
            ]);
        }

        // Proses Purchase
        $purchaseAmount = $request->payment_amount;
        // dd($purchaseAmount);
        $totalDebt = $request->total_amount - $purchaseAmount;
        $paymentStatus = 'unpaid';

        if ($purchaseAmount == $request->amount) {
            $paymentStatus = 'paid';
        } elseif ($purchaseAmount > 0 && $purchaseAmount < $request->amount) {
            $paymentStatus = 'partial';
        }

        // Simpan Purchase
        PurchasePayment::create([
            'purchase_invoice_id' => $invoice->id,
            'coa_id' => $request->coa_id,
            'purchase_amount' => $purchaseAmount,
            'total_debt' => $totalDebt,
            'payment_status' => $paymentStatus,
            'notes' => $request->notes,
        ]);

        // jurnal otomatis untuk purchase
        // karena psti masuk ke persediaan barang medis jadi akun e 13 
        $inventoryAccount = 13;

        // Buat Journal Entry
        $journalEntry = JournalEntry::create([
            'entry_date' => $request->purchase_date,
            'description' => 'Purchase Payment for Purchase Invoice Number ' . $invoice->id,
        ]);

        if ($purchaseAmount > 0) {
            // Debit: Persediaan Barang
            JournalDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'coa_id' => $inventoryAccount,
                'debit' => $request->total_amount,
                'credit' => 0
            ]);

            // Kredit: Kas/Bank (COA yang dipilih)
            JournalDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'coa_id' => $request->coa_id,
                'debit' => 0,
                'credit' => $purchaseAmount
            ]);
        }

        // ** Jika Ada Hutang (Partial atau Unpaid) **
        if ($totalDebt > 0) {
            // Kredit: Utang Usaha (Total Debt)
            JournalDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'coa_id' => 14, // Utang Usaha
                'debit' => 0,
                'credit' => $totalDebt
            ]);
        }

        return redirect()->route('dashboard.purchases.index')->with('success', 'Purchase Invoice created successfully.');
    }

    public function edit(PurchaseInvoice $purchase)
    {
        $suppliers = Supplier::all();
        return view('dashboard.purchases.edit', compact('purchase', 'suppliers'));
    }

    public function update(Request $request, PurchaseInvoice $purchase)
    {
        $request->validate([
            'invoice_number' => 'required|unique:purchase_invoices,invoice_number,' . $purchase->id,
            'supplier_id'    => 'required|exists:suppliers,id',
            'purchase_date'  => 'required|date',
            'total_amount'   => 'required|numeric',
            'discount'       => 'nullable|numeric',
            'grand_total'    => 'required|numeric',
            'status'         => 'required|in:pending,paid,cancelled',
        ]);

        $purchase->update($request->all());

        return redirect()->route('dashboard.purchases.index')->with('success', 'Purchase Invoice updated successfully.');
    }

    public function destroy(PurchaseInvoice $purchase)
    {
        $purchase->delete();
        return redirect()->route('dashboard.purchases.index')->with('success', 'Purchase Invoice deleted successfully.');
    }

    public function updateStockCard($dentalMaterialId, $quantity, $price, $referenceNumber)
    {
        $latestStock = StockCard::where('dental_material_id', $dentalMaterialId)
            ->latest('created_at')
            ->first();

        // Hitung stok dan harga rata-rata baru
        $newStock = ($latestStock ? $latestStock->remaining_stock : 0) + $quantity;
        $newAveragePrice = $latestStock
            ? (($latestStock->remaining_stock * $latestStock->average_price) + ($quantity * $price)) / $newStock
            : $price;

        // Simpan ke kartu stok
        StockCard::create([
            'dental_material_id' => $dentalMaterialId,
            'date' => now(),
            'reference_number' => $referenceNumber,
            'price_in' => $price,
            'quantity_in' => $quantity,
            'remaining_stock' => $newStock,
            'average_price' => $newAveragePrice
        ]);
    }

    public function receive(PurchaseInvoice $purchase)
    {
        return view('dashboard.purchases.receive', compact('purchase'));
    }

    public function storeReceived(Request $request, PurchaseInvoice $purchase)
    {
        foreach ($request->received_quantity as $materialId => $quantityReceived) {
            if ($quantityReceived > 0) {
                $detail = $purchase->details()->where('dental_material_id', $materialId)->first();
                $this->updateStockCard($materialId, $quantityReceived, $detail->unit_price, $purchase->invoice_number);
            }
        }

        $purchase->update(['status' => 'received']);

        return redirect()->route('dashboard.purchases.index')->with('success', 'Stock berhasil diperbarui.');
    }

    public function payDebt(Request $request)
{
    $request->validate([
        'purchase_id' => 'required|exists:purchase_invoices,id',
        'amount' => 'required|numeric|min:1',
        'payment_date' => 'required|date',
        'coa_id' => 'required|exists:chart_of_accounts,id',
        'notes' => 'nullable|string',
    ]);

    // dd($request);

    $purchase = PurchaseInvoice::findOrFail($request->purchase_id);
    $latestPayment = $purchase->payments()->latest()->first();
    $totalDebt = $latestPayment ? $latestPayment->total_debt : $purchase->grand_total;

    if ($request->amount > $totalDebt) {
        return redirect()->back()->with('error', 'Payment amount exceeds the remaining debt.');
    }

    PurchasePayment::create([
        'purchase_invoice_id' => $purchase->id,
        'coa_id' => $request->coa_id,
        'purchase_amount' => $request->amount,
        'total_debt' => max(0, $totalDebt - $request->amount),
        'payment_status' => 'unpaid',
        'notes' => $request->notes,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Jurnal untuk Pembayaran Hutang
    $journalEntry = JournalEntry::create([
        'entry_date' => $request->payment_date,
        'description' => 'Pembayaran Hutang untuk Purchase ID: ' . $purchase->id,
    ]);

    // Debit: Utang Usaha
    JournalDetail::create([
        'journal_entry_id' => $journalEntry->id,
        'coa_id' => 14, // Utang Usaha
        'debit' => $request->amount,
        'credit' => 0
    ]);

    // Kredit: Kas/Bank (COA yang dipilih)
    JournalDetail::create([
        'journal_entry_id' => $journalEntry->id,
        'coa_id' => $request->coa_id,
        'debit' => 0,
        'credit' => $request->amount
    ]);

    return redirect()->route('dashboard.purchases.index')->with('success', 'Payment added successfully.');
}}