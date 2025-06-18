<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\StockCard;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use App\Models\JournalDetail;
use App\Models\PurchaseOrder;
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

    public function create(PurchaseOrder $purchaseOrder = null)
    {
        $data = [
            'suppliers' => Supplier::all(),
            'materials' => DentalMaterial::all(),
            'cashAccounts' => ChartOfAccount::all(),
            'purchaseOrder' => $purchaseOrder,
        ];

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

    public function createFromOrder(PurchaseOrder $purchaseOrder)
    {
        // Pastikan order belum memiliki invoice
        if ($purchaseOrder->purchaseInvoice) {
            return redirect()->back()->with('error', 'Invoice sudah dibuat untuk purchase order ini');
        }

        $suppliers = Supplier::all();
        $materials = DentalMaterial::all();
        $cashAccounts = ChartOfAccount::all();

        // dd($purchaseOrder);

        return view('dashboard.purchases.create-from-order', [
            'purchaseOrder' => $purchaseOrder,
            'suppliers' => $suppliers,
            'materials' => $materials,
            'cashAccounts' => $cashAccounts
        ]);
    }


    public function storeFromOrder(Request $request, PurchaseOrder $purchaseOrder)
    {
        try {
            $this->saveInvoice($request, $purchaseOrder);
            return redirect()->route('dashboard.purchases.index')->with('success', 'Purchase Invoice created from order!');
        } catch (\Exception $e) {
            // dd($e);
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    private function saveInvoice(Request $request, ?PurchaseOrder $purchaseOrder = null)
{
    $request->validate([
        'purchase_order_id'  => 'nullable|exists:purchase_orders,id',
        'invoice_date' => 'required|date',
        'received_date' => 'nullable|date',
        'supplier_id'         => 'required|exists:suppliers,id',
        'payment_requirement' => 'nullable|string',
        'due_date' => 'nullable|date',
        'discount' => 'nullable',
        'ongkos_kirim' => 'nullable',
        'total_amount' => 'required',
        'grand_total' => 'required',
        'dental_material_id' => 'required|array',
    ]);

    return DB::transaction(function () use ($request) {

        // Ambil nilai diskon dan ongkir untuk perhitungan
        $discount =  $request->discount ?? 0;
        $ongkos_kirim = $request->ongkos_kirim ?? 0;
        
        // Ambil subtotal dari request. Pastikan frontend mengirim nilai numerik yang bersih.
        // total_amount adalah subtotal sebelum diskon dan ongkir.
        $subtotal =  $request->total_amount;

        // Jika subtotal 0 atau kurang, hentikan proses untuk menghindari pembagian dengan nol.
        if ($subtotal <= 0) {
            throw new \Exception("Subtotal tidak boleh nol.");
        }

        // Generate invoice number
        $lastInvoice = PurchaseInvoice::orderByDesc('id')->first();
        $nextNumber = $lastInvoice ? ((int) substr($lastInvoice->invoice_number, 4)) + 1 : 1;
        $invoiceNumber = 'INV-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // Buat invoice (kode ini sebagian besar tetap sama)
        $invoice = PurchaseInvoice::create([
            'invoice_number'      => $invoiceNumber,
            'invoice_date'        => $request->invoice_date,
            'purchase_order_id'   => $request->purchase_order_id,
            'supplier_id'         => $request->supplier_id,
            'payment_requirement' => $request->payment_requirement,
            'receive_date'        => $request->receive_date,
            'due_date'            => $request->due_date,
            'discount'            => $discount,
            'ongkos_kirim'        => $ongkos_kirim,
            'grand_total'         => $request->grand_total, // grand_total dari request
        ]);

        // Simpan detail pembelian dengan LOGIKA BARU
        foreach ($request->dental_material_id as $index => $material_id) {
            $quantity =$request->quantity[$index];
            $totalPrice =  $request->total_price[$index];
            $unitPrice = ($quantity > 0) ? $totalPrice / $quantity : 0;

            // --- Logika Alokasi Biaya Dimasukkan di Sini ---
            $valueWeight = $totalPrice / $subtotal;
            $allocatedShipping = $ongkos_kirim * $valueWeight;
            $allocatedDiscount = $discount * $valueWeight;
            $finalLineCost = $totalPrice + $allocatedShipping - $allocatedDiscount;
            $finalUnitPrice = ($quantity > 0) ? $finalLineCost / $quantity : 0;
            // --- Akhir Logika Alokasi ---

            PurchaseDetail::create([
                'purchase_invoice_id' => $invoice->id,
                'dental_material_id'  => $material_id, // Anda sudah melakukan findOrFail sebelumnya, tapi ini lebih ringkas
                'quantity'            => $quantity,
                'unit_price'          => $unitPrice,       // Harga asli disimpan
                'final_unit_price'    => $finalUnitPrice,  // << HARGA FINAL BARU DISIMPAN
                'subtotal'            => $totalPrice,
            ]);
        }

        // Buat entri jurnal dengan LOGIKA BARU
        // (Saya menggabungkan 2 pembuatan JournalEntry Anda menjadi 1 untuk efisiensi)
        $journalEntry = JournalEntry::create([
            'entry_date'  => $request->invoice_date,
            'description' => 'Pembelian dari ' . $invoice->supplier->name . ' - Invoice #' . $invoice->invoice_number,
            'purchase_id' => $invoice->id, // Menggunakan kolom yang sudah ada
        ]);

        $inventoryAccount = ChartOfAccount::where('name', 'Persediaan Barang Medis')->firstOrFail();
        $accountsPayable  = ChartOfAccount::where('name', 'Utang Usaha')->firstOrFail();

        // Debit: Persediaan senilai grand_total
        JournalDetail::create([
            'journal_entry_id' => $journalEntry->id,
            'coa_id'           => $inventoryAccount->id,
            'debit'            => $request->grand_total,
            'credit'           => 0,
        ]);

        // Kredit: Utang Usaha senilai grand_total
        JournalDetail::create([
            'journal_entry_id' => $journalEntry->id,
            'coa_id'           => $accountsPayable->id,
            'debit'            => 0,
            'credit'           => $request->grand_total,
        ]);
        

        return $invoice;
    });

}


    private function saveInvoiceXXXXX(Request $request, ?PurchaseOrder $purchaseOrder = null)
    {
        // dd('masuk function save invoice');
        try{
        $request->validate([
            'purchase_order_id'  => 'nullable|exists:purchase_orders,id',
            'invoice_date' => 'required|date',
            'received_date' => 'nullable|date',
            // 'purchase_date'      => 'required|date',
            'supplier_id'        => 'required|exists:suppliers,id',
            'payment_requirement' => 'nullable|string',
            'due_date' => 'nullable|date',
            'discount' => 'nullable',
            'ongkos_kirim' => 'nullable',
            'total_amount' => 'required',
            'grand_total' => 'required',
            'dental_material_id' => 'required|array',
        ]);
    }catch (\Exception $e){
        dd($e);
    }

        dd($request);

        // Generate invoice number
        $lastInvoice = PurchaseInvoice::orderByDesc('id')->first();
        $nextNumber = $lastInvoice ? ((int) substr($lastInvoice->invoice_number, 4)) + 1 : 1;
        $invoiceNumber = 'INV-' . $nextNumber;

        // Buat invoice
        $invoice = PurchaseInvoice::create([
            'invoice_number'       => $invoiceNumber,
            'invoice_date'         => $request->invoice_date,
            'purchase_order_id' => $request->purchase_order_id,
            'supplier_id'          => $request->supplier_id,
            'payment_requirement' => $request->payment_requirement,
            'receive_date' => $request->receive_date,
            'due_date' => $request->due_date,
            // 'purchase_date'        => $request->purchase_date,
            'discount'             => $request->discount,
            'ongkos_kirim'         => $request->ongkos_kirim,
            'grand_total'          => $request->grand_total,
        ]);
        // dd('dah buat invoice');

        // Simpan detail pembelian
        foreach ($request->dental_material_id as $index => $material_id) {
            $material = DentalMaterial::findOrFail($material_id);
            $quantity = $request->quantity[$index];
            // maksa
            $totalPrice = $request->grand_total;
            $unitPrice = ($quantity > 0) ? $totalPrice / $quantity : 0;

            dd($request->total_price[$index]);

            PurchaseDetail::create([
                'purchase_invoice_id' => $invoice->id,
                'dental_material_id'  => $material->id,
                'quantity'            => $quantity,
                'unit_price'          => $unitPrice,
                'subtotal'            => $totalPrice,
            ]);
        }
        // dd('dah detail invoice');
        // dd($invoice->id);

        // Buat jurnal entry
        JournalEntry::create([
            'purchase_id' => $invoice->id,
            'entry_date' => $request->invoice_date,
            'description' => 'Pembelian dari ' . $invoice->supplier->name . ' - Invoice #' . $invoice->invoice_number,
        ]);

        // dd('buat jurnal');

        // Buat entri jurnal
        $journalEntry = JournalEntry::create([
            'entry_date'  => $request->invoice_date,
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

        // Jika ada diskon, catat sebagai kredit ke akun diskon
        if ($request->discount > 0) {
            $purchaseDiscountAccount = ChartOfAccount::where('name', 'Diskon Pembelian')->value('id');

            JournalDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'coa_id'           => $purchaseDiscountAccount,
                'debit'            => 0,
                'credit'           => $request->discount,
            ]);
        }

        // Jika ada ongkos kirim, catat sebagai debit ke akun ongkos kirim
        if ($request->ongkos_kirim > 0) {
            $shippingCostAccount = ChartOfAccount::where('name', 'Beban Pengiriman Pembelian')->value('id');

            JournalDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'coa_id'           => $shippingCostAccount,
                'debit'            => $request->ongkos_kirim,
                'credit'           => 0,
            ]);
        }

        // Kredit: Utang Usaha (total bersih)
        JournalDetail::create([
            'journal_entry_id' => $journalEntry->id,
            'coa_id'           => $accountsPayable,
            'debit'            => 0,
            'credit'           => $request->grand_total,
        ]);

        // Credit: Kas (jika ada pembayaran)
        // if ($purchaseAmount > 0) {
        //     JournalDetail::create([
        //         'journal_entry_id' => $journalEntry->id,
        //         'coa_id'           => $request->coa_id,
        //         'debit'            => 0,
        //         'credit'           => $purchaseAmount,
        //     ]);
        // }

        // Credit: Utang Usaha (jika masih ada sisa pembayaran)
        // if ($totalDebt > 0) {
        //     JournalDetail::create([
        //         'journal_entry_id' => $journalEntry->id,
        //         'coa_id'           => $accountsPayable,
        //         'debit'            => 0,
        //         'credit'           => $totalDebt,
        //     ]);
        // }

        return $invoice;
    }

    public function store(Request $request)
    {
        try {
            $this->saveInvoice($request);
            return redirect()->route('dashboard.purchases.index')->with('success', 'Purchase Invoice created successfully.');
        } catch (\Exception $e) {
            // dd($e);
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    // public function storeFromRequest(Request $request, PurchaseRequest $purchaseRequest)
    // {
    //     $this->saveInvoice($request, $purchaseRequest);
    //     return redirect()->route('dashboard.purchases.index')->with('success', 'Purchase Invoice created from request!');
    // }



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
                'type' => 'usage'
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
                'type' => 'purchase'
            ]);
        }
    }

    public function storeReceived(Request $request, PurchaseInvoice $purchase)
    {
        // dd('hi'); 
        foreach ($request->received_quantity as $materialId => $quantityReceived) {
            if ($quantityReceived > 0) {
                // **Ambil semua Purchase Detail untuk Material ini**
                $details = $purchase->details()->where('dental_material_id', $materialId)->get();

                $totalQuantity = 0;
                $totalCost = 0;

                foreach ($details as $detail) {
                    $unitPrice = $detail->final_unit_price; // Harga per unit dari Purchase Detail

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
        // dd('masuk paydebt');
        $request->validate([
            'purchase_id' => 'required|exists:purchase_invoices,id',
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'coa_id' => 'required|exists:chart_of_accounts,id',
            'payment_method' => 'required|string',
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
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method
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

    // public function createFromRequest(PurchaseRequest $purchaseRequest)
    // {
    //     return $this->create($purchaseRequest);
    // }

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


    public function show($id)
    {
        // Ambil invoice berdasarkan ID dengan eager loading relasi
        $purchaseInvoice = PurchaseInvoice::with([
            'supplier',
            'purchaseOrder',
            'details.material',
            'payments.coa'
        ])->findOrFail($id);

        // Hitung total yang sudah dibayar
        $totalPaid = $purchaseInvoice->payments->sum('purchase_amount');

        // Hitung sisa tagihan
        $sisaTagihan = $purchaseInvoice->grand_total - $totalPaid;

        // Kirim data ke view
        return view('dashboard.purchases.show', [
            'title' => 'Detail Purchase Invoice',
            'purchaseInvoice' => $purchaseInvoice,
            'totalPaid' => $totalPaid,
            'sisaTagihan' => $sisaTagihan,
        ]);
    }
}
