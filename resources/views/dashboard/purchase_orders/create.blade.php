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

            <form action="{{ route('dashboard.purchase_orders.store') }}" method="POST" enctype="multipart/form-data" id="purchaseOrderForm">
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
                        @php
                        $isUsed = in_array($detail->id, $usedDetailIds);
                        @endphp

                        <div class="row mb-3 material-item align-items-end">
                            <div class="col-md-1 text-center">
                                {{-- Checkbox tidak berubah --}}
                                <input type="checkbox" name="selected_materials[{{ $index }}][include]" value="1" class="form-check-input material-checkbox" data-index="{{ $index }}" {{ $isUsed ? 'disabled' : 'checked' }}>
                            </div>
                            <div class="col-md-3">
                                {{-- Info material tidak berubah --}}
                                <label class="form-label small d-block">Material</label>
                                <input type="hidden" name="selected_materials[{{ $index }}][material_id]" value="{{ $detail->dental_material_id }}" class="material-id" id="material_id_{{ $index }}" {{ $isUsed ? 'disabled' : '' }}>
                                {{ $detail->material->name }} ({{ $detail->material->unit_type }})
                                <input type="hidden" name="selected_materials[{{ $index }}][purchase_request_detail_id]" value="{{ $detail->id }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Order Qty</label>
                                <input type="number" name="selected_materials[{{ $index }}][quantity]" class="form-control quantity-input" value="{{ $detail->quantity }}" id="quantity_{{ $index }}" {{ $isUsed ? 'disabled' : '' }} required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Unit Price</label>
                                {{-- Ini hanya untuk tampilan, dihitung oleh JS --}}
                                <p class="form-control-plaintext mb-1 unit-price-display">Rp 0.00</p>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Total Price</label>
                                {{-- Label "Price" diubah menjadi "Total Price" --}}
                                <input type="number" name="selected_materials[{{ $index }}][price]" class="form-control price-input" step="0.01" id="price_{{ $index }}" {{ $isUsed ? 'disabled' : '' }} required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Notes</label>
                                <input type="text" name="selected_materials[{{ $index }}][notes]" class="form-control notes-input" value="{{ $detail->notes ?? '' }}" id="notes_{{ $index }}" {{ $isUsed ? 'disabled' : '' }}>
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

                    <div class="mb-3">
                        <label for="attachment" class="form-label">Attachment (Optional)</label>
                        <input type="file" class="form-control" id="attachment" name="attachment">
                    </div>
                </div>

                <div class="text-end">
                    <button type="button" class="btn btn-primary" id="btn-save">
                        <i class="fas fa-save me-2"></i>Save Purchase Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('purchaseOrderForm');
        const dynamicMaterialList = document.getElementById('dynamic-material-list');
        const addMaterialBtn = document.getElementById('add-material');

        // FUNGSI UTAMA UNTUK MENGHITUNG SEMUANYA
        function calculateTotalAmount() {
            let subtotal = 0;

            document.querySelectorAll('.material-item').forEach(item => {
                const checkbox = item.querySelector('.material-checkbox');
                const quantityInput = item.querySelector('.quantity-input');
                const priceInput = item.querySelector('.price-input'); // Ini adalah Total Price
                const unitPriceDisplay = item.querySelector('.unit-price-display');

                if (checkbox && checkbox.checked && quantityInput && priceInput && unitPriceDisplay) {
                    const quantity = parseFloat(quantityInput.value) || 0;
                    const totalPrice = parseFloat(priceInput.value) || 0;

                    subtotal += totalPrice;

                    const unitPrice = quantity > 0 ? totalPrice / quantity : 0;
                    unitPriceDisplay.textContent = 'Rp ' + unitPrice.toLocaleString('id-ID', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                } else if (unitPriceDisplay) {
                    unitPriceDisplay.textContent = 'Rp 0.00';
                }
            });

            const discount = parseFloat(document.getElementById('discount')?.value) || 0;
            const ongkosKirim = parseFloat(document.getElementById('ongkos_kirim')?.value) || 0;
            const grandTotal = subtotal - discount + ongkosKirim;

            document.getElementById('total-amount-display').textContent = 'Rp ' + subtotal.toLocaleString('id-ID', {
                minimumFractionDigits: 2
            });
            document.getElementById('total_amount').value = subtotal.toFixed(2);
            document.getElementById('grand-total-display').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID', {
                minimumFractionDigits: 2
            });
            document.getElementById('harga_total').value = grandTotal.toFixed(2);
        }

        // SATU EVENT LISTENER UTAMA PADA FORM (EVENT DELEGATION)
        form.addEventListener('input', function(e) {
            // Pemicu untuk input kuantitas, harga, diskon, dan ongkos kirim
            if (e.target.classList.contains('quantity-input') || e.target.classList.contains('price-input') || e.target.id === 'discount' || e.target.id === 'ongkos_kirim') {
                calculateTotalAmount();
            }
        });

        form.addEventListener('change', function(e) {
            // Pemicu untuk checkbox
            if (e.target.classList.contains('material-checkbox')) {
                const index = e.target.dataset.index;
                const inputs = [
                    document.getElementById(`quantity_${index}`),
                    document.getElementById(`price_${index}`),
                    document.getElementById(`notes_${index}`)
                ];

                inputs.forEach(input => {
                    if (input) {
                        input.disabled = !e.target.checked;
                        input.required = e.target.checked;
                    }
                });
                calculateTotalAmount(); // Panggil kalkulasi saat checkbox berubah
            }
        });

        // FUNGSI UNTUK MENAMBAH MATERIAL BARU
        if (addMaterialBtn) {
            addMaterialBtn.addEventListener('click', function() {
                let index = 'new_' + Date.now(); // Indeks unik untuk elemen baru
                let html = `
                <div class="row mb-3 material-item align-items-end">
                    <div class="col-md-1 text-center">
                        {{-- Checkbox tidak butuh nama karena hanya untuk UI --}}
                        <input type="checkbox" value="1" class="form-check-input material-checkbox" data-index="${index}" checked>
                    </div>
                    <div class="col-md-3">
                        {{-- UBAH 'details' MENJADI 'selected_materials' --}}
                        <select name="selected_materials[${index}][material_id]" class="form-select material-id">
                            @foreach($materials as $material)
                                <option value="{{ $material->id }}">{{ $material->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="selected_materials[${index}][quantity]" class="form-control quantity-input" placeholder="Order Qty" required>
                    </div>
                    <div class="col-md-2">
                        <p class="form-control-plaintext mb-1 unit-price-display">Rp 0.00</p>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="selected_materials[${index}][price]" class="form-control price-input" step="0.01" placeholder="Total Price" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm remove-material">X</button>
                    </div>
                </div>`;
                dynamicMaterialList.insertAdjacentHTML('beforeend', html);
            });
        }

        // FUNGSI UNTUK MENGHAPUS MATERIAL (DIBUNGKUS DENGAN PENGECEKAN)
        if (dynamicMaterialList) { // <--- TAMBAHKAN PENGECEKAN INI
            dynamicMaterialList.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-material')) {
                    e.target.closest('.material-item').remove();
                    calculateTotalAmount(); // Panggil kalkulasi setelah menghapus baris
                }
            });
        }

        document.getElementById('btn-save').addEventListener('click', function(e) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to save this purchase order?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, save it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Saat konfirmasi, submit formnya
                    document.getElementById('purchaseOrderForm').submit();
                }
            });
        });

        // Inisialisasi kalkulasi saat halaman dimuat
        calculateTotalAmount();
    });
</script>
@endsection