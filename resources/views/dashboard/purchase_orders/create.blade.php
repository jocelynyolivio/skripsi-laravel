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
<div class="container">
    <h1>Create Purchase Order</h1>
    @if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <form action="{{ route('dashboard.purchase_orders.store') }}" method="POST" id="purchaseOrderForm">
        @csrf

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

        <div class="mb-3">
            <label for="order_date" class="form-label">Order Date</label>
            <input type="date" class="form-control" id="order_date" name="order_date" value="{{ old('order_date', date('Y-m-d')) }}" required>
        </div>

        <div class="mb-3">
            <label for="due_date" class="form-label">Due Date</label>
            <input type="date" class="form-control" id="due_date" name="due_date" value="{{ old('due_date') }}">
        </div>

        <div class="mb-3">
            <label for="ship_date" class="form-label">Shipping Date</label>
            <input type="date" class="form-control" id="ship_date" name="ship_date" value="{{ old('ship_date') }}">
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

        <h4>Materials</h4>
        <div id="material-list">
            @if(isset($purchaseRequest))
                @foreach($purchaseRequest->details as $index => $detail)
                    <div class="row mb-2 material-item">
                        <div class="col-md-1">
                            <input type="checkbox" 
                                   name="selected_materials[{{ $index }}][include]" 
                                   value="1" 
                                   checked
                                   class="material-checkbox"
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
                            <label>Requested Qty</label>
                            <input type="number" class="form-control" value="{{ $detail->quantity }}" readonly>
                        </div>
                        <div class="col-md-2">
                            <label>Order Qty</label>
                            <input type="number" 
                                   name="selected_materials[{{ $index }}][quantity]" 
                                   class="form-control quantity-input"
                                   value="{{ $detail->quantity }}"
                                   id="quantity_{{ $index }}">
                        </div>
                        <div class="col-md-2">
                            <label>Price</label>
                            <input type="number" 
                                   name="selected_materials[{{ $index }}][price]" 
                                   class="form-control price-input"
                                   step="0.01"
                                   id="price_{{ $index }}">
                        </div>
                        <div class="col-md-2">
                            <label>Notes</label>
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
                <button type="button" id="add-material" class="btn btn-secondary mb-3">Add Material</button>
            @endif
        </div>
        <div class="mb-3">
            <label for="payment_requirement">Payment Requirement</label>
            <select name="payment_requirement" class="form-control" id="payment_requirement" name="payment_requirement" required>
                <option value="">-- Select Payment Requirement --</option>
                <option value="cashOnDelivery">Cash On Delivery</option>
                <option value="30DaysTenor">30 Days After</option>
                <option value="cashBeforeDelivery">Cash Before Delivery</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="discount" class="form-label">Discount</label>
            <input type="number" class="form-control" id="discount" name="discount" value="{{ old('discount') }}">
        </div>

        <div class="mb-3">
            <label for="ongkos_kirim" class="form-label">Delivery Fee</label>
            <input type="number" class="form-control" id="ongkos_kirim" name="ongkos_kirim" value="{{ old('ongkos_kirim') }}" >
        </div>


        <div class="mb-3">
            <label for="notes" class="form-label">Notes</label>
            <textarea name="notes" id="notes" class="form-control">{{ old('notes') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Save</button>
    </form>
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
    // Hapus semua baris yang tidak dicentang
    document.querySelectorAll('.material-checkbox').forEach(checkbox => {
        if (!checkbox.checked) {
            const index = checkbox.dataset.index;

            // Hapus field hanya kalau tidak dicentang
            document.querySelectorAll(`[name^="selected_materials[${index}]"]`).forEach(el => {
                el.remove();
            });
        } else {
            // Pastikan material_id aktif (tidak disabled/null)
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
        // Initialize state
        toggleMaterialInputs(checkbox);
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // Tambahkan hidden input untuk harga total
    const form = document.getElementById('purchaseOrderForm');
    const hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = 'harga_total';
    hiddenInput.id = 'harga_total';
    form.appendChild(hiddenInput);

    // Optional: tempatkan juga tampilan harga total ke user
    const totalDisplay = document.createElement('div');
    totalDisplay.classList.add('mb-3');
    totalDisplay.innerHTML = `
        <label class="form-label">Total Price (After Discount & Delivery Fee)</label>
        <input type="text" class="form-control" id="harga_total_display" name="harga_total" readonly>
    `;
    form.insertBefore(totalDisplay, form.querySelector('button[type="submit"]'));

    function calculateTotal() {
    let total = 0;

    document.querySelectorAll('.material-item').forEach(item => {
        const checkbox = item.querySelector('.material-checkbox');
        const quantityInput = item.querySelector('.quantity-input');
        const priceInput = item.querySelector('.price-input');

        if (checkbox && checkbox.checked && quantityInput && priceInput) {
            const quantity = parseFloat(quantityInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;
            total +=  price;
        }
    });

    const discount = parseFloat(document.getElementById('discount')?.value) || 0;
    const ongkosKirim = parseFloat(document.getElementById('ongkos_kirim')?.value) || 0;

    // Hitung total setelah diskon dan ongkos kirim
    let grandTotal = total - discount + ongkosKirim;

    // Update hidden input dan display
    document.getElementById('harga_total').value = grandTotal.toFixed(2);
    document.getElementById('harga_total_display').value = grandTotal.toFixed(2);
}

document.addEventListener('input', function(e) {
    if (e.target.classList.contains('quantity-input') || 
        e.target.classList.contains('price-input') || 
        e.target.id === 'discount' || 
        e.target.id === 'ongkos_kirim') {
        calculateTotal();
    }
});

// Panggil sekali saat halaman load
calculateTotal();


    // Juga hitung ulang kalau centang/uncheck material
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('material-checkbox')) {
            calculateTotal();
        }
    });

    // Hitung awal saat halaman load
    calculateTotal();
});

</script>
@endsection