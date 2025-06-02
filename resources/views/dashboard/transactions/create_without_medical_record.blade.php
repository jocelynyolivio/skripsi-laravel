@extends('dashboard.layouts.main')

@section('breadcrumbs')
@include('dashboard.layouts.breadcrumbs', [
'customBreadcrumbs' => [
['url' => route('dashboard.transactions.index'), 'text' => 'Transactions'],
['text' => 'Create Manual Transaction']
]
])
@endsection

@section('container')
<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-9"> {{-- Adjusted column width for a comprehensive form --}}
        <div class="card mt-4 shadow-sm">
            <div class="card-header bg-light">
                <h4 class="card-title mb-0">Create Manual Transaction</h4>
                <p class="text-muted mb-0">For transactions without a prior medical record.</p>
            </div>
            <div class="card-body">
                @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
                @endif

                <form id="manual-transaction-form" action="{{ route('dashboard.transactions.storeWithoutMedicalRecord') }}" method="POST">
                    @csrf
                    <input type="hidden" name="admin_id" value="{{ auth()->id() }}">

                    {{-- Patient Selection Section --}}
                    <div class="border rounded p-3 mb-4">
                        <h5 class="mb-3">1. Select Patient</h5>
                        <div class="mb-3">
                            <label for="patient_id" class="form-label">Patient <span class="text-danger">*</span></label>
                            <select class="form-select @error('patient_id') is-invalid @enderror" id="patient_id" name="patient_id" required>
                                <option value="">-- Select Patient --</option>
                                @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                    {{ $patient->fname }} {{ $patient->mname ?? '' }} {{ $patient->lname }} (ID: {{ $patient->id }})
                                </option>
                                @endforeach
                            </select>
                            @error('patient_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Procedure Management Section --}}
                    <div class="border rounded p-3 mb-4">
                        <h5 class="mb-3">2. Manage Procedures</h5>
                        <div class="mb-3">
                            <label for="procedure_id_selector" class="form-label">Add Procedure</label>
                            <div class="input-group">
                                <select class="form-select" id="procedure_id_selector">
                                    <option value="">-- Select Procedure --</option>
                                    @foreach($proceduresWithPrices as $item)
                                    <option value="{{ $item['procedure']->id }}"
                                        data-base-price="{{ $item['basePrice'] }}"
                                        data-promo-price="{{ $item['promoPrice'] ?? '' }}">
                                        {{ $item['procedure']->item_code }} - {{ $item['procedure']->name }}
                                    </option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-primary" id="add-procedure-btn">
                                    <i class="bi bi-plus-circle"></i> Add
                                </button>
                            </div>
                        </div>

                        <div id="selected-items-container" class="mt-3">
                            {{-- Dynamically added procedures will appear here --}}
                            <p class="text-muted" id="no-items-text">No procedures added yet.</p>
                        </div>
                    </div>

                    {{-- Voucher and Totals Summary Section --}}
                    <div class="row mt-4 align-items-end">
                        <div class="col-md-6 mb-3">
                            <label for="voucher" class="form-label">Apply Voucher (Optional)</label>
                            <select class="form-select" id="voucher" name="voucher">
                                <option value="">-- No Voucher --</option>
                                @foreach ($vouchers as $voucher)
                                <option value="{{ $voucher->birthday_voucher_code }}">{{ $voucher->birthday_voucher_code }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3 bg-light">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="mb-0">Total Amount:</h5>
                                    <h5 class="mb-0 text-primary" id="total-amount-display">Rp 0</h5>
                                </div>
                                <hr class="my-1">
                                <div class="d-flex justify-content-between align-items-center text-danger">
                                    <h6 class="mb-0">Remaining Bill:</h6>
                                    <h6 class="mb-0" id="remaining-amount-display">Rp 0</h6>
                                </div>
                                <input type="hidden" id="total_amount_hidden" name="total_amount" value="0">
                                <input type="hidden" id="remaining_amount_hidden" name="remaining_amount" value="0">
                            </div>
                        </div>
                    </div>

                    {{-- Payment Details Section --}}
                    <div class="border rounded p-3 mt-4">
                        <h5 class="mb-3">3. Payment Details</h5>
                        <div id="payments-container">
                            {{-- Default first payment item --}}
                            <div class="payment-item">
                                <div class="row g-3">
                                    <div class="col-md-6 col-lg-4">
                                        <label for="payment_amount_0" class="form-label">Payment Amount <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control payment-amount-input" id="payment_amount_0" name="payments[0][amount]" min="0" value="0" required>
                                    </div>
                                    <div class="col-md-6 col-lg-4">
                                        <label for="payment_method_0" class="form-label">Payment Method <span class="text-danger">*</span></label>
                                        <select class="form-select payment-method-select" id="payment_method_0" name="payments[0][method]" required>
                                            <option value="">-- Select Method --</option>
                                            <optgroup label="Tunai">
                                                <option value="tunai" {{ old('payments.0.method') == 'tunai' ? 'selected' : '' }}>Tunai</option>
                                            </optgroup>
                                            <optgroup label="QRIS">
                                                <option value="QRIS BCA">QRIS BCA</option>
                                                {{-- Add other QRIS options here --}}
                                                <option value="QRIS Mandiri">QRIS Mandiri</option>
                                            </optgroup>
                                            <optgroup label="Kartu Kredit/Debit">
                                                <option value="Visa">Visa</option>
                                                {{-- Add other Card options here --}}
                                                <option value="Mastercard">Mastercard</option>
                                            </optgroup>
                                            <optgroup label="Transfer Bank">
                                                <option value="Transfer Bank BCA">Transfer Bank BCA</option>
                                                {{-- Add other Transfer options here --}}
                                            </optgroup>
                                            <optgroup label="E-Wallet">
                                                <option value="GoPay">GoPay</option>
                                                {{-- Add other E-Wallet options here --}}
                                            </optgroup>
                                        </select>
                                    </div>
                                    <div class="col-md-6 col-lg-4">
                                        <label for="coa_id_0" class="form-label">Deposit To (Account) <span class="text-danger">*</span></label>
                                        <select class="form-select coa-select" id="coa_id_0" name="payments[0][coa_id]" required>
                                            <option value="">-- Select Account --</option>
                                            @foreach ($cashAccounts as $account)
                                            <option value="{{ $account->id }}" {{ old('payments.0.coa_id') == $account->id ? 'selected' : '' }}>
                                                {{ $account->code }} - {{ $account->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 col-lg-12">
                                        <label for="payment_notes_0" class="form-label">Notes (Optional)</label>
                                        <input type="text" class="form-control payment-notes-input" id="payment_notes_0" name="payments[0][notes]" value="{{ old('payments.0.notes') }}">
                                    </div>
                                </div>
                                {{-- Option to add more payment methods can be added here if needed --}}
                            </div>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="text-end mt-4">
                        <a href="{{ route('dashboard.transactions.index') }}" class="btn btn-secondary me-2">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg" id="create-transaction-btn">
                            <i class="bi bi-check-circle"></i> Create Transaction
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const procedureSelector = document.getElementById('procedure_id_selector');
        const addProcedureButton = document.getElementById('add-procedure-btn');
        const selectedItemsContainer = document.getElementById('selected-items-container');
        const noItemsText = document.getElementById('no-items-text');

        const totalAmountField = document.getElementById('total_amount_hidden');
        const totalAmountDisplay = document.getElementById('total-amount-display');
        const remainingAmountField = document.getElementById('remaining_amount_hidden');
        const remainingAmountDisplay = document.getElementById('remaining-amount-display');
        const paymentAmountInput = document.querySelector('.payment-amount-input'); // Assumes one payment method for now

        let itemIndex = 0;
        let proceduresData = []; // To store data of added procedures

        function formatCurrency(value, withRp = true) {
            const number = parseFloat(value) || 0;
            const formatted = number.toLocaleString('id-ID');
            return withRp ? 'Rp ' + formatted : formatted;
        }

        function calculateGrandTotal() {
            let grandTotal = 0;
            proceduresData.forEach(item => {
                const quantity = parseInt(item.quantity) || 1;
                const unitPrice = parseFloat(item.unitPrice) || 0;
                let discount = parseFloat(item.discountValue) || 0;
                let itemSubtotal = unitPrice * quantity;

                if (item.discountType === 'percent') {
                    discount = (itemSubtotal * (discount / 100));
                }
                discount = Math.min(discount, itemSubtotal); // Cap discount

                grandTotal += (itemSubtotal - discount);
            });

            totalAmountField.value = grandTotal;
            totalAmountDisplay.textContent = formatCurrency(grandTotal);
            calculateRemainingPayment();
            updatePaymentInputMax();
        }

        function calculateRemainingPayment() {
            const total = parseFloat(totalAmountField.value) || 0;
            const payment = parseFloat(paymentAmountInput.value) || 0;
            const remaining = Math.max(0, total - payment);

            remainingAmountField.value = remaining;
            remainingAmountDisplay.textContent = formatCurrency(remaining);
        }

        function updatePaymentInputMax() {
            const total = parseFloat(totalAmountField.value) || 0;
            paymentAmountInput.max = total;
            if (parseFloat(paymentAmountInput.value) > total) {
                paymentAmountInput.value = total;
                calculateRemainingPayment();
            }
        }

        addProcedureButton.addEventListener('click', function() {
            const selectedOption = procedureSelector.options[procedureSelector.selectedIndex];
            if (!selectedOption.value) {
                Swal.fire('No Procedure', 'Please select a procedure to add.', 'warning');
                return;
            }

            const procedureId = selectedOption.value;
            // Check if procedure already added
            if (proceduresData.find(p => p.id === procedureId)) {
                Swal.fire('Already Added', 'This procedure has already been added.', 'info');
                return;
            }

            const procedureName = selectedOption.text;
            const basePrice = parseFloat(selectedOption.dataset.basePrice) || 0;
            const promoPrice = selectedOption.dataset.promoPrice ? parseFloat(selectedOption.dataset.promoPrice) : null;

            const currentItemIndex = itemIndex++;
            const procedureDataItem = {
                id: procedureId,
                name: procedureName,
                basePrice: basePrice,
                promoPrice: promoPrice,
                unitPrice: basePrice, // Default to base price
                quantity: 1,
                discountValue: 0,
                discountType: 'rp', // 'rp' or 'percent'
                itemIndex: currentItemIndex
            };
            proceduresData.push(procedureDataItem);

            renderAddedItem(procedureDataItem);
            calculateGrandTotal();
            if (noItemsText) noItemsText.style.display = 'none';
            procedureSelector.value = ""; // Reset selector
        });

        function renderAddedItem(itemData) {
            const itemDiv = document.createElement('div');
            itemDiv.classList.add('card', 'mb-3', 'shadow-sm', `procedure-item-${itemData.itemIndex}`);
            itemDiv.dataset.itemIndex = itemData.itemIndex;

            let priceOptionsHtml = `<option value="${itemData.basePrice}" ${itemData.unitPrice == itemData.basePrice ? 'selected' : ''}>Base: ${formatCurrency(itemData.basePrice)}</option>`;
            if (itemData.promoPrice !== null && typeof itemData.promoPrice !== 'undefined' && itemData.promoPrice !== '') { // Added check for promoPrice
                priceOptionsHtml += `<option value="${itemData.promoPrice}" ${itemData.unitPrice == itemData.promoPrice ? 'selected' : ''}>Promo: ${formatCurrency(itemData.promoPrice)}</option>`;
            }

            itemDiv.innerHTML = `
            <div class="card-header bg-white py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 text-primary">${itemData.name}</h6>
                    <button type="button" class="btn btn-danger btn-sm remove-item-btn py-0 px-2" data-item-index="${itemData.itemIndex}">
                        <i class="bi bi-trash"></i> Remove
                    </button>
                </div>
            </div>
            <div class="card-body p-3">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="quantity-${itemData.itemIndex}" class="form-label form-label-sm">Quantity</label>
                        <input type="number" class="form-control form-control-sm quantity-input" id="quantity-${itemData.itemIndex}" name="items[${itemData.itemIndex}][quantity]" value="${itemData.quantity}" min="1" data-item-index="${itemData.itemIndex}">
                        <input type="hidden" name="items[${itemData.itemIndex}][id]" value="${itemData.id}">
                    </div>
                    <div class="col-md-4">
                        <label for="price-select-${itemData.itemIndex}" class="form-label form-label-sm">Unit Price</label>
                        <select class="form-select form-select-sm price-select" id="price-select-${itemData.itemIndex}" name="items[${itemData.itemIndex}][unit_price]" data-item-index="${itemData.itemIndex}">
                            ${priceOptionsHtml}
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label for="discount-${itemData.itemIndex}" class="form-label form-label-sm">Discount</label>
                        <div class="input-group input-group-sm">
                            <input type="number" class="form-control form-control-sm discount-input" id="discount-${itemData.itemIndex}" name="items[${itemData.itemIndex}][discount_value]" value="${itemData.discountValue}" min="0" data-item-index="${itemData.itemIndex}">
                            <select class="form-select form-select-sm discount-type" name="items[${itemData.itemIndex}][discount_type]" style="max-width: 65px;" data-item-index="${itemData.itemIndex}">
                                <option value="rp" ${itemData.discountType === 'rp' ? 'selected' : ''}>Rp</option>
                                <option value="percent" ${itemData.discountType === 'percent' ? 'selected' : ''}>%</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 mt-2 text-end">
                        <small class="text-muted">Applied Discount: Rp <span class="applied-discount-display">0</span></small><br>
                        <strong>Subtotal: Rp <span class="final-price-display">0</span></strong>
                    </div>
                </div>
            </div>
        `;
            selectedItemsContainer.appendChild(itemDiv);
            updateItemDisplay(itemData.itemIndex);
        }

        selectedItemsContainer.addEventListener('click', function(event) {
            if (event.target.closest('.remove-item-btn')) {
                const button = event.target.closest('.remove-item-btn');
                const idxToRemove = parseInt(button.dataset.itemIndex);

                proceduresData = proceduresData.filter(p => p.itemIndex !== idxToRemove);
                document.querySelector(`.procedure-item-${idxToRemove}`).remove();

                if (proceduresData.length === 0 && noItemsText) {
                    noItemsText.style.display = 'block';
                }
                calculateGrandTotal();
            }
        });

        selectedItemsContainer.addEventListener('change', function(event) {
            const target = event.target;
            if (target.classList.contains('quantity-input') || target.classList.contains('price-select') || target.classList.contains('discount-input') || target.classList.contains('discount-type')) {
                const idx = parseInt(target.dataset.itemIndex);
                const itemData = proceduresData.find(p => p.itemIndex === idx);
                if (itemData) {
                    if (target.classList.contains('quantity-input')) itemData.quantity = target.value;
                    if (target.classList.contains('price-select')) itemData.unitPrice = target.value;
                    if (target.classList.contains('discount-input')) itemData.discountValue = target.value;
                    if (target.classList.contains('discount-type')) itemData.discountType = target.value;

                    updateItemDisplay(idx);
                    calculateGrandTotal();
                }
            }
        });
        selectedItemsContainer.addEventListener('input', function(event) { // For discount input to react immediately
            const target = event.target;
            if (target.classList.contains('discount-input') || target.classList.contains('quantity-input')) {
                const idx = parseInt(target.dataset.itemIndex);
                const itemData = proceduresData.find(p => p.itemIndex === idx);
                if (itemData) {
                    if (target.classList.contains('quantity-input')) itemData.quantity = target.value;
                    if (target.classList.contains('discount-input')) itemData.discountValue = target.value;

                    updateItemDisplay(idx);
                    calculateGrandTotal();
                }
            }
        });


        function updateItemDisplay(idx) {
            const itemData = proceduresData.find(p => p.itemIndex === idx);
            if (!itemData) return;

            const itemDiv = document.querySelector(`.procedure-item-${idx}`);
            const quantity = parseInt(itemData.quantity) || 1;
            const unitPrice = parseFloat(itemData.unitPrice) || 0;
            let discountAmount = parseFloat(itemData.discountValue) || 0;
            const itemSubtotalBeforeDiscount = unitPrice * quantity;

            if (itemData.discountType === 'percent') {
                discountAmount = (itemSubtotalBeforeDiscount * (discountAmount / 100));
            }
            discountAmount = Math.min(discountAmount, itemSubtotalBeforeDiscount); // Cap discount

            const finalItemPrice = itemSubtotalBeforeDiscount - discountAmount;

            itemDiv.querySelector('.applied-discount-display').textContent = formatCurrency(discountAmount, false);
            itemDiv.querySelector('.final-price-display').textContent = formatCurrency(finalItemPrice, false);
        }

        if (paymentAmountInput) {
            paymentAmountInput.addEventListener('input', calculateRemainingPayment);
        }

        // Initial calculation if there are any old inputs (e.g. from validation error)
        // This part needs to be more robust if you use old() for items
        calculateGrandTotal();

        // Form submission confirmation
        const form = document.getElementById('manual-transaction-form');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (proceduresData.length === 0) {
                    Swal.fire('No Procedures', 'Please add at least one procedure to the transaction.', 'warning');
                    return;
                }
                const total = parseFloat(totalAmountField.value) || 0;
                const payment = parseFloat(paymentAmountInput.value) || 0;
                if (payment < total && payment > 0) {
                    Swal.fire({
                        title: 'Incomplete Payment',
                        text: `The payment amount (Rp ${formatCurrency(payment,false)}) is less than the total bill (Rp ${formatCurrency(total,false)}). The remaining bill of Rp ${formatCurrency(total-payment,false)} will be recorded as payable. Continue?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Continue',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            e.target.submit();
                        }
                    });
                } else if (payment <= 0 && total > 0) {
                    Swal.fire({
                        title: 'No Payment Made',
                        text: `No payment has been entered for a bill of Rp ${formatCurrency(total,false)}. The entire amount will be recorded as payable. Continue?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Continue',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            e.target.submit();
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Confirm Transaction Creation',
                        html: `Total Bill: <strong>${formatCurrency(total)}</strong><br>Payment Made: <strong>${formatCurrency(payment)}</strong><br>Remaining: <strong>${formatCurrency(total-payment)}</strong><br><br>Are you sure you want to create this transaction?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Create!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            e.target.submit();
                        }
                    });
                }
            });
        }
    });
</script>
<style>
    .form-label-sm {
        font-size: .7875em;
        /* Smaller labels for item cards */
        margin-bottom: .2rem;
    }

    .form-control-sm,
    .form-select-sm {
        font-size: .7875rem;
        /* Smaller inputs for item cards */
    }

    #selected-items-container .card-header h6 {
        font-size: 0.9rem;
    }
</style>
@endsection