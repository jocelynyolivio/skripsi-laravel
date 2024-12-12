<?php

namespace App\Http\Controllers;

use App\Models\ExpenseRequest;
use Illuminate\Http\Request;

class ExpenseRequestController extends Controller
{
    public function index()
    {
        $requests = ExpenseRequest::with('requester', 'approver')->get();
        return view('dashboard.expense_requests.index', compact('requests'));
    }

    public function create()
    {
        return view('dashboard.expense_requests.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'estimated_cost' => 'required|numeric|min:0',
        ]);

        ExpenseRequest::create([
            'item_name' => $request->item_name,
            'description' => $request->description,
            'quantity' => $request->quantity,
            'estimated_cost' => $request->estimated_cost,
            'requested_by' => auth()->id(),
        ]);

        return redirect()->route('dashboard.expense_requests.index')->with('success', 'Expense request created successfully.');
    }

    public function approve($id)
    {
        $request = ExpenseRequest::findOrFail($id);
        $request->update([
            'status' => 'Approved',
            'approved_by' => auth()->id(),
        ]);

        return redirect()->route('dashboard.expense_requests.index')->with('success', 'Expense request approved.');
    }

    public function reject($id)
    {
        $request = ExpenseRequest::findOrFail($id);
        $request->update([
            'status' => 'Rejected',
            'approved_by' => auth()->id(),
        ]);

        return redirect()->route('dashboard.expense_requests.index')->with('success', 'Expense request rejected.');
    }

    public function markDone($id)
    {
        $request = ExpenseRequest::findOrFail($id);
        $request->update([
            'status' => 'Done',
        ]);

        return redirect()->route('dashboard.expense_requests.index')->with('success', 'Expense marked as done.');
    }
}
