@extends('dashboard.layouts.main')

@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['url' => route('dashboard.transactions.index'), 'text' => 'Transactions'],
            ['text' => 'Create']
        ]
    ])
@endsection

@section('container')
<div class="container mt-5 col-md-6">
    <h3 class="text-center">Create Transaction for Medical Record ID: {{ $medicalRecord->id }}</h3>

    <div class="card mb-3" style="border: 1px solid;">
        <div class="card-header">
            <strong>Medical Record Details</strong>
        </div>
        <div class="card-body">
            <p><strong>Patient:</strong> {{ $medicalRecord->patient->fname }} {{ $medicalRecord->patient->mname }} {{ $medicalRecord->patient->lname }}</p>
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
        
        <div class="form-group">
                <label for="voucher">Vouchers :</label>
                <select class="form-control" id="voucher" name="voucher">
                    <option value="">-- Pilih Voucher --</option>
                    @foreach ($vouchers as $voucher)
                    <option value="{{ $voucher->birthday_voucher_code }}">{{ $voucher->birthday_voucher_code }}</option>
                    @endforeach
                </select>
            </div>

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
                <label for="coa_id">Setor Ke (Akun Kas/Bank)</label>
                <select class="form-control" id="coa_id" name="payments[0][coa_id]" required>
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

        <div class="card mt-3 bg-warning text-dark p-2 w-50 mx-auto">
            <h5 class="text-center mb-0">Remaining Amount: Rp <span id="remaining-amount-display">0</span></h5>
        </div>
        <input type="hidden" id="remaining_amount" name="remaining_amount" value="0">

        <button type="submit" class="btn btn-success">Create Transaction</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    console.log('Script loaded');
    
    // Get all procedure items containers
    const procedureItems = document.querySelectorAll('.mb-3');
    const totalAmountField = document.getElementById('total_amount');
    const totalAmountDisplay = document.getElementById('total-amount-display');
    const paymentInput = document.getElementById('payment');
    const remainingAmountField = document.getElementById('remaining_amount');
    const remainingAmountDisplay = document.getElementById('remaining-amount-display');

    function calculateTotal() {
        let total = 0;
        
        procedureItems.forEach((item, index) => {
            // Skip if not a procedure item (there might be other mb-3 elements)
            if (!item.querySelector('.amount-input')) return;

            const quantity = parseInt(item.querySelector('.quantity-input').value) || 1;
            const selectedPriceInput = item.querySelector('.amount-input:checked');
            const discountInput = item.querySelector('.discount-input');
            const discountType = item.querySelector('.discount-type').value;
            const discountDisplay = item.querySelector('.discount-amount-display');
            const discountHidden = item.querySelector('.discount-hidden');

            if (selectedPriceInput) {
                let unitPrice = parseFloat(selectedPriceInput.value);
                let discount = parseFloat(discountInput.value) || 0;
                let finalDiscount = discountType === 'percent' 
                    ? ((unitPrice * quantity) * (discount / 100)) 
                    : discount;
                
                // Ensure discount doesn't make price negative
                finalDiscount = Math.min(finalDiscount, unitPrice * quantity);
                
                discountDisplay.textContent = finalDiscount.toLocaleString();
                discountHidden.value = finalDiscount;
                total += Math.max((unitPrice * quantity) - finalDiscount, 0);
            }
        });
        
        totalAmountField.value = total;
        totalAmountDisplay.textContent = total.toLocaleString();
        calculateRemainingPayment();
    }

    function calculateRemainingPayment() {
        let total = parseFloat(totalAmountField.value) || 0;
        let payment = parseFloat(paymentInput.value) || 0;
        
        // Prevent overpayment
        if (payment > total) {
            payment = total;
            paymentInput.value = total;
        }
        
        let remaining = Math.max(total - payment, 0);
        remainingAmountField.value = remaining;
        remainingAmountDisplay.textContent = remaining.toLocaleString();
    }

    // Add event listeners to all relevant elements
    procedureItems.forEach(item => {
        const amountInputs = item.querySelectorAll('.amount-input');
        const discountInput = item.querySelector('.discount-input');
        const discountType = item.querySelector('.discount-type');
        
        if (amountInputs) {
            amountInputs.forEach(input => {
                input.addEventListener('change', calculateTotal);
            });
        }
        
        if (discountInput) {
            discountInput.addEventListener('input', calculateTotal);
        }
        
        if (discountType) {
            discountType.addEventListener('change', calculateTotal);
        }
    });

    paymentInput.addEventListener('input', calculateRemainingPayment);

    // Initial calculation
    calculateTotal();
});
</script>
@endsection