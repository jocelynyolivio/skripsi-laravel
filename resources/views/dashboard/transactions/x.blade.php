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
            <label for="user_id" class="form-label">User</label>
            <select class="form-control" id="user_id" name="user_id" required>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="total_amount" class="form-label">Total Amount</label>
            <input type="text" class="form-control" id="total_amount" name="total_amount" value="0" readonly>
        </div>

        <div class="mb-3">
            <label for="items" class="form-label">Select Procedures or Items</label>
            <select class="form-control" id="procedure_id" name="items[][id]">
                <option value="">-- Select Procedure --</option>
                @foreach($proceduresWithPrices as $item)
                    <option value="{{ $item['procedure']->id }}" data-price="{{ $item['basePrice'] }}">
                        {{ $item['procedure']->name }} - Base Price: Rp {{ number_format($item['basePrice'], 0, ',', '.') }}
                    </option>
                @endforeach
            </select>
            <div id="selected-items"></div>
        </div>

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
        const selectedItemsContainer = document.getElementById('selected-items');
        const totalAmountField = document.getElementById('total_amount');
        let totalAmount = 0;

        procedureSelect.addEventListener('change', function () {
            const selectedOption = procedureSelect.options[procedureSelect.selectedIndex];
            const procedureId = selectedOption.value;
            const procedureName = selectedOption.text;
            const unitPrice = parseFloat(selectedOption.dataset.price);

            if (procedureId && !document.getElementById(`item-${procedureId}`)) {
                const itemDiv = document.createElement('div');
                itemDiv.classList.add('mb-2');
                itemDiv.id = `item-${procedureId}`;
                itemDiv.innerHTML = `
                    <input type="hidden" name="items[][id]" value="${procedureId}">
                    <input type="hidden" name="items[][unit_price]" value="${unitPrice}">
                    <span>${procedureName}</span>
                    <input type="number" name="items[][quantity]" class="form-control d-inline w-auto" value="1" min="1" onchange="updateTotal()">
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeItem('${procedureId}', ${unitPrice})">Remove</button>
                `;
                selectedItemsContainer.appendChild(itemDiv);
                totalAmount += unitPrice;
                updateTotal();
            }
        });

        function removeItem(procedureId, unitPrice) {
            const itemDiv = document.getElementById(`item-${procedureId}`);
            if (itemDiv) {
                totalAmount -= unitPrice;
                itemDiv.remove();
                updateTotal();
            }
        }

        function updateTotal() {
            let newTotal = 0;
            document.querySelectorAll('input[name="items[][quantity]"]').forEach(input => {
                const quantity = parseInt(input.value);
                const unitPrice = parseFloat(input.previousElementSibling.value);
                newTotal += unitPrice * quantity;
            });
            totalAmountField.value = newTotal.toFixed(0);
        }
    });
</script>
@endsection
