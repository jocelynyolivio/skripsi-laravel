@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5 col-md-8">
    <h3 class="text-center">Create Purchase Invoice</h3>
    <form action="{{ route('dashboard.purchases.store') }}" method="POST">
        @csrf

        <!-- Purchase Invoice Details -->
        <div class="mb-3">
            <label class="form-label">Supplier:</label>
            <select name="supplier_id" class="form-control" required>
                <option value="" disabled selected>Choose Supplier</option>
                @foreach($suppliers as $supplier)
                <option value="{{ $supplier->id }}">{{ $supplier->nama }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Purchase Date:</label>
            <input type="date" class="form-control" name="purchase_date" required>
        </div>

        <!-- TABEL DENTAL MATERIAL -->
        <h5>Dental Materials</h5>
        <table class="table table-bordered" id="materialsTable">
            <thead>
                <tr>
                    <th>Material</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Unit Price</th>
                    <th>Subtotal</th>
                    <th><button type="button" class="btn btn-sm btn-success" id="addRow">+</button></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <select name="dental_material_id[]" class="form-control" required>
                            @foreach($materials as $material)
                            <option value="{{ $material->id }}">{{ $material->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" name="quantity[]" class="form-control quantity" required></td>
                    <td><input type="text" name="unit[]" class="form-control" required></td>
                    <td><input type="number" name="unit_price[]" class="form-control unit_price" required></td>
                    <td><input type="number" name="subtotal[]" class="form-control subtotal" readonly></td>
                    <td><button type="button" class="btn btn-sm btn-danger removeRow">-</button></td>
                </tr>
            </tbody>
        </table>

        <div class="mb-3">
            <label class="form-label">Total Amount:</label>
            <input type="number" class="form-control" name="total_amount" id="totalAmount" readonly>
        </div>

        <!-- Purchase Payments Section -->
        <h5>Purchase Payments</h5>
        <div class="mb-3">
            <label class="form-label">Payment Amount:</label>
            <input type="number" class="form-control" name="payment_amount" id="paymentAmount" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Payment Method:</label>
            <select name="coa_id" class="form-control" required>
                <option value="" disabled selected>Choose Account</option>
                @foreach($cashAccounts as $account)
                <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Notes:</label>
            <input type="text" class="form-control" name="payment_notes">
        </div>

        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.subtotal').forEach(subtotal => {
            total += parseFloat(subtotal.value) || 0;
        });
        document.getElementById('totalAmount').value = total;
    }

    document.getElementById('addRow').addEventListener('click', function () {
        let newRow = document.querySelector('#materialsTable tbody tr').cloneNode(true);
        newRow.querySelectorAll('input').forEach(input => input.value = '');
        document.querySelector('#materialsTable tbody').appendChild(newRow);
    });

    document.addEventListener('input', function (event) {
        if (event.target.classList.contains('quantity') || event.target.classList.contains('unit_price')) {
            let row = event.target.closest('tr');
            let qty = row.querySelector('.quantity').value;
            let price = row.querySelector('.unit_price').value;
            row.querySelector('.subtotal').value = (qty * price) || 0;
            calculateTotal();
        }
    });

    document.addEventListener('click', function (event) {
        if (event.target.classList.contains('removeRow')) {
            event.target.closest('tr').remove();
            calculateTotal();
        }
    });
});
</script>
@endsection