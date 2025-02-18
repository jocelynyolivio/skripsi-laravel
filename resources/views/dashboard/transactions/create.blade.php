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
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <div>
                <strong>{{ $item['procedure']->name }}</strong> (x{{ $item['quantity'] }}) <br>
                <strong>Base Price:</strong> Rp {{ number_format($item['basePrice'], 0, ',', '.') }} <br>
                <strong>Total Price:</strong> Rp {{ number_format($item['basePrice'] * $item['quantity'], 0, ',', '.') }}
                @if($item['promoPrice'])
                    <br>
                    <strong>Promo Price:</strong> Rp {{ number_format($item['promoPrice'], 0, ',', '.') }} <br>
                    <strong>Total Promo Price:</strong> Rp {{ number_format($item['promoPrice'] * $item['quantity'], 0, ',', '.') }}
                @endif
            </div>
        </li>
    @endforeach
</ul>

<!-- Total Amount Section -->
<!-- <div class="card mt-3 bg-primary text-white p-3">
    <h4 class="text-center mb-0">Total Amount: Rp {{ number_format($totalAmount, 0, ',', '.') }}</h4>
</div> -->



        </div>
    </div>
    <!-- <div class="mb-3">
        <label for="total_amount" class="form-label">Total Amount</label>
        <input type="text" class="form-control" id="total_amount" name="total_amount" value="{{ number_format($totalAmount, 2) }}" readonly>
    </div> -->

    <form action="{{ route('dashboard.transactions.store') }}" method="POST">
    @csrf
    <input type="hidden" name="medical_record_id" value="{{ $medicalRecord->id }}">
    <input type="hidden" name="user_id" value="{{ auth()->id() }}">
    <input type="hidden" name="admin_id" value="{{ auth()->id() }}">

    @foreach($proceduresWithPrices as $index => $item)
    <input type="hidden" name="procedure_ids[]" value="{{ $item['procedure']->id }}">

<div class="form-check">
    <input class="form-check-input" type="radio" name="amount[{{ $index }}]" value="{{ $item['basePrice'] }}" checked>
    <label class="form-check-label">
        {{ $item['procedure']->name }} - Base Price: Rp {{ number_format($item['basePrice'], 0, ',', '.') }}
    </label>
</div>

@if($item['promoPrice'])
    <div class="form-check">
        <input class="form-check-input" type="radio" name="amount[{{ $index }}]" value="{{ $item['promoPrice'] }}">
        <label class="form-check-label">
            {{ $item['procedure']->name }} - Promo Price: Rp {{ number_format($item['promoPrice'], 0, ',', '.') }}
        </label>
    </div>
@endif

    @endforeach

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

<script>
    document.addEventListener('DOMContentLoaded', function () {
    const amountInputs = document.querySelectorAll('input[name^="amount"]');
    const totalAmountField = document.getElementById('total_amount');

    function calculateTotal() {
        let total = 0;
        amountInputs.forEach(input => {
            if (input.checked) {
                total += parseFloat(input.value || 0);
            }
        });
        totalAmountField.value = total.toFixed(2);
    }

    amountInputs.forEach(input => {
        input.addEventListener('change', calculateTotal);
    });

    calculateTotal();
});

</script>

@endsection
