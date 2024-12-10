<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\DentalMaterial;

class ExpenseController extends Controller
{
    public function index()
{
    $title = "Expense Management"; // Atur judul sesuai kebutuhan
    $expenses = Expense::with('category', 'admin')->get();
    return view('dashboard.expenses.index', compact('expenses', 'title'));
}


public function create()
{
    $categories = Category::all(); // Ambil semua kategori
    $dentalMaterials = DentalMaterial::all(); // Ambil semua bahan dental
    return view('dashboard.expenses.create', compact('categories', 'dentalMaterials'));
}


    public function store(Request $request)
{
    $request->validate([
        'date' => 'required|date',
        'amount' => 'required|numeric',
        'category_id' => 'required|exists:categories,id',
        'description' => 'nullable|string',
        'dental_material_id' => 'nullable|exists:dental_materials,id',
        'quantity' => 'nullable|integer|min:1',
    ]);

    $expense = Expense::create([
        'date' => $request->date,
        'amount' => $request->amount,
        'category_id' => $request->category_id,
        'description' => $request->description,
        'created_by' => auth()->id(),
        'dental_material_id' => $request->dental_material_id,
        'quantity' => $request->quantity,
    ]);

    // Perbarui stok bahan dental jika ini adalah bahan baku
    if ($expense->category->name === 'Bahan Baku' && $request->dental_material_id) {
        $dentalMaterial = DentalMaterial::findOrFail($request->dental_material_id);
        $dentalMaterial->stock_quantity += $request->quantity;
        $dentalMaterial->save();
    }

    return redirect()->route('dashboard.expenses.index')->with('success', 'Expense recorded successfully!');
}


    public function edit($id)
{
    $expense = Expense::findOrFail($id); // Temukan pengeluaran berdasarkan ID
    $categories = Category::all(); // Ambil semua kategori untuk dropdown
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

    return redirect()->route('dashboard.expenses.index')->with('success', 'Expense deleted successfully!');
}

}
