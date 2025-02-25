<?php

namespace App\Http\Controllers;

use App\Models\SalaryCalculation;
use Illuminate\Http\Request;

class SalaryCalculationController extends Controller
{
    // Index - Menampilkan semua data salary_calculations
    public function index()
    {
        $salaries = SalaryCalculation::all();
        return view('dashboard.salary_calculations.index', compact('salaries'));
    }

    // Create - Form untuk menambah data baru
    public function create()
    {
        return view('dashboard.salary_calculations.create');
    }

    // Store - Menyimpan data baru ke database
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'month' => 'required|date_format:Y-m',
            'normal_shift' => 'nullable|integer',
            'holiday_shift' => 'nullable|integer',
            'shift_pagi' => 'nullable|integer',
            'shift_siang' => 'nullable|integer',
            'lembur' => 'nullable|integer',
            'base_salary' => 'required|integer',
            'allowance' => 'nullable|integer',
            'grand_total' => 'required|integer',
        ]);

        SalaryCalculation::create($request->all());

        return redirect()->route('salary_calculations.index')
                         ->with('success', 'Data gaji berhasil ditambahkan.');
    }

    // Edit - Form untuk mengedit data
    public function edit($id)
    {
        $salary = SalaryCalculation::findOrFail($id);
        return view('dashboard.salary_calculations.edit', compact('salary'));
    }

    // Update - Mengupdate data di database
    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'month' => 'required|date_format:Y-m',
            'normal_shift' => 'nullable|integer',
            'holiday_shift' => 'nullable|integer',
            'shift_pagi' => 'nullable|integer',
            'shift_siang' => 'nullable|integer',
            'lembur' => 'nullable|integer',
            'base_salary' => 'required|integer',
            'allowance' => 'nullable|integer',
            'grand_total' => 'required|integer',
        ]);

        $salary = SalaryCalculation::findOrFail($id);
        $salary->update($request->all());

        return redirect()->route('salary_calculations.index')
                         ->with('success', 'Data gaji berhasil diperbarui.');
    }

    // Destroy - Menghapus data dari database
    public function destroy($id)
    {
        $salary = SalaryCalculation::findOrFail($id);
        $salary->delete();

        return redirect()->route('salary_calculations.index')
                         ->with('success', 'Data gaji berhasil dihapus.');
    }
}
