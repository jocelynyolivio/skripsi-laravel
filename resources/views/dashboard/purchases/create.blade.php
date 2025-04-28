@extends('dashboard.layouts.main')
@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['text' => 'Purchases Invoices', 'url' => route('dashboard.purchases.index')],
            ['text' => 'Create Purchase Invoice']
        ]
    ])
@endsection
@section('container')
<div class="container mt-5 col-md-8">
    <h3 class="text-center">
        @isset($purchaseRequest)
        Create Purchase Invoice from Request #{{ $purchaseRequest->id }}
        @else
        Create Purchase Invoice
        @endisset
    </h3>

    <form action="{{ isset($purchaseRequest) ? route('dashboard.purchases.storeFromRequest', $purchaseRequest->id) : route('dashboard.purchases.store') }}" method="POST">
        @csrf

        <!-- Purchase Invoice Details -->
        <div class="mb-3">
            <label class="form-label">Supplier:</label>
            <select name="supplier_id" class="form-control" required>
                <option value="" disabled {{ !isset($purchaseRequest) ? 'selected' : '' }}>Choose Supplier</option>
                @foreach($suppliers as $supplier)
                <option value="{{ $supplier->id }}"
                    @isset($purchaseRequest)
                    {{ $supplier->id == $purchaseRequest->supplier_id ? 'selected' : '' }}
                    @endisset>
                    {{ $supplier->nama }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Purchase Date:</label>
            <input type="date" class="form-control" name="purchase_date" required
                value="{{ old('purchase_date', now()->format('Y-m-d')) }}">
        </div>

        <!-- TABEL DENTAL MATERIAL -->
        <h5>Dental Materials</h5>
        <table class="table table-bordered" id="materialsTable">
            <thead>
                <tr>
                    <th>Material</th>
                    <th>Quantity</th>
                    <th>Price (Total Purchase Price)</th>
                    <th>Unit Price (Auto Calculate)</th>
                    <th><button type="button" class="btn btn-sm btn-success" id="addRow">+</button></th>
                </tr>
            </thead>
            <tbody>
                @isset($purchaseRequest)
                @foreach($purchaseRequest->details as $detail)
                <tr>
                    <td>
                        <select name="dental_material_id[]" class="form-control material-select" required>
                            @foreach($materials as $material)
                            <option value="{{ $material->id }}" data-unit="{{ $material->unit_type }}"
                                {{ $material->id == $detail->dental_material_id ? 'selected' : '' }}>
                                {{ $material->name }} ({{ $material->unit_type }})
                            </option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" name="quantity[]" class="form-control quantity" value="{{ $detail->quantity }}" required></td>
                    <td><input type="number" name="total_price[]" class="form-control total_price" required></td>
                    <td><input type="number" name="unit_price[]" class="form-control unit_price" readonly></td>
                    <td><button type="button" class="btn btn-danger btn-sm removeRow">-</button></td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td>
                        <select name="dental_material_id[]" class="form-control material-select" required>
                            @foreach($materials as $material)
                            <option value="{{ $material->id }}" data-unit="{{ $material->unit_type }}">
                                {{ $material->name }} ({{ $material->unit_type }})
                            </option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" name="quantity[]" class="form-control quantity" required min="1"></td>
                    <td><input type="number" name="total_price[]" class="form-control total_price" required></td>
                    <td><input type="number" name="unit_price[]" class="form-control unit_price" readonly></td>
                    <td><button type="button" class="btn btn-danger btn-sm removeRow">-</button></td>
                </tr>
                @endisset
            </tbody>
        </table>

        

        <div class="card mt-3 bg-primary text-white p-2 w-50 mx-auto">
            <h5 class="text-center mb-0">Total Amount: Rp <span id="total-amount-display">0</span></h5>
        </div>
        <input type="hidden" id="total_amount" name="total_amount" value="0">
        <div class="mb-3">
            <label class="form-label">Discount (Rp):</label>
            <input type="number" class="form-control" name="discount" id="discount" value="0" min="0">
        </div>

        <div class="mb-3">
            <label class="form-label">Ongkos Kirim (Rp):</label>
            <input type="number" class="form-control" name="ongkos_kirim" id="ongkosKirim" value="0" min="0">
        </div>
        <div class="card mt-3 bg-success text-white p-2 w-50 mx-auto">
    <h5 class="text-center mb-0">Grand Total: Rp <span id="grand-total-display">0</span></h5>
</div>
<input type="hidden" id="grand_total" name="grand_total" value="0">

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

        <!-- Sisa Tagihan : jadi HUTANG-->
        <div class="card mt-3 bg-warning text-dark p-2 w-50 mx-auto">
            <h5 class="text-center mb-0">Sisa Tagihan: Rp <span id="remaining-amount-display">0</span></h5>
        </div>
        <input type="hidden" id="remaining_amount" name="remaining_amount" value="0">
        <br>
        <button type="submit" class="btn btn-success w-100 d-block mx-auto">
            Create Purchase Invoice
        </button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Event listener untuk menghitung unit price, total harga, dan sisa tagihan
        document.addEventListener('input', function(event) {
            if (event.target.classList.contains('quantity') || event.target.classList.contains('total_price')) {
                calculateUnitPrice(event.target);
                calculateTotalAmount();
            }

            if (event.target.id === 'paymentAmount') {
                calculateTotalAmount();
            }
        });

        // Fungsi menghitung unit price otomatis (Total Price / Quantity)
        function calculateUnitPrice(inputElement) {
            let row = inputElement.closest('tr');
            let qty = parseFloat(row.querySelector('.quantity').value) || 0;
            let totalPrice = parseFloat(row.querySelector('.total_price').value) || 0;
            let unitPriceField = row.querySelector('.unit_price');

            if (qty > 0 && totalPrice > 0) {
                unitPriceField.value = (totalPrice / qty).toFixed(2);
            } else {
                unitPriceField.value = '';
            }
        }

        // Fungsi untuk menghitung total amount dan sisa tagihan
        function calculateTotalAmount() {
    let total = 0;
    document.querySelectorAll('.total_price').forEach(input => {
        total += parseFloat(input.value) || 0;
    });

    document.getElementById('total-amount-display').textContent = total.toLocaleString('id-ID', { minimumFractionDigits: 2 });
    document.getElementById('total_amount').value = total.toFixed(2);

    // Ambil nilai discount dan ongkos kirim
    let discount = parseFloat(document.getElementById('discount')?.value) || 0;
    let ongkir = parseFloat(document.getElementById('ongkosKirim')?.value) || 0;

    // Hitung grand total
    let grandTotal = total - discount + ongkir;
    document.getElementById('grand-total-display').textContent = grandTotal.toLocaleString('id-ID', { minimumFractionDigits: 2 });
    document.getElementById('grand_total').value = grandTotal.toFixed(2);

    // Hitung sisa tagihan dari grand total
    let paymentAmount = parseFloat(document.getElementById('paymentAmount')?.value) || 0;
    let remainingAmount = grandTotal - paymentAmount;

    document.getElementById('remaining-amount-display').textContent = remainingAmount.toLocaleString('id-ID', { minimumFractionDigits: 2 });
    document.getElementById('remaining_amount').value = remainingAmount.toFixed(2);
}
// Tambahkan event listener untuk discount & ongkos kirim
['discount', 'ongkosKirim'].forEach(id => {
        let el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', calculateTotalAmount);
        }
    });

     // Sudah ada juga event listener untuk total_price & paymentAmount? Tambahkan jika belum:
     document.querySelectorAll('.total_price').forEach(input => {
        input.addEventListener('input', calculateTotalAmount);
    });

        // Fungsi untuk menambahkan baris material baru
        document.getElementById('addRow').addEventListener('click', function() {
            let tableBody = document.querySelector('#materialsTable tbody');
            let newRow = tableBody.querySelector('tr').cloneNode(true);

            // Reset semua input dalam row baru
            newRow.querySelectorAll('input').forEach(input => input.value = '');

            let materialSelect = newRow.querySelector('.material-select');
            let unitField = newRow.querySelector('.unit');

            // Menyesuaikan unit saat material dipilih
            materialSelect.addEventListener('change', function() {
                let selectedOption = this.options[this.selectedIndex];
                if (unitField) {
                    unitField.value = selectedOption.dataset.unit;
                }
            });

            tableBody.appendChild(newRow);
        });

        // Fungsi untuk menghapus baris material
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('removeRow')) {
                event.target.closest('tr').remove();
                calculateTotalAmount();
            }
        });

        // Hitung total amount saat pertama kali load
        calculateTotalAmount();
    });
</script>

@endsection