<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use Illuminate\Http\Request;

class ChartOfAccountController extends Controller
{
    public function index()
    {
        $coa = ChartOfAccount::all();
        return view('dashboard.coa.index', compact('coa'));
    }

    public function create()
    {
        return view('dashboard.coa.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:chart_of_accounts,code',
            'name' => 'required',
            'type' => 'required'
        ]);

        ChartOfAccount::create($request->all());

        return redirect()->route('dashboard.coa.index')->with('success', 'Account created successfully!');
    }

    public function edit(ChartOfAccount $coa)
    {
        return view('dashboard.coa.edit', compact('coa'));
    }

    public function update(Request $request, ChartOfAccount $coa)
    {
        $request->validate([
            'code' => 'required|unique:chart_of_accounts,code,' . $coa->id,
            'name' => 'required',
            'type' => 'required'
        ]);

        $coa->update($request->all());

        return redirect()->route('dashboard.coa.index')->with('success', 'Account updated successfully!');
    }

    public function destroy(ChartOfAccount $coa)
    {
        $coa->delete();
        return redirect()->route('dashboard.coa.index')->with('success', 'Account deleted successfully!');
    }
}
