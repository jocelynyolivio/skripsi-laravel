@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5 col-md-6">
    <h3 class="text-center">Create Transaction for Medical Record ID: {{ $medicalRecord->id }}</h3>

    <div class="card mb-3" style="border: 1px solid;">
        <div class="card-header">
            <strong>Medical Record Details</strong>
        </div>
        <div class="card-body">
            <p><strong>Patient:</strong> {{ $medicalRecord->reservation->patient->name }}</p>
            <p><strong>Doctor:</strong> {{ $medicalRecord->reservation->doctor->name }}</p>
            <p><strong>Reservation Date:</strong> {{ $medicalRecord->reservation->tanggal_reservasi }}</p>
            <p><strong>Procedures:</strong></p>
            <ul class="list-group">
                @foreach($proceduresWithPrices as $item)
                <li class="list-group-item">
                    <strong>{{ $item['procedure']->name }}</strong> (x{{ $item['quantity'] }})<br>
                    <!-- <strong>Base Price:</strong> Rp {{ number_format($item['basePrice'], 0, ',', '.') }} <br> -->
                    <!-- <strong>Total Price:</strong> Rp {{ number_format($item['basePrice'] * $item['quantity'], 0, ',', '.') }} -->
                    <!-- @if($item['promoPrice'])
                            <br>
                            <strong>Promo Price:</strong> Rp {{ number_format($item['promoPrice'], 0, ',', '.') }} <br>
                            <strong>Total Promo Price:</strong> Rp {{ number_format($item['promoPrice'] * $item['quantity'], 0, ',', '.') }}
                        @endif -->
                </li>
                @endforeach
            </ul>
        </div>
    </div>

    <!-- Formulir Transaksi -->
    <form action="{{ route('dashboard.transactions.store') }}" method="POST">
        @csrf
        <input type="hidden" name="medical_record_id" value="{{ $medicalRecord->id }}">
        <input type="hidden" name="user_id" value="{{ auth()->id() }}">
        <input type="hidden" name="admin_id" value="{{ auth()->id() }}">

        @foreach($proceduresWithPrices as $index => $item)
        <input type="hidden" name="procedure_ids[]" value="{{ $item['procedure']->id }}">

        <div class="mb-3">
            <label><strong>{{ $item['procedure']->name }}</strong> (x{{ $item['quantity'] }})</label>

            <div class="form-check">
                <input class="form-check-input amount-input" type="radio"
                    name="amount[{{ $item['procedure']->id }}]"
                    value="{{ $item['basePrice'] }}"
                    data-quantity="{{ $item['quantity'] }}"
                    checked>
                <label class="form-check-label">
                    Base Price: Rp {{ number_format($item['basePrice'], 0, ',', '.') }}
                </label>
            </div>

            @if($item['promoPrice'])
            <div class="form-check">
                <input class="form-check-input amount-input" type="radio"
                    name="amount[{{ $item['procedure']->id }}]"
                    value="{{ $item['promoPrice'] }}"
                    data-quantity="{{ $item['quantity'] }}">
                <label class="form-check-label">
                    Promo Price: Rp {{ number_format($item['promoPrice'], 0, ',', '.') }}
                </label>
            </div>
            @endif

            <!-- Input Diskon -->
            <label for="discount[{{ $item['procedure']->id }}]">Discount (Rp):</label>
            <input type="number" name="discount[{{ $item['procedure']->id }}]" class="form-control discount-input" value="0" min="0">
        </div>
        @endforeach


        <!-- Total Amount -->
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

            <div class="form-group">
    <label for="coa_id">Bayar Dari (Akun Kas/Bank)</label>
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

        <!-- Sisa Tagihan -->
        <div class="card mt-3 bg-warning text-dark p-2 w-50 mx-auto">
            <h5 class="text-center mb-0">Sisa Tagihan: Rp <span id="remaining-amount-display">0</span></h5>
        </div>
        <input type="hidden" id="remaining_amount" name="remaining_amount" value="0">
</div>

<button type="submit" class="btn btn-success">Create Transaction</button>
</form>
</div>

<!-- Script Perhitungan Total Harga -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log("Script Loaded: Form transaction with medical record is ready!");

        // Deklarasi Variabel untuk Element DOM
        const amountInputs = document.querySelectorAll('.amount-input');
        const discountInputs = document.querySelectorAll('.discount-input');
        const totalAmountField = document.getElementById('total_amount');
        const totalAmountDisplay = document.getElementById('total-amount-display');

        const remainingPaymentField = document.getElementById('remaining_amount');
        const remainingPaymentDisplay = document.getElementById('remaining-amount-display');

        const paymentInput = document.getElementById('payment');

        console.log("DOM Elements:", {
            amountInputs,
            discountInputs,
            totalAmountField,
            totalAmountDisplay,
            remainingPaymentField,
            remainingPaymentDisplay,
            paymentInput
        });

        // Fungsi untuk Menghitung Total Harga
        function calculateTotal() {
            console.log("Calculating Total...");

            let total = 0;

            amountInputs.forEach(input => {
                if (input.checked) {
                    const procedureId = input.name.match(/\d+/)[0]; // Ambil ID prosedur
                    const unitPrice = parseFloat(input.value) || 0;
                    const discountInput = document.querySelector(`input[name="discount[${procedureId}]"]`);
                    const quantity = parseInt(input.dataset.quantity) || 1; // Ambil quantity dari data-quantity

                    const discount = parseFloat(discountInput?.value) || 0;
                    const finalPrice = Math.max((unitPrice * quantity) - discount, 0);
                    total += finalPrice;

                    // Console Log untuk Debugging
                    console.log(`Procedure ID: ${procedureId}`);
                    console.log(`Unit Price: Rp ${unitPrice}`);
                    console.log(`Quantity: ${quantity}`);
                    console.log(`Discount: Rp ${discount}`);
                    console.log(`Final Price: Rp ${finalPrice}`);
                }
            });

            console.log(`Total Amount: Rp ${total}`);

            totalAmountField.value = total.toFixed(0);
            totalAmountDisplay.textContent = total.toLocaleString();

            calculateRemainingPayment(total);
        }

        // Fungsi untuk Menghitung Sisa Tagihan
        function calculateRemainingPayment(total) {
            console.log("Calculating Remaining Payment...");

            const payment = parseFloat(paymentInput.value) || 0;
            const remaining = Math.max(total - payment, 0);

            remainingPaymentField.value = remaining.toFixed(0);
            remainingPaymentDisplay.textContent = remaining.toLocaleString();

            console.log(`Payment Made: Rp ${payment}`);
            console.log(`Remaining Payment: Rp ${remaining}`);
        }

        // Event Listener untuk Perubahan Harga
        amountInputs.forEach(input => {
            input.addEventListener('change', () => {
                console.log(`Price Changed: ${input.value}`);
                calculateTotal();
            });
        });

        // Event Listener untuk Perubahan Diskon
        discountInputs.forEach(input => {
            input.addEventListener('input', () => {
                console.log(`Discount Changed: ${input.value}`);
                calculateTotal();
            });
        });

        // Event Listener untuk Perubahan Pembayaran
        paymentInput.addEventListener('input', () => {
            console.log(`Payment Input Changed: ${paymentInput.value}`);
            const total = parseFloat(totalAmountField.value) || 0;
            calculateRemainingPayment(total);
        });

        // Hitung total awal saat halaman dimuat
        calculateTotal();
    });
</script>



@endsection