<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Support\Str;
use GuzzleHttp\Psr7\Message;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\DentalMaterial;
use App\Models\PurchaseRequest;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseOrderDetail;
use App\Http\Controllers\Controller;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // dd('index purchase order');
        $orders = PurchaseOrder::with('supplier', 'purchaseRequest')->latest()->get();
        return view('dashboard.purchase_orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $suppliers = Supplier::all();
        $materials = DentalMaterial::all();

        if ($request->has('request_id')) {
            $purchaseRequest = PurchaseRequest::with('details.material')->findOrFail($request->request_id);
            return view('dashboard.purchase_orders.create', compact('suppliers', 'materials', 'purchaseRequest'));
        }

        $requests = PurchaseRequest::where('status', 'approved')->get();
        return view('dashboard.purchase_orders.create', compact('suppliers', 'materials', 'requests'));
    }

    public function store(Request $request)
    {
        $filteredMaterials = [];
        if ($request->has('selected_materials')) {
            foreach ($request->selected_materials as $material) {
                if (isset($material['include']) && $material['include'] == 1) {
                    $filteredMaterials[] = $material;
                }
            }
            $request->merge(['selected_materials' => $filteredMaterials]);
        }

        // dd($request->all());

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'due_date' => 'nullable|date',
            'ship_date' => 'nullable|date',
            'shipping_address' => 'nullable|string',
            'payment_requirement' => 'nullable',
            'discount' => 'nullable|numeric',
            'ongkos_kirim' => 'nullable|numeric',
            'harga_total'       => 'nullable|numeric',
            'notes' => 'nullable|string',
            'purchase_request_id' => 'nullable|exists:purchase_requests,id',
            'selected_materials' => 'sometimes|array',
            'selected_materials.*.material_id' => 'required|exists:dental_materials,id',
            'selected_materials.*.quantity' => 'required|numeric|min:1',
            'selected_materials.*.price' => 'required|numeric|min:0',
            'selected_materials.*.notes' => 'nullable|string'
        ]);

        // dd($validated['harga_total']);
        // Generate nomor unik
        $datePrefix = 'PO-' . now()->format('Ymd');

        // Hitung jumlah PO yang sudah dibuat hari ini
        $latestPoToday = \App\Models\PurchaseOrder::where('order_number', 'like', $datePrefix . '-%')
            ->orderByDesc('order_number')
            ->first();

        if ($latestPoToday) {
            // Ambil angka urutan terakhir, lalu tambahkan 1
            $lastNumber = (int) str_replace($datePrefix . '-', '', $latestPoToday->order_number);
            $nextNumber = $lastNumber + 1;
        } else {
            // Belum ada, mulai dari 1
            $nextNumber = 1;
        }

        // Buat nomor baru
        $orderNumber = $datePrefix . '-' . $nextNumber;


        DB::beginTransaction();
        try {

            if ($request->hasFile('attachment')) {
                $path = $request->file('attachment')->store('attachments/purchase_orders', 'public');
                // $validated['attachment_path'] = $path;
            }
            // Create Purchase Order
            $purchaseOrder = PurchaseOrder::create([
                'order_number' => $orderNumber,
                'supplier_id' => $validated['supplier_id'],
                'order_date' => $validated['order_date'],
                'due_date' => $validated['due_date'],
                'ship_date' => $validated['ship_date'],
                'shipping_address' => $validated['shipping_address'],
                'payment_requirement' => $validated['payment_requirement'],
                'discount' => $validated['discount'],
                'ongkos_kirim' => $validated['ongkos_kirim'],
                'harga_total' => $validated['harga_total'],
                'notes' => $validated['notes'],
                'purchase_request_id' => $validated['purchase_request_id'] ?? null,
                'attachment' => $path ?? null,
            ]);

            // dd('udh create po');

            // Add Materials
            // Tambahkan detail material
            foreach ($validated['selected_materials'] ?? [] as $material) {
                // dd('masuk loop');
                // dd($material);
                $purchaseOrder->details()->create([
                    'material_id' => $material['material_id'],
                    'quantity' => $material['quantity'],
                    'price' => $material['price'],
                    'notes' => $material['notes'] ?? null
                ]);
            }
            // dd('udh create pod');
            DB::commit();

            return redirect()->route('dashboard.purchase_orders.show', $purchaseOrder)
                ->with('success', 'Purchase Order created successfully!');
        } catch (\Exception $e) {
            dd($e);
            // DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create Purchase Order: ' . $e->getMessage());
        }
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
