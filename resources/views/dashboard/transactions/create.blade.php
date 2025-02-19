@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5 col-md-6">
    <h3 class="text-center">Create Transaction for Medical Record ID: {{ $medicalRecord->id }}</h3>

    <div class="card mb-3">
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
                        <strong>Base Price:</strong> Rp {{ number_format($item['basePrice'], 0, ',', '.') }} <br>
                        <strong>Total Price:</strong> Rp {{ number_format($item['basePrice'] * $item['quantity'], 0, ',', '.') }}
                        @if($item['promoPrice'])
                            <br>
                            <strong>Promo Price:</strong> Rp {{ number_format($item['promoPrice'], 0, ',', '.') }} <br>
                            <strong>Total Promo Price:</strong> Rp {{ number_format($item['promoPrice'] * $item['quantity'], 0, ',', '.') }}
                        @endif
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
                <label><strong>{{ $item['procedure']->name }}</strong></label>
                <div class="form-check">
                    <input class="form-check-input amount-input" type="radio" name="amount[{{ $item['procedure']->id }}]" value="{{ $item['basePrice'] }}" checked>
                    <label class="form-check-label">
                        Base Price: Rp {{ number_format($item['basePrice'], 0, ',', '.') }}
                    </label>
                </div>

                @if($item['promoPrice'])
                    <div class="form-check">
                        <input class="form-check-input amount-input" type="radio" name="amount[{{ $item['procedure']->id }}]" value="{{ $item['promoPrice'] }}">
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

        <button type="submit" class="btn btn-success">Create Transaction</button>
    </form>
</div>

<!-- Script Perhitungan Total Harga -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        console.log("Script Loaded: Form transaction with medical record is ready!");

        const amountInputs = document.querySelectorAll('.amount-input');
        const discountInputs = document.querySelectorAll('.discount-input');
        const totalAmountField = document.getElementById('total_amount');
        const totalAmountDisplay = document.getElementById('total-amount-display');

        function calculateTotal() {
            let total = 0;

            amountInputs.forEach(input => {
                if (input.checked) {
                    const procedureId = input.name.match(/\d+/)[0]; // Ambil ID prosedur
                    const unitPrice = parseFloat(input.value) || 0;
                    const discountInput = document.querySelector(`input[name="discount[${procedureId}]"]`);
                    const discount = parseFloat(discountInput.value) || 0;
                    const finalPrice = Math.max(unitPrice - discount, 0);

                    total += finalPrice;

                    console.log(`Procedure ID: ${procedureId}, Unit Price: Rp ${unitPrice}, Discount: Rp ${discount}, Final Price: Rp ${finalPrice}`);
                }
            });

            totalAmountField.value = total.toFixed(0);
            totalAmountDisplay.textContent = total.toLocaleString();
            console.log(`Total Amount Updated: Rp ${total}`);
        }

        amountInputs.forEach(input => {
            input.addEventListener('change', calculateTotal);
        });

        discountInputs.forEach(input => {
            input.addEventListener('input', calculateTotal);
        });

        calculateTotal();
    });
</script>
@endsection
