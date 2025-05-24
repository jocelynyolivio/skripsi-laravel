<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\DentalMaterial;
use App\Models\PurchaseRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\PurchaseRequestDetail;

class PurchaseRequestController extends Controller
{
    public function index()
    {
        $requests = PurchaseRequest::with('requester')->latest()->get();
        return view('dashboard.purchase_requests.index', compact('requests'));
    }

    public function create()
    {
        $materials = DentalMaterial::all();
        return view('dashboard.purchase_requests.create', compact('materials'));
    }

    public function store(Request $request)
    {
        try {
            $updated_by = auth()->id();
            $request->validate([
                'request_date' => 'required|date',
                'materials' => 'required|array|min:1',
                'materials.*.dental_material_id' => 'required|exists:dental_materials,id',
                'materials.*.quantity' => 'required|integer|min:1',
                'materials.*.notes' => 'nullable|string',
            ]);

            // Generate nomor unik
            $requestNumber = 'PR-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));

            // Simpan header
            $purchaseRequest = PurchaseRequest::create([
                'request_number' => $requestNumber,
                'request_date' => $request->request_date,
                'requested_by' => Auth::id(),
                'notes' => $request->notes,
                'updated_by' => $updated_by
            ]);

            // Simpan detail
            foreach ($request->materials as $material) {
                PurchaseRequestDetail::create([
                    'purchase_request_id' => $purchaseRequest->id,
                    'dental_material_id' => $material['dental_material_id'],
                    'quantity' => $material['quantity'],
                    'notes' => $material['notes'] ?? null,
                ]);
            }

            return redirect()->route('dashboard.purchase_requests.index')->with('success', 'Purchase Request created!');
        } catch (\Exception $e) {
            // dd($e);
            return redirect()->back()->with('error', 'Gagal : ' . $e->getMessage());
        }
    }

    public function show(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->load('details.material', 'requester', 'approver', 'editor');
        return view('dashboard.purchase_requests.show', compact('purchaseRequest'));
    }

    public function approve(PurchaseRequest $purchaseRequest)
    {
        // dd(now());
        $purchaseRequest->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // dd($purchaseRequest);

        return redirect()->route('dashboard.purchase_requests.show', $purchaseRequest->id)
            ->with('success', 'Purchase request approved.');
    }

    public function reject(Request $request, PurchaseRequest $purchaseRequest)
    {
        $request->validate([
            'approval_notes' => 'nullable|string',
        ]);

        $purchaseRequest->update([
            'status' => 'rejected',
            'approval_notes' => $request->approval_notes,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('dashboard.purchase_requests.show', $purchaseRequest->id)
            ->with('success', 'Purchase request rejected.');
    }

    public function duplicate(PurchaseRequest $purchaseRequest)
    {
        return view('dashboard.purchase_requests.create', [
            'materials' => DentalMaterial::all(),
            'duplicateFrom' => $purchaseRequest, // misal dari id yang di-duplicate
        ]);
    }

    public function edit(PurchaseRequest $purchaseRequest)
    {
        //
    }

    public function update(Request $request, PurchaseRequest $purchaseRequest)
    {
        //
    }

    public function destroy(PurchaseRequest $purchaseRequest)
    {
        //
    }


}
