@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5 col-md-6">
    <h3 class="text-center">Create Transaction for Medical Record ID: {{ $medicalRecord->id }}</h3>

    <div class="card mb-3" style="border: 1px solid;">
        <div class="card-header">
            <strong>Medical Record Details</strong>
        </div>
        <div class="card-body">
            <p><strong>Patient:</strong> {{ $medicalRecord->patient->name }}</p>
            <p><strong>Doctor:</strong> {{ $medicalRecord->doctor->name }}</p>
            <p><strong>Reservation Date:</strong> {{ $medicalRecord->tanggal_reservasi }}</p>
        </div>
    </div>

    <form action="{{ route('dashboard.transactions.store') }}" method="POST">
        @csrf
        <input type="hidden" name="medical_record_id" value="{{ $medicalRecord->id }}">
        <input type="hidden" name="user_id" value="{{ auth()->id() }}">
        <input type="hidden" name="admin_id" value="{{ auth()->id() }}">

        @foreach($proceduresWithPrices as $index => $item)
        <input type="hidden" name="procedure_ids[]" value="{{ $item['procedure']->id }}">

        <div class="mb-3">
            <label><strong>{{ $item['procedure']->name }}</strong> (x{{ $item['quantity'] }})</label>
            <input type="hidden" class="quantity-input" value="{{ $item['quantity'] }}">

            <div class="form-check">
                <input class="form-check-input amount-input" type="radio" name="amount[{{ $item['procedure']->id }}]" value="{{ $item['basePrice'] }}" checked>
                <label class="form-check-label">Base Price: Rp {{ number_format($item['basePrice'], 0, ',', '.') }}</label>
            </div>

            @if($item['promoPrice'])
            <div class="form-check">
                <input class="form-check-input amount-input" type="radio" name="amount[{{ $item['procedure']->id }}]" value="{{ $item['promoPrice'] }}">
                <label class="form-check-label">Promo Price: Rp {{ number_format($item['promoPrice'], 0, ',', '.') }}</label>
            </div>
            @endif

            <label>Discount:</label>
            <div class="input-group">
                <input type="number" name="discount[{{ $item['procedure']->id }}]" class="form-control discount-input" value="0" min="0">
                <select class="form-select discount-type">
                    <option value="rp" selected>Rp</option>
                    <option value="percent">%</option>
                </select>
            </div>
            <p class="text-muted">Discount Applied: Rp <span class="discount-amount-display">0</span></p>
            <input type="hidden" class="discount-hidden" name="discount_final[{{ $item['procedure']->id }}]" value="0">
        </div>
        @endforeach

        <div class="card mt-3 bg-primary text-white p-2 w-50 mx-auto">
            <h5 class="text-center mb-0">Total Amount: Rp <span id="total-amount-display">0</span></h5>
        </div>
        <input type="hidden" id="total_amount" name="total_amount" value="0">

        <h5>Payments:</h5>
        <div id="payments-container">
            <div class="payment-item mb-3">
                <label>Payment Amount:</label>
                <input type="number" class="form-control" id="payment" name="payments[0][amount]" min="0" value="0" required>
            </div>

            <div class="form-group">
                <label for="coa_id">Menerima Ke (Akun Kas/Bank)</label>
                <select class="form-control" id="coa_id" name="coa_id" required>
                    <option value="">-- Pilih Akun Kas/Bank --</option>
                    @foreach ($cashAccounts as $account)
                    <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                    @endforeach
                </select>
            </div>

            <label>Notes:</label>
            <input type="string" class="form-control" name="payments[0][notes]">
        </div>

        <div class="card mt-3 bg-warning text-dark p-2 w-50 mx-auto">
            <h5 class="text-center mb-0">Remaining Amount: Rp <span id="remaining-amount-display">0</span></h5>
        </div>
        <input type="hidden" id="remaining_amount" name="remaining_amount" value="0">

        <button type="submit" class="btn btn-success">Create Transaction</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const amountInputs = document.querySelectorAll('.amount-input');
        const discountInputs = document.querySelectorAll('.discount-input');
        const discountTypes = document.querySelectorAll('.discount-type');
        const discountDisplays = document.querySelectorAll('.discount-amount-display');
        const discountHiddenInputs = document.querySelectorAll('.discount-hidden');
        const totalAmountField = document.getElementById('total_amount');
        const totalAmountDisplay = document.getElementById('total-amount-display');
        const paymentInput = document.getElementById('payment');
        const remainingAmountField = document.getElementById('remaining_amount');
        const remainingAmountDisplay = document.getElementById('remaining-amount-display');

        function calculateTotal() {
            let total = 0;
            amountInputs.forEach((input, index) => {
                if (input.checked) {
                    const quantity = parseInt(input.closest('.mb-3').querySelector('.quantity-input').value) || 1;
                    let unitPrice = parseFloat(input.value);
                    let discountInput = discountInputs[index];
                    let discountType = discountTypes[index].value;
                    let discount = parseFloat(discountInput.value) || 0;
                    let finalDiscount = discountType === 'percent' ? ((unitPrice * quantity) * (discount / 100)) : discount;
                    discountDisplays[index].textContent = finalDiscount.toLocaleString();
                    discountHiddenInputs[index].value = finalDiscount;
                    total += Math.max((unitPrice * quantity) - finalDiscount, 0);
                }
            });
            totalAmountField.value = total;
            totalAmountDisplay.textContent = total.toLocaleString();
            calculateRemainingPayment();
        }

        function calculateRemainingPayment() {
            let total = parseFloat(totalAmountField.value);
            let payment = parseFloat(paymentInput.value) || 0;
            if (payment > total) {
                paymentInput.value = total;
            }
            let remaining = Math.max(total - payment, 0);
            remainingAmountField.value = remaining;
            remainingAmountDisplay.textContent = remaining.toLocaleString();
        }

        amountInputs.forEach(input => input.addEventListener('change', calculateTotal));
        discountInputs.forEach(input => input.addEventListener('input', calculateTotal));
        discountTypes.forEach(select => select.addEventListener('change', calculateTotal));
        paymentInput.addEventListener('input', calculateRemainingPayment);

        calculateTotal();
    });
</script>
@endsection
