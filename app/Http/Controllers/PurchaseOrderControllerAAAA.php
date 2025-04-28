<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\DentalMaterial;
use App\Models\PurchaseRequest;
use App\Http\Controllers\Controller;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        dd('hai');
        $orders = PurchaseOrder::with('supplier', 'purchaseRequest')->latest()->get();
        return view('dashboard.purchase_orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::all();
        $requests = PurchaseRequest::all();
        $materials = DentalMaterial::all();
        return view('dashboard.purchase_orders.create', compact('suppliers', 'requests', 'materials'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_number' => 'required|unique:purchase_orders',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'purchase_request_id' => 'nullable|exists:purchase_requests,id',
            'order_date' => 'required|date',
            'notes' => 'nullable|string',
            'status' => 'required|in:draft,sent,completed,cancelled',
            'details' => 'array|required',
            'details.*.material_id' => 'required|exists:dental_materials,id',
            'details.*.quantity' => 'required|integer|min:1',
            'details.*.price' => 'required|numeric|min:0',
            'details.*.unit' => 'required|string',
            'details.*.notes' => 'nullable|string',
        ]);

        $order = PurchaseOrder::create($validated);

        foreach ($validated['details'] as $detail) {
            $order->details()->create([
                'material_id' => $detail['material_id'],
                'quantity' => $detail['quantity'],
                'price' => $detail['price'],
                'unit' => $detail['unit'],
                'notes' => $detail['notes'] ?? null,
            ]);
        }

        return redirect()->route('dashboard.purchase_orders.index')->with('success', 'Purchase Order created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('details.material', 'supplier', 'purchaseRequest');
        return view('dashboard.purchase_orders.show', compact('purchaseOrder'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('details');
        $suppliers = Supplier::all();
        $requests = PurchaseRequest::all();
        $materials = DentalMaterial::all();

        return view('dashboard.purchase_orders.edit', compact('purchaseOrder', 'suppliers', 'requests', 'materials'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validate([
            'order_number' => 'required|unique:purchase_orders,order_number,' . $purchaseOrder->id,
            'supplier_id' => 'nullable|exists:suppliers,id',
            'purchase_request_id' => 'nullable|exists:purchase_requests,id',
            'order_date' => 'required|date',
            'notes' => 'nullable|string',
            'status' => 'required|in:draft,sent,completed,cancelled',
            'details' => 'array|required',
            'details.*.material_id' => 'required|exists:dental_materials,id',
            'details.*.quantity' => 'required|integer|min:1',
            'details.*.price' => 'required|numeric|min:0',
            'details.*.unit' => 'required|string',
            'details.*.notes' => 'nullable|string',
        ]);

        $purchaseOrder->update($validated);

        $purchaseOrder->details()->delete();

        foreach ($validated['details'] as $detail) {
            $purchaseOrder->details()->create([
                'material_id' => $detail['material_id'],
                'quantity' => $detail['quantity'],
                'price' => $detail['price'],
                'unit' => $detail['unit'],
                'notes' => $detail['notes'] ?? null,
            ]);
        }

        return redirect()->route('dashboard.purchase_orders.index')->with('success', 'Purchase Order updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->delete();
        return back()->with('success', 'Purchase Order deleted.');
    }
}
