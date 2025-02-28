<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Category;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use App\Models\JournalDetail;
use App\Models\ChartOfAccount;
use App\Models\DentalMaterial;

class ExpenseController extends Controller
{
    public function index()
    {
        $title = "Expense Management";
        $expenses = Expense::with(['category', 'purchases'])->get();
        $coa = ChartOfAccount::whereIn('type', ['asset'])->get(); // Hanya akun kas dan bank

        return view('dashboard.expenses.index', compact('expenses', 'title', 'coa'));
    }


    public function create(Request $request)
    {
        $categories = Category::all();
        $suppliers = Supplier::all();
        $dentalMaterials = null;
        $coa = ChartOfAccount::all();

        if ($request->has('category_id')) {
            $category = Category::find($request->category_id);
            if ($category && $category->name === 'Bahan Baku') {
                $dentalMaterials = DentalMaterial::all();
            }
        }

        return view('dashboard.expenses.create', compact('categories', 'dentalMaterials', 'suppliers', 'coa'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'description' => 'nullable|string',
            'expired_at' => 'nullable|date',
            'dental_material_id' => 'nullable|exists:dental_materials,id',
            'quantity' => 'nullable|integer|min:1',
            'purchase' => 'nullable|numeric|min:0',
            'coa_id' => 'nullable|exists:chart_of_accounts,id'
        ]);

        // dd($request->all());

        // Simpan Expense
        $expense = Expense::create([
            'date' => $request->date,
            'amount' => $request->amount,
            'category_id' => $request->category_id,
            'supplier_id' => $request->supplier_id,
            'description' => $request->description,
            'expired_at' => $request->expired_at,
            'created_by' => auth()->id(),
            'dental_material_id' => $request->dental_material_id,
            'quantity' => $request->quantity,
        ]);

        // Jika kategori adalah Bahan Baku, Update Stok
        if ($request->category_id && $request->dental_material_id) {
            $category = Category::find($request->category_id);
            if ($category && $category->name === 'Bahan Baku') {
                $dentalMaterial = DentalMaterial::findOrFail($request->dental_material_id);
                $dentalMaterial->stock_quantity += $request->quantity;
                $dentalMaterial->save();
            }
        }

        // Proses Purchase
        $purchaseAmount = $request->purchase;
        $totalDebt = $request->amount - $purchaseAmount;
        $paymentStatus = 'unpaid';

        if ($purchaseAmount == $request->amount) {
            $paymentStatus = 'paid';
        } elseif ($purchaseAmount > 0 && $purchaseAmount < $request->amount) {
            $paymentStatus = 'partial';
        }

        // Simpan Purchase
        Purchase::create([
            'expense_id' => $expense->id,
            'coa_id' => $request->coa_id,
            'purchase_amount' => $purchaseAmount,
            'total_debt' => $totalDebt,
            'payment_status' => $paymentStatus,
            'notes' => $request->notes,
        ]);

        // Update Status Expense
        // $expense->status = $paymentStatus;
        // $expense->save();

        // ** Jurnal Otomatis untuk Purchase **

        // Tentukan Akun Persediaan
        $inventoryAccount = 12; // Default: Persediaan Barang
        if ($request->category_id && $request->dental_material_id) {
            $category = Category::find($request->category_id);
            if ($category && $category->name === 'Bahan Baku') {
                $inventoryAccount = 13; // Persediaan Barang Medis
            }
        }

        // Buat Journal Entry
        $journalEntry = JournalEntry::create([
            'entry_date' => $request->date,
            'description' => 'Purchase for Expense ID: ' . $expense->id,
        ]);

        // ** Jika Purchase Dibayar Langsung **
        if ($purchaseAmount > 0) {
            // Debit: Persediaan Barang
            JournalDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'coa_id' => $inventoryAccount,
                'debit' => $request->amount,
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

        return redirect()->route('dashboard.expenses.index')->with('success', 'Expense recorded successfully!');
    }

    public function edit($id)
    {
        $expense = Expense::findOrFail($id);
        $suppliers = Supplier::all();
        $dentalMaterials = DentalMaterial::all(); // Material Dental untuk Pilihan

        return view('dashboard.expenses.edit', compact('expense', 'suppliers', 'dentalMaterials'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'description' => 'nullable|string',
            'expired_at' => 'nullable|date',
            'dental_material_id' => 'nullable|exists:dental_materials,id',
        ]);

        $expense = Expense::findOrFail($id);

        // Simpan Stok Lama
        $oldMaterialId = $expense->dental_material_id;
        $oldQuantity = $expense->quantity;

        // Update Data Expense
        $expense->update([
            'date' => $request->date,
            'supplier_id' => $request->supplier_id,
            'description' => $request->description,
            'expired_at' => $request->expired_at,
            'dental_material_id' => $request->dental_material_id,
        ]);

        // ** Jika Material Dental Diubah, Update Stok Otomatis **
        if ($request->dental_material_id) {
            if ($oldMaterialId && $oldMaterialId != $request->dental_material_id) {
                // Kurangi Stok dari Material Lama
                $oldMaterial = DentalMaterial::find($oldMaterialId);
                if ($oldMaterial) {
                    $oldMaterial->stock_quantity -= $oldQuantity;
                    $oldMaterial->save();
                }

                // Tambah Stok pada Material Baru
                $newMaterial = DentalMaterial::find($request->dental_material_id);
                $newMaterial->stock_quantity += $expense->quantity;
                $newMaterial->save();
            } elseif ($oldMaterialId == $request->dental_material_id) {
                // Jika Material Sama, Tidak Ada Perubahan Stok
            }
        }

        return redirect()->route('dashboard.expenses.index')->with('success', 'Expense updated successfully!');
    }


    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();

        if ($expense->category && $expense->category->name === 'Bahan Baku' && $expense->dental_material_id) {
            $dentalMaterial = DentalMaterial::find($expense->dental_material_id);
            if ($dentalMaterial) {
                $dentalMaterial->stock_quantity -= $expense->quantity;
                $dentalMaterial->save();
            }
        }


        return redirect()->route('dashboard.expenses.index')->with('success', 'Expense deleted successfully!');
    }

    public function payDebt(Request $request, Purchase $purchase)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $purchase->total_debt,
            'coa_id' => 'required|exists:chart_of_accounts,id',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        // Simpan Purchase Baru sebagai Pembayaran Hutang
        $newPurchase = Purchase::create([
            'expense_id' => $purchase->expense_id,
            'coa_id' => $request->coa_id,
            'purchase_amount' => $request->amount,
            'total_debt' => 0, // Karena ini untuk pembayaran
            'payment_status' => 'paid',
            'notes' => $request->notes,
        ]);

        // Update Total Debt dan Status pada Purchase Lama
        $purchase->total_debt -= $request->amount;
        $purchase->payment_status = ($purchase->total_debt > 0) ? 'partial' : 'paid';
        $purchase->save();

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

        return redirect()->back()->with('success', 'Hutang berhasil dibayar!');
    }
}
