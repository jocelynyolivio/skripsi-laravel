<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\StockCard;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use App\Models\JournalDetail;
use App\Models\ChartOfAccount;
use App\Models\DentalMaterial;
use App\Models\PurchaseDetail;
use App\Models\PurchaseInvoice;
use App\Models\PurchasePayment;
use App\Models\PurchaseRequest;
use Illuminate\Support\Facades\DB;

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

    // public function create()
    // {
    //     $materials = DentalMaterial::all();
    //     $suppliers = Supplier::all();
    //     $cashAccounts = ChartOfAccount::all();
    //     return view('dashboard.purchases.create', compact('suppliers', 'materials', 'cashAccounts'));
    // }
    public function create(PurchaseRequest $purchaseRequest = null)
    {
        $data = [
            'suppliers' => Supplier::all(),
            'materials' => DentalMaterial::all(),
            'cashAccounts' => ChartOfAccount::all(),
        ];

        if ($purchaseRequest) {
            $data['purchaseRequest'] = $purchaseRequest;
        }

        return view('dashboard.purchases.create', $data);
    }
    // awal tanpa request
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'supplier_id'    => 'required|exists:suppliers,id',
    //         'purchase_date'  => 'required|date',
    //         'total_amount'   => 'required|numeric',
    //         'payment_amount'   => 'required|numeric',
    //     ]);

    //     // Generate nomor invoice otomatis
    //     $lastInvoice = PurchaseInvoice::orderByDesc('id')->first();
    //     $nextNumber = $lastInvoice ? ((int) substr($lastInvoice->invoice_number, 4)) + 1 : 1;
    //     $invoiceNumber = 'INV-' . $nextNumber;

    //     // Simpan Purchase Invoice
    //     $invoice = PurchaseInvoice::create([
    //         'invoice_number' => $invoiceNumber,
    //         'supplier_id'    => $request->supplier_id,
    //         'purchase_date'  => $request->purchase_date,
    //         'total_amount'   => $request->total_amount,
    //         'grand_total'    => $request->total_amount,
    //     ]);

    //     // Simpan Purchase Details
    //     foreach ($request->dental_material_id as $index => $material_id) {
    //         \App\Models\PurchaseDetail::create([
    //             'purchase_invoice_id' => $invoice->id,
    //             'dental_material_id'  => $material_id,
    //             'quantity'            => $request->quantity[$index],
    //             'unit'                => $request->unit[$index],
    //             'unit_price'          => $request->unit_price[$index],
    //             'subtotal'            => $request->subtotal[$index],
    //         ]);
    //     }

    //     // Proses Purchase
    //     $purchaseAmount = $request->payment_amount;
    //     // dd($purchaseAmount);
    //     $totalDebt = $request->total_amount - $purchaseAmount;
    //     $paymentStatus = 'unpaid';

    //     if ($purchaseAmount == $request->amount) {
    //         $paymentStatus = 'paid';
    //     } elseif ($purchaseAmount > 0 && $purchaseAmount < $request->amount) {
    //         $paymentStatus = 'partial';
    //     }

    //     // Simpan Purchase
    //     PurchasePayment::create([
    //         'purchase_invoice_id' => $invoice->id,
    //         'coa_id' => $request->coa_id,
    //         'purchase_amount' => $purchaseAmount,
    //         'total_debt' => $totalDebt,
    //         'payment_status' => $paymentStatus,
    //         'notes' => $request->notes,
    //     ]);

    //     // jurnal otomatis untuk purchase
    //     // karena psti masuk ke persediaan barang medis jadi akun e 13 
    //     $inventoryAccount = 13;

    //     // Buat Journal Entry
    //     $journalEntry = JournalEntry::create([
    //         'entry_date' => $request->purchase_date,
    //         'description' => 'Purchase Payment for Purchase Invoice Number ' . $invoice->id,
    //     ]);

    //     if ($purchaseAmount > 0) {
    //         // Debit: Persediaan Barang
    //         JournalDetail::create([
    //             'journal_entry_id' => $journalEntry->id,
    //             'coa_id' => $inventoryAccount,
    //             'debit' => $request->total_amount,
    //             'credit' => 0
    //         ]);

    //         // Kredit: Kas/Bank (COA yang dipilih)
    //         JournalDetail::create([
    //             'journal_entry_id' => $journalEntry->id,
    //             'coa_id' => $request->coa_id,
    //             'debit' => 0,
    //             'credit' => $purchaseAmount
    //         ]);
    //     }

    //     // ** Jika Ada Hutang (Partial atau Unpaid) **
    //     if ($totalDebt > 0) {
    //         // Kredit: Utang Usaha (Total Debt)
    //         JournalDetail::create([
    //             'journal_entry_id' => $journalEntry->id,
    //             'coa_id' => 14, // Utang Usaha
    //             'debit' => 0,
    //             'credit' => $totalDebt
    //         ]);
    //     }

    //     return redirect()->route('dashboard.purchases.index')->with('success', 'Purchase Invoice created successfully.');
    // }

    // gabungan dengan request
    // public function store(Request $request, PurchaseRequest $purchaseRequest = null)
    // {
    //     $request->validate([
    //         'supplier_id'    => 'required|exists:suppliers,id',
    //         'purchase_date'  => 'required|date',
    //         'total_amount'   => 'required|numeric',
    //         'payment_amount' => 'required|numeric|min:0',
    //         'coa_id'         => 'required|exists:chart_of_accounts,id', // COA untuk pembayaran
    //         'dental_material_id' => 'required|array',
    //         'quantity' => 'required|array',
    //         'total_price' => 'required|array',
    //         // 'purchase_request_id' => $request->purchase_request_id,
    //     ]);

    //     // **1. Generate Invoice Number**
    //     $lastInvoice = PurchaseInvoice::orderByDesc('id')->first();
    //     $nextNumber = $lastInvoice ? ((int) substr($lastInvoice->invoice_number, 4)) + 1 : 1;
    //     $invoiceNumber = 'INV-' . $nextNumber;

    //     // **2. Simpan Purchase Invoice**
    //     $invoice = PurchaseInvoice::create([
    //         'invoice_number' => $invoiceNumber,
    //         'supplier_id'    => $request->supplier_id,
    //         'purchase_date'  => $request->purchase_date,
    //         'total_amount'   => $request->total_amount,
    //         'grand_total'    => $request->total_amount, // Bisa ditambah diskon nanti
    //     ]);

    //     // Jika berasal dari purchase request, tambahkan relasinya
    //     if ($purchaseRequest) {
    //         $invoiceData['purchase_request_id'] = $purchaseRequest->id;
    //     }
    //     $invoice = PurchaseInvoice::create($invoiceData);

    //     // **3. Simpan Purchase Details**
    //     foreach ($request->dental_material_id as $index => $material_id) {
    //         $material = DentalMaterial::findOrFail($material_id);
    //         $quantity = $request->quantity[$index];
    //         $totalPrice = $request->total_price[$index];
    //         $unitPrice = ($quantity > 0) ? $totalPrice / $quantity : 0;

    //         PurchaseDetail::create([
    //             'purchase_invoice_id' => $invoice->id,
    //             'dental_material_id'  => $material->id,
    //             'quantity'            => $quantity,
    //             'unit_price'          => $unitPrice,
    //             'subtotal'            => $totalPrice
    //         ]);
    //     }

    //     // **4. Proses Purchase Payment**
    //     $purchaseAmount = $request->payment_amount;
    //     $totalDebt = $request->total_amount - $purchaseAmount;
    //     $paymentStatus = ($totalDebt > 0) ? 'partial' : 'paid';

    //     // Jika ada pembayaran, simpan ke PurchasePayment
    //     if ($purchaseAmount > 0) {
    //         PurchasePayment::create([
    //             'purchase_invoice_id' => $invoice->id,
    //             'coa_id' => $request->coa_id, // COA untuk pembayaran (Kas/Bank)
    //             'purchase_amount' => $purchaseAmount,
    //             'total_debt' => $totalDebt,
    //             'payment_status' => $paymentStatus,
    //             'notes' => $request->payment_notes ?? null,
    //         ]);
    //     }

    //     // **5. Buat Journal Entry untuk Purchase**
    //     $journalEntry = JournalEntry::create([
    //         'entry_date' => $request->purchase_date,
    //         'description' => 'Purchase Payment for Invoice ' . $invoice->invoice_number,
    //     ]);

    //     // **6. Journal Entries**
    //     $inventoryAccount = ChartOfAccount::where('name', 'Persediaan Barang Medis')->value('id');; // COA ID untuk Persediaan Barang Medis
    //     $accountsPayable = ChartOfAccount::where('name', 'Utang Usaha')->value('id');;  // COA ID untuk Hutang Usaha

    //     // **6.1. Debit: Persediaan Barang**
    //     JournalDetail::create([
    //         'journal_entry_id' => $journalEntry->id,
    //         'coa_id' => $inventoryAccount, // Persediaan Barang Medis
    //         'debit' => $request->total_amount,
    //         'credit' => 0
    //     ]);

    //     // **6.2. Jika ada pembayaran, Kredit: Kas/Bank**
    //     if ($purchaseAmount > 0) {
    //         JournalDetail::create([
    //             'journal_entry_id' => $journalEntry->id,
    //             'coa_id' => $request->coa_id, // Kas atau Bank yang dipilih user
    //             'debit' => 0,
    //             'credit' => $purchaseAmount
    //         ]);
    //     }

    //     // **6.3. Jika ada hutang, Kredit: Hutang Usaha**
    //     if ($totalDebt > 0) {
    //         JournalDetail::create([
    //             'journal_entry_id' => $journalEntry->id,
    //             'coa_id' => $accountsPayable, // Hutang Usaha
    //             'debit' => 0,
    //             'credit' => $totalDebt
    //         ]);
    //     }


    //     // Tentukan pesan sukses berdasarkan asal request
    //     $successMessage = $purchaseRequest
    //         ? 'Purchase Invoice created from request!'
    //         : 'Purchase Invoice created successfully.';

    //     return redirect()->route('dashboard.purchases.index')->with('success',$successMessage);
    // }

    private function saveInvoice(Request $request, ?PurchaseRequest $purchaseRequest = null)
{
    $request->validate([
        'supplier_id'        => 'required|exists:suppliers,id',
        'purchase_date'      => 'required|date',
        'total_amount'       => 'required|numeric',
        'discount'           => 'nullable|numeric',
        'ongkos_kirim'       => 'nullable|numeric',
        'payment_amount'     => 'required|numeric|min:0',
        'coa_id'             => 'required|exists:chart_of_accounts,id',
        'dental_material_id' => 'required|array',
        'quantity'           => 'required|array',
        'total_price'        => 'required|array',
        'grand_total'        => 'required|numeric',
    ]);

    // dd($request->discount);

    // Generate invoice number
    $lastInvoice = PurchaseInvoice::orderByDesc('id')->first();
    $nextNumber = $lastInvoice ? ((int) substr($lastInvoice->invoice_number, 4)) + 1 : 1;
    $invoiceNumber = 'INV-' . $nextNumber;

    // Buat invoice
    $invoice = PurchaseInvoice::create([
        'invoice_number'       => $invoiceNumber,
        'supplier_id'          => $request->supplier_id,
        'purchase_date'        => $request->purchase_date,
        'discount'             => $request->discount,
        'ongkos_kirim'         => $request->ongkos_kirim ?? 0,
        'total_amount'         => $request->total_amount,
        'grand_total'          => $request->grand_total,
        'purchase_request_id'  => $purchaseRequest?->id,
    ]);

    // Simpan detail pembelian
    foreach ($request->dental_material_id as $index => $material_id) {
        $material = DentalMaterial::findOrFail($material_id);
        $quantity = $request->quantity[$index];
        $totalPrice = $request->total_price[$index];
        $unitPrice = ($quantity > 0) ? $totalPrice / $quantity : 0;

        PurchaseDetail::create([
            'purchase_invoice_id' => $invoice->id,
            'dental_material_id'  => $material->id,
            'quantity'            => $quantity,
            'unit_price'          => $unitPrice,
            'subtotal'            => $totalPrice,
        ]);
    }

    // Simpan pembayaran
    $purchaseAmount = $request->payment_amount;
    $grandTotal = $request->grand_total;
    $totalDebt = $grandTotal - $purchaseAmount;
    $paymentStatus = ($totalDebt > 0) ? 'partial' : 'paid';

    if ($purchaseAmount > 0) {
        PurchasePayment::create([
            'purchase_invoice_id' => $invoice->id,
            'coa_id'              => $request->coa_id,
            'purchase_amount'     => $purchaseAmount,
            'total_debt'          => $totalDebt,
            'payment_status'      => $paymentStatus,
            'notes'               => $request->payment_notes ?? null,
        ]);
    }

    // Buat entri jurnal
    $journalEntry = JournalEntry::create([
        'entry_date'  => $request->purchase_date,
        'description' => 'Purchase Payment for Invoice ' . $invoice->invoice_number,
    ]);

    $inventoryAccount = ChartOfAccount::where('name', 'Persediaan Barang Medis')->value('id');
    $accountsPayable  = ChartOfAccount::where('name', 'Utang Usaha')->value('id');

    // Debit: Persediaan
    JournalDetail::create([
        'journal_entry_id' => $journalEntry->id,
        'coa_id'           => $inventoryAccount,
        'debit'            => $request->total_amount,
        'credit'           => 0,
    ]);

    // Credit: Kas (jika ada pembayaran)
    if ($purchaseAmount > 0) {
        JournalDetail::create([
            'journal_entry_id' => $journalEntry->id,
            'coa_id'           => $request->coa_id,
            'debit'            => 0,
            'credit'           => $purchaseAmount,
        ]);
    }

    // Credit: Utang Usaha (jika masih ada sisa pembayaran)
    if ($totalDebt > 0) {
        JournalDetail::create([
            'journal_entry_id' => $journalEntry->id,
            'coa_id'           => $accountsPayable,
            'debit'            => 0,
            'credit'           => $totalDebt,
        ]);
    }

    return $invoice;
}

    public function store(Request $request)
    {
        $this->saveInvoice($request);
        return redirect()->route('dashboard.purchases.index')->with('success', 'Purchase Invoice created successfully.');
    }

    public function storeFromRequest(Request $request, PurchaseRequest $purchaseRequest)
    {
        $this->saveInvoice($request, $purchaseRequest);
        return redirect()->route('dashboard.purchases.index')->with('success', 'Purchase Invoice created from request!');
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

    public function updateStockCard($dentalMaterialId, $quantity, $price, $referenceNumber, $isOut = false)
    {
        // **Ambil stok terakhir sebelum update**
        $latestStock = StockCard::where('dental_material_id', $dentalMaterialId)
            ->latest('created_at')
            ->first();

        $oldStock = $latestStock ? $latestStock->remaining_stock : 0;
        $oldAveragePrice = $latestStock ? $latestStock->average_price : 0;

        if ($isOut) {
            // **Barang Keluar: Pakai Harga Rata-rata Terakhir**
            StockCard::create([
                'dental_material_id' => $dentalMaterialId,
                'date' => now(),
                'reference_number' => $referenceNumber,
                'price_out' => $oldAveragePrice, // Pakai harga rata-rata terbaru
                'quantity_out' => $quantity,
                'remaining_stock' => max(0, $oldStock - $quantity), // Kurangi stok
                'average_price' => $oldAveragePrice, // Harga tetap
            ]);
        } else {
            // **Barang Masuk: Hitung Harga Rata-rata Baru**
            $newStock = $oldStock + $quantity;
            $newAveragePrice = ($oldStock * $oldAveragePrice + $quantity * $price) / $newStock;

            // Simpan ke Stock Card dengan harga rata-rata yang diperbarui
            StockCard::create([
                'dental_material_id' => $dentalMaterialId,
                'date' => now(),
                'reference_number' => $referenceNumber,
                'price_in' => $price,
                'quantity_in' => $quantity,
                'remaining_stock' => $newStock,
                'average_price' => $newAveragePrice, // Harga rata-rata baru
            ]);
        }
    }

    public function storeReceived(Request $request, PurchaseInvoice $purchase)
    {
        foreach ($request->received_quantity as $materialId => $quantityReceived) {
            if ($quantityReceived > 0) {
                // **Ambil semua Purchase Detail untuk Material ini**
                $details = $purchase->details()->where('dental_material_id', $materialId)->get();

                $totalQuantity = 0;
                $totalCost = 0;

                foreach ($details as $detail) {
                    $unitPrice = $detail->unit_price; // Harga per unit dari Purchase Detail

                    // **Hitung total kuantitas & total biaya**
                    $totalQuantity += $quantityReceived;
                    $totalCost += $quantityReceived * $unitPrice;
                }

                // **Hitung harga rata-rata saat barang masuk**
                $this->updateStockCard($materialId, $totalQuantity, $totalCost / $totalQuantity, $purchase->invoice_number, false);
            }
        }

        // **Update status purchase menjadi "received"**
        $purchase->update(['status' => 'received']);

        return redirect()->route('dashboard.purchases.index')->with('success', 'Stock successfully updated!');
    }
    public function receive(PurchaseInvoice $purchase)
    {
        return view('dashboard.purchases.receive', compact('purchase'));
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

        $idUtangUsaha = ChartOfAccount::where('name', 'Utang Usaha')->value('id');

        // Debit: Utang Usaha
        JournalDetail::create([
            'journal_entry_id' => $journalEntry->id,
            'coa_id' => $idUtangUsaha, // Utang Usaha
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
    }

    public function createFromRequest(PurchaseRequest $purchaseRequest)
    {
        return $this->create($purchaseRequest);
    }

    // public function createFromRequest(PurchaseRequest $purchaseRequest)
    // {
    //     $suppliers = Supplier::all();
    //     $materials = DentalMaterial::all();
    //     $cashAccounts = ChartOfAccount::all();

    //     return view('dashboard.purchases.create', compact(
    //         'purchaseRequest',
    //         'suppliers',
    //         'materials',
    //         'cashAccounts'
    //     ));
    // }
}
