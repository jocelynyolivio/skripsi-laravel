<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Category;
use App\Models\DentalMaterial;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $title = "Expense Management";
        $expenses = Expense::with('category')->get();
        return view('dashboard.expenses.index', compact('expenses', 'title'));
    }

    public function create(Request $request)
    {
        $categories = Category::all();
        $dentalMaterials = null;

        if ($request->has('category_id')) {
            $category = Category::find($request->category_id);
            if ($category && $category->name === 'Bahan Baku') {
                $dentalMaterials = DentalMaterial::all();
            }
        }

        return view('dashboard.expenses.create', compact('categories', 'dentalMaterials'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'expired_at' => 'nullable|date', // Tambahkan validasi expired date
            'dental_material_id' => 'nullable|exists:dental_materials,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        // dd($request->all());

        $expense = Expense::create([
            'date' => $request->date,
            'amount' => $request->amount,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'expired_at' => $request->expired_at, // Simpan tanggal expired
            'created_by' => auth()->id(),
            'dental_material_id' => $request->dental_material_id,
            'quantity' => $request->quantity,
        ]);

        if ($request->category_id && $request->dental_material_id) {
            $category = Category::find($request->category_id);
            if ($category && $category->name === 'Bahan Baku') {
                $dentalMaterial = DentalMaterial::findOrFail($request->dental_material_id);
                $dentalMaterial->stock_quantity += $request->quantity;
                $dentalMaterial->save();
            } else {
                return redirect()->back()->withErrors(['error' => 'Dental Material hanya bisa dipilih jika kategori adalah Bahan Baku']);
            }
        }        

        return redirect()->route('dashboard.expenses.index')->with('success', 'Expense recorded successfully!');
    }

    public function edit($id)
    {
        $expense = Expense::findOrFail($id);
        $categories = Category::all();
        return view('dashboard.expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
        ]);

        $expense = Expense::findOrFail($id);
        $expense->update([
            'date' => $request->date,
            'amount' => $request->amount,
            'category_id' => $request->category_id,
            'description' => $request->description,
        ]);

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
}
