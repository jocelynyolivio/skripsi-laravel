@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5 col-md-6">
    <h3 class="text-center">Create Transaction (Without Medical Record)</h3>

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('dashboard.transactions.storeWithoutMedicalRecord') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="patient_id" class="form-label">Patient</label>
            <select class="form-control" id="patient_id" name="patient_id" required>
                @foreach($patients as $patient)
                    <option value="{{ $patient->id }}">{{ $patient->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="procedure_id" class="form-label">Select Procedures</label>
            <div class="input-group">
                <select class="form-control" id="procedure_id">
                    <option value="">-- Select Procedure --</option>
                    @foreach($proceduresWithPrices as $item)
                        <option value="{{ $item['procedure']->id }}" 
                                data-base-price="{{ $item['basePrice'] }}"
                                data-promo-price="{{ $item['promoPrice'] }}">
                            {{ $item['procedure']->name }}
                        </option>
                    @endforeach
                </select>
                <button type="button" class="btn btn-primary" id="add-procedure">Add</button>
            </div>
        </div>

        <div id="selected-items" class="mb-3"></div>

        <!-- Total Amount Section -->
        <div class="card mt-3 bg-primary text-white p-2 w-50 mx-auto">
    <h5 class="text-center mb-0">Total Amount: Rp <span id="total-amount-display">0</span></h5>
</div>



        <input type="hidden" id="total_amount" name="total_amount" value="0">

        <div class="mb-3">
            <label for="payment_method" class="form-label">Payment Method</label>
            <select class="form-control" id="payment_method" name="payment_method" required>
                <option value="cash">Cash</option>
                <option value="card">Card</option>
            </select>
        </div>

        <input type="hidden" name="admin_id" value="{{ auth()->id() }}">

        <button type="submit" class="btn btn-success">Create Transaction</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const procedureSelect = document.getElementById('procedure_id');
        const addProcedureButton = document.getElementById('add-procedure');
        const selectedItemsContainer = document.getElementById('selected-items');
        const totalAmountField = document.getElementById('total_amount');
        const totalAmountDisplay = document.getElementById('total-amount-display');
        let totalAmount = 0;
        let itemIndex = 0;

        addProcedureButton.addEventListener('click', function () {
            const selectedOption = procedureSelect.options[procedureSelect.selectedIndex];
            const procedureId = selectedOption.value;
            const procedureName = selectedOption.text;
            const basePrice = parseFloat(selectedOption.getAttribute('data-base-price')) || 0;
            const promoPrice = parseFloat(selectedOption.getAttribute('data-promo-price')) || null;

            if (procedureId && !document.getElementById(`item-${procedureId}`)) {
                const itemDiv = document.createElement('div');
                itemDiv.classList.add('card', 'p-2', 'mb-2');
                itemDiv.id = `item-${procedureId}`;
                itemDiv.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <span><strong>${procedureName}</strong></span>
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeItem('${procedureId}')">Remove</button>
                    </div>
                    <label>Quantity:</label>
                    <input type="number" name="items[${itemIndex}][quantity]" class="form-control w-50 quantity-input" value="1" min="1">
                    
                    <label>Price:</label>
                    <select name="items[${itemIndex}][unit_price]" class="form-control price-select">
                        <option value="${basePrice}" selected>Base Price: Rp ${basePrice.toLocaleString()}</option>
                        ${promoPrice ? `<option value="${promoPrice}">Promo Price: Rp ${promoPrice.toLocaleString()}</option>` : ''}
                    </select>
                    
                    <input type="hidden" name="items[${itemIndex}][id]" value="${procedureId}">
                `;
                selectedItemsContainer.appendChild(itemDiv);
                itemIndex++;
                updateTotal();
            }
        });

        window.removeItem = function (procedureId) {
            const itemDiv = document.getElementById(`item-${procedureId}`);
            if (itemDiv) {
                itemDiv.remove();
                updateTotal();
            }
        }

        function updateTotal() {
            let newTotal = 0;

            document.querySelectorAll('.quantity-input').forEach((input, index) => {
                const quantity = parseInt(input.value) || 1;
                const priceSelect = document.querySelectorAll('.price-select')[index];
                const unitPrice = parseFloat(priceSelect.value) || 0;
                newTotal += unitPrice * quantity;
            });

            totalAmountField.value = newTotal.toFixed(0);
            totalAmountDisplay.textContent = newTotal.toLocaleString();
        }

        document.addEventListener('change', function (event) {
            if (event.target.classList.contains('quantity-input') || event.target.classList.contains('price-select')) {
                updateTotal();
            }
        });
    });
</script>
@endsection
