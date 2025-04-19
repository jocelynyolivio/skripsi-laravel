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
                        {{ $item['procedure']->item_code }} - {{ $item['procedure']->name }}
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

        <!-- Input untuk Payments -->
        <h5>Payments:</h5>
        <div id="payments-container">
            <div class="payment-item mb-3">
                <label>Payment Amount:</label>
                <input type="number" class="form-control" id="payment" name="payments[0][amount]" min="0" value="0" required>
            </div>

            <div class="form-group">
                <label for="coa_id">Setor Ke (Akun Kas/Bank)</label>
                <select class="form-control" id="coa_id" name="coa_id" required>
                    <option value="">-- Pilih Akun Kas/Bank --</option>
                    @foreach ($cashAccounts as $account)
                    <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="payment_method" class="form-label">Cara Bayar</label>
                <select class="form-control" id="payment_method" name="payments[0][method]" required>
                <option value="">-- Pilih Metode Pembayaran --</option>

                    <!-- QRIS -->
                    <optgroup label="QRIS">
                        <option value="QRIS BCA">QRIS BCA</option>
                        <option value="QRIS CIMB Niaga">QRIS CIMB Niaga</option>
                        <option value="QRIS Mandiri">QRIS Mandiri</option>
                        <option value="QRIS BRI">QRIS BRI</option>
                        <option value="QRIS BNI">QRIS BNI</option>
                        <option value="QRIS Permata">QRIS Permata</option>
                        <option value="QRIS Maybank">QRIS Maybank</option>
                        <option value="QRIS Danamon">QRIS Danamon</option>
                        <option value="QRIS Bank Mega">QRIS Bank Mega</option>
                    </optgroup>

                    <!-- Kartu Kredit/Debit -->
                    <optgroup label="Kartu Kredit/Debit">
                        <option value="Visa">Visa</option>
                        <option value="Mastercard">Mastercard</option>
                        <option value="JCB">JCB</option>
                        <option value="American Express">American Express (AMEX)</option>
                        <option value="GPN">GPN (Gerbang Pembayaran Nasional)</option>
                        <option value="Kartu Kredit BCA">Kartu Kredit BCA</option>
                        <option value="Kartu Kredit Mandiri">Kartu Kredit Mandiri</option>
                        <option value="Kartu Kredit BRI">Kartu Kredit BRI</option>
                        <option value="Kartu Kredit BNI">Kartu Kredit BNI</option>
                        <option value="Kartu Kredit CIMB Niaga">Kartu Kredit CIMB Niaga</option>
                    </optgroup>

                    <!-- Transfer Bank -->
                    <optgroup label="Transfer Bank">
                        <option value="Transfer Bank BCA">Transfer Bank BCA</option>
                        <option value="Transfer Bank Mandiri">Transfer Bank Mandiri</option>
                        <option value="Transfer Bank BRI">Transfer Bank BRI</option>
                        <option value="Transfer Bank BNI">Transfer Bank BNI</option>
                        <option value="Transfer Bank CIMB Niaga">Transfer Bank CIMB Niaga</option>
                        <option value="Transfer Bank Permata">Transfer Bank Permata</option>
                        <option value="Transfer Bank Maybank">Transfer Bank Maybank</option>
                    </optgroup>

                    <!-- E-Wallet -->
                    <optgroup label="E-Wallet">
                        <option value="GoPay">GoPay</option>
                        <option value="OVO">OVO</option>
                        <option value="Dana">Dana</option>
                        <option value="LinkAja">LinkAja</option>
                        <option value="ShopeePay">ShopeePay</option>
                        <option value="Doku Wallet">Doku Wallet</option>
                        <option value="PayPal">PayPal</option>
                    </optgroup>
                </select>
            </div>


            <label>Notes:</label>
            <input type="string" class="form-control" name="payments[0][notes]">
        </div>

        <!-- Sisa Tagihan -->
        <div class="card mt-3 bg-warning text-dark p-2 w-50 mx-auto">
            <h5 class="text-center mb-0">Sisa Tagihan: Rp <span id="remaining-amount-display">0</span></h5>
        </div>
        <br>
        <button type="submit" class="btn btn-success w-100 d-block mx-auto">Create Transaction</button>

</div>

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
        const paymentInput = document.getElementById('payment');

        let itemIndex = 0;

        function updateTotal() {
            console.log("Menghitung Total Amount...");
            let newTotal = 0;

            document.querySelectorAll('.quantity-input').forEach((input, index) => {
                const quantity = parseInt(input.value) || 1;
                const priceSelect = document.querySelectorAll('.price-select')[index];
                const unitPrice = parseFloat(priceSelect.value) || 0;
                const discountInput = document.querySelectorAll('.discount-input')[index];
                const discountType = document.querySelectorAll('.discount-type')[index].value;

                let discount = parseFloat(discountInput.value) || 0;
                let finalDiscount = discount;

                if (discountType === 'percent') {
                    finalDiscount = Math.min((unitPrice * quantity) * (discount / 100), unitPrice * quantity);
                    discountInput.value = finalDiscount.toFixed(0); // Update nilai di form input
                }

                const finalPrice = Math.max((unitPrice * quantity) - finalDiscount, 0);
                document.querySelectorAll('.final-price-display')[index].textContent = formatCurrency(finalPrice);

                console.log(`Item Index: ${index}`);
                console.log(`Quantity: ${quantity}`);
                console.log(`Unit Price: Rp ${unitPrice}`);
                console.log(`Discount Type: ${discountType}`);
                console.log(`Discount: Rp ${discount}`);
                console.log(`Final Discount (Rp): ${finalDiscount}`);
                console.log(`Final Price: Rp ${finalPrice}`);

                newTotal += finalPrice;
            });

            console.log(`New Total Amount: Rp ${newTotal}`);
            totalAmountField.value = newTotal.toFixed(0);
            totalAmountDisplay.textContent = formatCurrency(newTotal);

            updatePaymentLimit(newTotal);
            calculateRemainingPayment(newTotal);
        }


        function calculateRemainingPayment(total) {
            console.log("Calculating Remaining Payment...");
            let payment = parseFloat(paymentInput.value) || 0;
            let remaining = Math.max(total - payment, 0);

            remainingPaymentDisplay.textContent = formatCurrency(remaining);

            console.log(`Payment Made: Rp ${payment}`);
            console.log(`Remaining Payment: Rp ${remaining}`);
        }

        function updatePaymentLimit(total) {
            console.log("Updating Payment Input Limit...");

            paymentInput.max = total;
            let payment = parseFloat(paymentInput.value) || 0;

            if (payment > total) {
                console.warn("⚠️ Payment amount exceeds total due. Adjusting...");
                paymentInput.value = total;
                payment = total;
            }

            calculateRemainingPayment(total);
        }

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

                    <label>Discount:</label>
                    <div class="input-group">
                        <input type="number" name="items[${itemIndex}][discount]" class="form-control discount-input" value="0" min="0">
                        <select class="form-select discount-type" data-index="${itemIndex}">
                            <option value="rp" selected>Rp</option>
                            <option value="percent">%</option>
                        </select>
                    </div>

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

        window.removeItem = function(procedureId) {
            console.log(`Removing Procedure ID: ${procedureId}`);
            const itemDiv = document.getElementById(`item-${procedureId}`);
            if (itemDiv) {
                itemDiv.remove();
                console.log("Procedure Removed. Updating Total...");
                updateTotal();
            }
        };

        document.addEventListener('input', function(event) {
            if (event.target.classList.contains('quantity-input') ||
                event.target.classList.contains('discount-input')) {
                console.log("Quantity atau Diskon diubah. Mengupdate total...");
                updateTotal();
            }
        });

        document.addEventListener('change', function(event) {
            if (event.target.classList.contains('price-select') ||
                event.target.classList.contains('discount-type')) {
                console.log("Harga atau tipe diskon berubah. Mengupdate total...");
                updateTotal();
            }
        });


        paymentInput.addEventListener('input', function() {
            console.log("Payment Changed. Updating Remaining Payment...");
            let total = parseFloat(totalAmountField.value) || 0;
            updatePaymentLimit(total);
        });

        function formatCurrency(amount) {
            return amount.toLocaleString('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).replace('Rp', '').trim();
        }

        updateTotal();
    });
</script>


@endsection