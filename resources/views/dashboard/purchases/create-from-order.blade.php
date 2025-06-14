@extends('dashboard.layouts.main')

@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['text' => 'Purchase Invoices', 'url' => route('dashboard.purchases.index')],
            ['text' => 'Create Purchase Invoice From Order']
        ]
    ])
@endsection

@section('container')
<div class="container mt-5 col-md-10">
    <h3 class="text-center">Create Purchase Invoice from Order #{{ $purchaseOrder->order_number }}</h3>

    <form id="createInvoiceForm" action="{{ route('dashboard.purchases.storeFromOrder', $purchaseOrder) }}" method="POST">
        @csrf
        <input type="hidden" name="purchase_order_id" value="{{ $purchaseOrder->id }}">

        {{-- Section 1: Order Information (Readonly) --}}
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Order Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Order Number</label>
                        <input type="text" class="form-control" value="{{ $purchaseOrder->order_number }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Order Date</label>
                        <input type="date" class="form-control" value="{{ $purchaseOrder->order_date }}" >
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ship Date</label>
                        <input type="date" class="form-control" value="{{ $purchaseOrder->ship_date }}" >
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Shipping Address</label>
                        <textarea class="form-control">{{ $purchaseOrder->shipping_address }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 2: Invoice Details --}}
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Invoice Details</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="invoice_date" class="form-label">Invoice Date</label>
                        <input type="date" class="form-control" id="invoice_date" name="invoice_date" 
                            value="{{ old('invoice_date', now()->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="received_date" class="form-label">Received Date</label>
                        <input type="date" class="form-control" id="received_date" name="received_date" 
                            value="{{ old('received_date') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="purchase_date" class="form-label">Purchase Date</label>
                        <input type="date" class="form-control" id="purchase_date" name="purchase_date" 
                            value="{{ old('purchase_date', $purchaseOrder->order_date) }}" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Supplier</label>
                        <select name="supplier_id" class="form-control" required>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ $supplier->id == $purchaseOrder->supplier_id ? 'selected' : '' }}>
                                    {{ $supplier->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="payment_requirement" class="form-label">Payment Requirement</label>
                        <input type="text" class="form-control" id="payment_requirement" name="payment_requirement" 
                            value="{{ old('payment_requirement', $purchaseOrder->payment_requirement) }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="due_date" class="form-label">Due Date</label>
                        <input type="date" class="form-control" id="due_date" name="due_date" 
                            value="{{ old('due_date', $purchaseOrder->due_date) }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 3: Dental Materials --}}
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Dental Materials</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="materialsTable">
                        <thead class="table-light">
                            <tr>
                                <th>Material</th>
                                <th>Quantity</th>
                                <th>Purchase Price</th>
                                <th>Unit Price (Auto Calculated)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchaseOrder->details as $detail)
                            <tr>
                                <td>
                                    <select name="dental_material_id[]" class="form-control" required>
                                        @foreach($materials as $material)
                                            <option value="{{ $material->id }}" 
                                                {{ $material->id == $detail->material_id ? 'selected' : '' }}>
                                                {{ $material->name }} ({{ $material->unit_type }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="quantity[]" class="form-control quantity" value="{{ $detail->quantity }}" required>
                                </td>
                                <td>
                                    <input type="number" name="total_price[]" class="form-control total_price" value="{{ $detail->price }}" required>
                                </td>
                                <td>
                                    <input type="number" name="unit_price[]" class="form-control unit_price" value="{{ $detail->price / $detail->quantity }}" readonly>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Section 4: Pricing Summary --}}
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Pricing Summary</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="discount" class="form-label">Discount</label>
                        <input type="number" class="form-control" id="discount" name="discount" 
                            value="{{ old('discount', $purchaseOrder->discount) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="ongkosKirim" class="form-label">Shipping Cost</label>
                        <input type="number" class="form-control" id="ongkosKirim" name="ongkos_kirim" 
                            value="{{ old('ongkos_kirim', $purchaseOrder->ongkos_kirim) }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Total Amount</label>
                        <h4 id="total-amount-display" class="text-primary">0</h4>
                        <input type="hidden" id="total_amount" name="total_amount">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Grand Total</label>
                        <h4 id="grand-total-display" class="text-success">0</h4>
                        <input type="hidden" id="grand_total" name="grand_total">
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="text-center">
            <button type="submit" class="btn btn-success btn-lg px-5">
                <i class="fas fa-file-invoice me-2"></i> Create Purchase Invoice
            </button>
        </div>
    </form>
</div>

<script>
    document.getElementById('createInvoiceForm').addEventListener('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Confirm Purchase Invoice',
            text: "Are you sure you want to create this purchase invoice?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, sure!'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
        function calculateTotalAmount() {
            let total = 0;
            document.querySelectorAll('.total_price').forEach(input => {
                total += parseFloat(input.value) || 0;
            });

            document.getElementById('total-amount-display').textContent = total.toLocaleString('id-ID', { minimumFractionDigits: 2 });
            document.getElementById('total_amount').value = total.toFixed(2);

            let discount = parseFloat(document.getElementById('discount')?.value) || 0;
            let ongkir = parseFloat(document.getElementById('ongkosKirim')?.value) || 0;

            let grandTotal = total - discount + ongkir;
            document.getElementById('grand-total-display').textContent = grandTotal.toLocaleString('id-ID', { minimumFractionDigits: 2 });
            document.getElementById('grand_total').value = grandTotal.toFixed(2);
        }

        document.querySelectorAll('.quantity, .total_price, #discount, #ongkosKirim').forEach(input => {
            input.addEventListener('input', calculateTotalAmount);
        });

        calculateTotalAmount();
    });
</script>
@endsection