@extends('dashboard.layouts.main')

@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['text' => 'Purchases Orders', 'url' => route('dashboard.purchase_orders.index')],
            ['text' => 'Create Purchase Order']
        ]
    ])
@endsection

@section('container')
<div class="container mt-5 col-md-10">
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-file-purchase-order me-2"></i>
                Create Purchase Order
            </h5>
        </div>
        <div class="card-body">
            @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('dashboard.purchase_orders.store') }}" method="POST" id="purchaseOrderForm">
                @csrf

                <!-- Basic Information Section -->
                <div class="mb-4">
                    @if(isset($purchaseRequest))
                        <input type="hidden" name="purchase_request_id" value="{{ $purchaseRequest->id }}">
                    @else
                        <div class="mb-3">
                            <label for="purchase_request_id" class="form-label">Purchase Request</label>
                            <select name="purchase_request_id" id="purchase_request_id" class="form-control">
                                <option value="">-- Select Purchase Request --</option>
                                @foreach($requests as $request)
                                    <option value="{{ $request->id }}" {{ old('purchase_request_id') == $request->id ? 'selected' : '' }}>
                                        {{ $request->request_number }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="order_date" class="form-label">Order Date</label>
                            <input type="date" class="form-control" id="order_date" name="order_date" value="{{ old('order_date', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="due_date" class="form-label">Due Date</label>
                            <input type="date" class="form-control" id="due_date" name="due_date" value="{{ old('due_date') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="ship_date" class="form-label">Shipping Date</label>
                            <input type="date" class="form-control" id="ship_date" name="ship_date" value="{{ old('ship_date') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="shipping_address" class="form-label">Shipping Address</label>
                        <input type="text" class="form-control" id="shipping_address" name="shipping_address" value="{{ old('shipping_address') }}">
                    </div>

                    <div class="mb-3">
                        <label for="supplier_id" class="form-label">Supplier</label>
                        <select name="supplier_id" id="supplier_id" class="form-control">
                            <option value="">-- Select Supplier --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Materials Section -->
                <div class="mb-4">
                    <h5 class="mb-3">Materials</h5>
                    <div id="material-list">
                        @if(isset($purchaseRequest))
                            @foreach($purchaseRequest->details as $index => $detail)
                                <div class="row mb-3 material-item align-items-center">
                                    <div class="col-md-1 text-center">
                                        <input type="checkbox" 
                                               name="selected_materials[{{ $index }}][include]" 
                                               value="1" 
                                               checked
                                               class="form-check-input material-checkbox"
                                               data-index="{{ $index }}">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="hidden" 
                                               name="selected_materials[{{ $index }}][material_id]" 
                                               value="{{ $detail->dental_material_id }}"
                                               class="material-id"
                                               id="material_id_{{ $index }}">
                                        {{ $detail->material->name }} ({{ $detail->material->unit_type }})
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small">Requested Qty</label>
                                        <input type="number" class="form-control" value="{{ $detail->quantity }}" readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small">Order Qty</label>
                                        <input type="number" 
                                               name="selected_materials[{{ $index }}][quantity]" 
                                               class="form-control quantity-input"
                                               value="{{ $detail->quantity }}"
                                               id="quantity_{{ $index }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small">Price</label>
                                        <input type="number" 
                                               name="selected_materials[{{ $index }}][price]" 
                                               class="form-control price-input"
                                               step="0.01"
                                               id="price_{{ $index }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small">Notes</label>
                                        <input type="text" 
                                               name="selected_materials[{{ $index }}][notes]" 
                                               class="form-control notes-input"
                                               value="{{ $detail->notes ?? '' }}"
                                               id="notes_{{ $index }}">
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div id="dynamic-material-list"></div>
                            <button type="button" id="add-material" class="btn btn-outline-secondary">
                                <i class="fas fa-plus me-2"></i> Add Material
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Payment & Summary Section -->
                <div class="mb-4">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="payment_requirement">Payment Requirement</label>
                            <select name="payment_requirement" class="form-control" id="payment_requirement" name="payment_requirement" required>
                                <option value="">-- Select Payment Requirement --</option>
                                <option value="cashOnDelivery">Cash On Delivery</option>
                                <option value="30DaysTenor">30 Days After</option>
                                <option value="cashBeforeDelivery">Cash Before Delivery</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="discount" class="form-label">Discount</label>
                            <input type="number" class="form-control" id="discount" name="discount" value="{{ old('discount') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="ongkos_kirim" class="form-label">Delivery Fee</label>
                            <input type="number" class="form-control" id="ongkos_kirim" name="ongkos_kirim" value="{{ old('ongkos_kirim') }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Total Amount</label>
                            <h5 id="total-amount-display">0</h5>
                            <input type="hidden" id="total_amount" name="total_amount">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Grand Total</label>
                            <h5 id="grand-total-display">0</h5>
                            <input type="hidden" id="harga_total" name="harga_total">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea name="notes" id="notes" class="form-control">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add material functionality
    document.getElementById('add-material')?.addEventListener('click', function() {
        let index = document.querySelectorAll('.material-item').length;
        let html = `
            <div class="row mb-2 material-item">
                <div class="col-md-3">
                    <select name="details[${index}][material_id]" class="form-control">
                        @foreach($materials as $material)
                            <option value="{{ $material->id }}">{{ $material->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2"><input type="number" name="details[${index}][quantity]" class="form-control"></div>
                <div class="col-md-2"><input type="number" name="details[${index}][price]" class="form-control" step="0.01"></div>
                <div class="col-md-2"><input type="text" name="details[${index}][unit]" class="form-control"></div>
                <div class="col-md-2"><input type="text" name="details[${index}][notes]" class="form-control"></div>
                <div class="col-md-1"><button type="button" class="btn btn-danger btn-sm remove-material">X</button></div>
            </div>`;
        document.getElementById('dynamic-material-list').insertAdjacentHTML('beforeend', html);
    });

    // Remove material functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-material')) {
            e.target.closest('.material-item').remove();
        }
    });

    // Handle form submission
    document.getElementById('purchaseOrderForm').addEventListener('submit', function(e) {
        document.querySelectorAll('.material-checkbox').forEach(checkbox => {
            if (!checkbox.checked) {
                const index = checkbox.dataset.index;
                document.querySelectorAll(`[name^="selected_materials[${index}]"]`).forEach(el => {
                    el.remove();
                });
            } else {
                const materialInput = document.querySelector(`#material_id_${checkbox.dataset.index}`);
                if (materialInput && materialInput.disabled) {
                    materialInput.disabled = false;
                }
            }
        });
    });

    // Toggle material inputs based on checkbox state
    function toggleMaterialInputs(checkbox) {
        const index = checkbox.dataset.index;
        const inputs = [
            document.getElementById(`quantity_${index}`),
            document.getElementById(`price_${index}`),
            document.getElementById(`notes_${index}`)
        ];
        
        inputs.forEach(input => {
            if (input) {
                input.disabled = !checkbox.checked;
                input.required = checkbox.checked;
            }
        });
    }

    // Initialize material checkboxes
    document.querySelectorAll('.material-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            toggleMaterialInputs(this);
        });
        toggleMaterialInputs(checkbox);
    });

    // Calculate totals function
    function calculateTotalAmount() {
        let total = 0;
        document.querySelectorAll('.material-item').forEach(item => {
            const checkbox = item.querySelector('.material-checkbox');
            const quantityInput = item.querySelector('.quantity-input');
            const priceInput = item.querySelector('.price-input');

            if (checkbox && checkbox.checked && quantityInput && priceInput) {
                const quantity = parseFloat(quantityInput.value) || 0;
                const price = parseFloat(priceInput.value) || 0;
                total += price;
            }
        });

        const discount = parseFloat(document.getElementById('discount')?.value) || 0;
        const ongkosKirim = parseFloat(document.getElementById('ongkos_kirim')?.value) || 0;
        const grandTotal = total - discount + ongkosKirim;

        document.getElementById('total-amount-display').textContent = total.toLocaleString('id-ID', { minimumFractionDigits: 2 });
        document.getElementById('total_amount').value = total.toFixed(2);
        document.getElementById('grand-total-display').textContent = grandTotal.toLocaleString('id-ID', { minimumFractionDigits: 2 });
        document.getElementById('harga_total').value = grandTotal.toFixed(2);
    }

    // Event listeners for calculations
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('quantity-input') || 
            e.target.classList.contains('price-input') || 
            e.target.id === 'discount' || 
            e.target.id === 'ongkos_kirim') {
            calculateTotalAmount();
        }
    });

    // Initial calculation
    calculateTotalAmount();
});
</script>
@endsection