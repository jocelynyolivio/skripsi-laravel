@extends('dashboard.layouts.main')

@section('breadcrumbs')
@include('dashboard.layouts.breadcrumbs', [
'customBreadcrumbs' => [
['text' => 'Purchase Orders', 'url' => route('dashboard.purchase_orders.index')],
['text' => 'Purchase Order Details']
]
])
@endsection

@section('container')
<div class="container mt-5 col-md-10">
    <!-- Main Order Card -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-file-purchase-order me-2"></i>
                Purchase Order #{{ $purchaseOrder->order_number }}
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label text-muted">Supplier</label>
                        <p class="form-control-static">{{ $purchaseOrder->supplier->nama ?? '-' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Order Date</label>
                        <p class="form-control-static">{{ \Carbon\Carbon::parse($purchaseOrder->order_date)->format('d F Y') }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <!-- <div class="mb-3">
                        <label class="form-label text-muted">Status</label>
                        <p class="form-control-static">
                            <span class="badge bg-{{ $purchaseOrder->status === 'completed' ? 'success' : ($purchaseOrder->status === 'pending' ? 'warning' : 'secondary') }}">
                                {{ ucfirst($purchaseOrder->status) }}
                            </span>
                        </p>
                    </div> -->
                    <div class="mb-3">
                        <label class="form-label text-muted">Notes</label>
                        <p class="form-control-static">{{ $purchaseOrder->notes ?: '-' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Attachment</label>
                        @if($purchaseOrder->attachment)
                        <p>
                            <a href="{{ asset('storage/' . $purchaseOrder->attachment) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-paperclip me-1"></i> View Attachment
                            </a>
                        </p>
                        @else
                        <p class="form-control-static">-</p>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Order Details Card -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Order Details</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Material</th>
                            <th class="text-end">Quantity</th>
                            <th>Unit</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Subtotal</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchaseOrder->details as $detail)
                        <tr>
                            <td>{{ $detail->material->name }}</td>
                            <td class="text-end">{{ number_format($detail->quantity) }}</td>
                            <td>{{ $detail->material->unit_type }}</td>
                            <td class="text-end">{{ number_format($detail->price / $detail->quantity, 2) }}</td>
                            <td class="text-end">{{ number_format($detail->price, 2) }}</td>
                            <td>{{ $detail->notes ?: '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
    {{-- Kita hitung dulu semua nilainya agar mudah dibaca dan digunakan --}}
    @php
        $subtotal = $purchaseOrder->details->sum('price');
        $discount = $purchaseOrder->discount ?? 0;
        $shipping = $purchaseOrder->ongkos_kirim ?? 0;
        $grandTotal = $subtotal - $discount + $shipping;
    @endphp

    {{-- Baris untuk Subtotal (Total harga barang) --}}
    <tr>
        <td colspan="4" class="text-end">Subtotal</td>
        <td class="text-end">{{ number_format($subtotal, 2) }}</td>
        <td></td>
    </tr>

    {{-- Baris untuk Diskon, hanya tampil jika ada diskon --}}
    @if($discount > 0)
    <tr>
        <td colspan="4" class="text-end">Diskon</td>
        <td class="text-end text-danger">- {{ number_format($discount, 2) }}</td>
        <td></td>
    </tr>
    @endif

    {{-- Baris untuk Ongkos Kirim, hanya tampil jika ada ongkos kirim --}}
    @if($shipping > 0)
    <tr>
        <td colspan="4" class="text-end">Ongkos Kirim</td>
        <td class="text-end">{{ number_format($shipping, 2) }}</td>
        <td></td>
    </tr>
    @endif

    {{-- Baris untuk Grand Total --}}
    <tr class="table-active">
        <td colspan="4" class="text-end fw-bold">Grand Total</td>
        <td class="text-end fw-bold">
            {{ number_format($grandTotal, 2) }}
        </td>
        <td></td>
    </tr>
</tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Action Buttons Card -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <a href="{{ route('dashboard.purchase_orders.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Back to List
                </a>

                @if($purchaseOrder->purchaseInvoices->isEmpty())
                <a href="{{ route('dashboard.purchases.createFromOrder', $purchaseOrder) }}" class="btn btn-primary">
                    <i class="fas fa-file-invoice me-2"></i> Create Invoice
                </a>
                @else
                <button class="btn btn-success" disabled>
                    <i class="fas fa-check-circle me-2"></i> Invoice Already Created
                </button>
                @endif
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const createInvoiceBtn = document.querySelector('.btn-primary');

        if (createInvoiceBtn) {
            createInvoiceBtn.addEventListener('click', function (e) {
                e.preventDefault(); // Cegah redirect langsung

                Swal.fire({
                    title: 'Create Invoice?',
                    text: "Are you sure you want to create an invoice from this Purchase Order?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, create it'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = this.getAttribute('href');
                    }
                });
            });
        }
    });
</script>

@endsection