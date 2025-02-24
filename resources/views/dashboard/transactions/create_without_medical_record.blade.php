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

        <input type="hidden" name="admin_id" value="{{ auth()->id() }}">
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

        <!-- Metode Pembayaran -->
        <div class="mb-3">
            <label for="payment_method" class="form-label">Payment Method</label>
            <select class="form-control" id="payment_method" name="payment_method" required>
                <option value="cash">Cash</option>
                <option value="card">Card</option>
            </select>
        </div>

        <!-- Input untuk Payments -->
        <h5>Payments:</h5>
        <div id="payments-container">
            <div class="payment-item mb-3">
                <label>Payment Amount:</label>
                <input type="number" class="form-control" id="payment" name="payments[0][amount]" min="0" value="0" required>

                <label>Payment Method:</label>
                <select class="form-control" name="payments[0][payment_method]" required>
                    <option value="cash">Cash</option>
                    <option value="card">Card</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <label>Notes:</label>
            <input type="string" class="form-control" name="payments[0][notes]">
        </div>

        <!-- Sisa Tagihan -->
        <div class="card mt-3 bg-warning text-dark p-2 w-50 mx-auto">
            <h5 class="text-center mb-0">Sisa Tagihan: Rp <span id="remaining-amount-display">0</span></h5>
        </div>
</div>

<button type="submit" class="btn btn-success">Create Transaction</button>
</form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    console.log("Script Loaded: Form Transaction Without Medical Record is Ready!");

    const procedureSelect = document.getElementById('procedure_id');
    const addProcedureButton = document.getElementById('add-procedure');
    const selectedItemsContainer = document.getElementById('selected-items');
    const totalAmountField = document.getElementById('total_amount');
    const totalAmountDisplay = document.getElementById('total-amount-display');
    const remainingPaymentDisplay = document.getElementById('remaining-amount-display');
    let itemIndex = 0;

    // Event Listener untuk Tombol "Add"
    addProcedureButton.addEventListener('click', function() {
        const selectedOption = procedureSelect.options[procedureSelect.selectedIndex];
        const procedureId = selectedOption.value;
        const procedureName = selectedOption.text;
        const basePrice = parseFloat(selectedOption.getAttribute('data-base-price')) || 0;
        const promoPrice = parseFloat(selectedOption.getAttribute('data-promo-price')) || null;

        console.log(`Selected Procedure ID: ${procedureId}`);
        console.log(`Selected Procedure Name: ${procedureName}`);
        console.log(`Base Price: Rp ${basePrice}`);
        console.log(`Promo Price: Rp ${promoPrice}`);

        // Cek Apakah Item Sudah Ada
        if (procedureId && !document.getElementById(`item-${procedureId}`)) {
            console.log("Adding Procedure to List...");

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
                    <option value="${basePrice}" selected>Base Price: Rp ${formatCurrency(basePrice)}</option>
                    ${promoPrice ? `<option value="${promoPrice}">Promo Price: Rp ${formatCurrency(promoPrice)}</option>` : ''}
                </select>

                <label>Discount (Rp):</label>
                <input type="number" name="items[${itemIndex}][discount]" class="form-control discount-input" value="0" min="0">

                <label>Final Price:</label>
                <p><strong>Rp <span class="final-price-display">0</span></strong></p>

                <input type="hidden" name="items[${itemIndex}][id]" value="${procedureId}">
            `;
            selectedItemsContainer.appendChild(itemDiv);
            itemIndex++;
            console.log("Procedure Added. Updating Total...");
            updateTotal();
        } else {
            console.log("Procedure is already in the list or not selected.");
        }
    });

    // Fungsi untuk Menghapus Item
    window.removeItem = function(procedureId) {
        console.log(`Removing Procedure ID: ${procedureId}`);
        const itemDiv = document.getElementById(`item-${procedureId}`);
        if (itemDiv) {
            itemDiv.remove();
            console.log("Procedure Removed. Updating Total...");
            updateTotal();
        }
    }

    // Fungsi untuk Menghitung Total Harga
    function updateTotal() {
        console.log("Calculating Total Amount...");
        let newTotal = 0;

        document.querySelectorAll('.quantity-input').forEach((input, index) => {
            const quantity = parseInt(input.value) || 1;
            const priceSelect = document.querySelectorAll('.price-select')[index];
            const unitPrice = parseFloat(priceSelect.value) || 0;
            const discountInput = document.querySelectorAll('.discount-input')[index];
            const discount = parseFloat(discountInput.value) || 0;
            const finalPrice = Math.max((unitPrice * quantity) - discount, 0);

            console.log(`Item Index: ${index}`);
            console.log(`Quantity: ${quantity}`);
            console.log(`Unit Price: Rp ${unitPrice}`);
            console.log(`Discount: Rp ${discount}`);
            console.log(`Final Price: Rp ${finalPrice}`);

            document.querySelectorAll('.final-price-display')[index].textContent = formatCurrency(finalPrice);
            newTotal += finalPrice;
        });

        console.log(`New Total Amount: Rp ${newTotal}`);
        totalAmountField.value = newTotal.toFixed(0);
        totalAmountDisplay.textContent = formatCurrency(newTotal);

        calculateRemainingPayment(newTotal);
    }

    // Fungsi untuk Menghitung Sisa Tagihan
    function calculateRemainingPayment(total) {
        console.log("Calculating Remaining Payment...");
        const paymentInputs = document.querySelectorAll('#payments-container input[type="number"]');
        let totalPayment = 0;

        paymentInputs.forEach(input => {
            const paymentAmount = parseFloat(input.value) || 0;
            totalPayment += paymentAmount;
            console.log(`Payment Amount: Rp ${paymentAmount}`);
        });

        const remaining = Math.max(total - totalPayment, 0);
        console.log(`Remaining Payment: Rp ${remaining}`);
        remainingPaymentDisplay.textContent = formatCurrency(remaining);
    }

    // Fungsi untuk Memformat ke Rupiah
    function formatCurrency(amount) {
        return amount.toLocaleString('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).replace('Rp', '').trim();
    }

    // Event Listener untuk Perubahan Quantity atau Diskon
    document.addEventListener('input', function(event) {
        if (event.target.classList.contains('quantity-input') ||
            event.target.classList.contains('discount-input')) {
            console.log("Quantity or Discount Changed. Updating Total...");
            updateTotal();
        }
    });

    // Event Listener untuk Perubahan Harga
    document.addEventListener('change', function(event) {
        if (event.target.classList.contains('price-select')) {
            console.log("Price Changed. Updating Total...");
            updateTotal();
        }
    });

    // Event Listener untuk Perubahan Pembayaran
    document.querySelectorAll('#payments-container input[type="number"]').forEach(paymentInput => {
        paymentInput.addEventListener('input', function() {
            console.log("Payment Changed. Calculating Remaining Payment...");
            updateTotal();
        });
    });
});

</script>

@endsection