@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
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
            <ul>
                @foreach($proceduresWithPrices as $item)
                    <li>
                        {{ $item['procedure']->name }}
                        <br>
                        <strong>Base Price:</strong> ${{ number_format($item['basePrice'], 2) }}
                        @if($item['promoPrice'])
                            <br>
                            <strong>Promo Price:</strong> ${{ number_format($item['promoPrice'], 2) }}
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="mb-3">
    <label for="total_amount" class="form-label">Total Amount</label>
    <input type="text" class="form-control" id="total_amount" name="total_amount" value="{{ number_format($totalAmount, 2) }}" readonly>
</div>


    <form action="{{ route('dashboard.transactions.store') }}" method="POST">
        @csrf
        <input type="hidden" name="medical_record_id" value="{{ $medicalRecord->id }}">

        <div class="mb-3">
    <label for="amount" class="form-label">Amount</label>
    <div>
        @foreach($proceduresWithPrices as $index => $item)
            <div class="form-check">
                <input class="form-check-input" type="radio" name="amount[{{ $index }}]" value="{{ $item['basePrice'] }}" checked>
                <label class="form-check-label">
                    {{ $item['procedure']->name }} - Base Price: ${{ number_format($item['basePrice'], 2) }}
                </label>
            </div>
            @if($item['promoPrice'])
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="amount[{{ $index }}]" value="{{ $item['promoPrice'] }}">
                    <label class="form-check-label">
                        {{ $item['procedure']->name }} - Promo Price: ${{ number_format($item['promoPrice'], 2) }}
                    </label>
                </div>
            @endif
        @endforeach
    </div>
</div>


        <div class="mb-3">
            <label for="payment_type" class="form-label">Payment Type</label>
            <select class="form-control" id="payment_type" name="payment_type" required>
                <option value="cash">Cash</option>
                <option value="credit">Credit</option>
                <option value="dp">DP</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="payment_status" class="form-label">Payment Status</label>
            <select class="form-control" id="payment_status" name="payment_status" required>
                <option value="lunas">Lunas</option>
                <option value="cicilan">Cicilan</option>
                <option value="dp">DP</option>
            </select>
        </div>

        <form action="{{ route('dashboard.transactions.store') }}" method="POST">
    @csrf
    <!-- Isi form lainnya -->
    <button type="submit" class="btn btn-success">Create Transaction</button>
</form>

    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const amountInputs = document.querySelectorAll('input[name^="amount"]');
        const totalAmountField = document.getElementById('total_amount');

        // Fungsi untuk menghitung total amount
        function calculateTotal() {
            let total = 0;
            amountInputs.forEach(input => {
                if (input.checked) {
                    total += parseFloat(input.value || 0);
                }
            });
            totalAmountField.value = total.toFixed(2); // Format menjadi 2 desimal
        }

        // Event listener untuk setiap input amount
        amountInputs.forEach(input => {
            input.addEventListener('change', calculateTotal);
        });

        // Hitung total saat halaman dimuat
        calculateTotal();
    });
</script>

@endsection
