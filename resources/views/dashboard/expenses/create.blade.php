@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5 col-md-6">
    <h1>Add Expense</h1>

    <!-- Form Filter Kategori -->
    <form action="{{ route('dashboard.expenses.create') }}" method="GET">
        <div class="mb-3">
            <label for="category_id" class="form-label">Category</label>
            <select name="category_id" id="category_id" class="form-control" required>
                <option value="">-- Select Category --</option>
                @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <!-- Form Tambah Expense -->
    @if(request('category_id'))
    <form action="{{ route('dashboard.expenses.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" name="date" class="form-control" value="{{ old('date') }}" required>
        </div>

        <div class="mb-3">
            <label for="amount" class="form-label">Amount</label>
            <input type="number" step="0.01" name="amount" class="form-control amount" value="{{ old('amount') }}" required>
        </div>

        <input type="hidden" name="category_id" value="{{ request('category_id') }}">

        <!-- Jika kategori adalah Bahan Baku -->
        @if(isset($dentalMaterials) && $categories->find(request('category_id'))->name === 'Bahan Baku')
        <div class="mb-3">
            <label for="dental_material_id" class="form-label">Dental Material</label>
            <select name="dental_material_id" class="form-control" required>
                <option value="">-- Select Material --</option>
                @foreach ($dentalMaterials as $material)
                <option value="{{ $material->id }}" {{ old('dental_material_id') == $material->id ? 'selected' : '' }}>
                    {{ $material->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="supplier_id" class="form-label">Supplier</label>
            <select name="supplier_id" class="form-control">
                <option value="">-- Select Supplier --</option>
                @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                    {{ $supplier->nama }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" name="quantity" class="form-control" value="{{ old('quantity') }}" required>
        </div>

        <!-- Input untuk Tanggal Kadaluarsa -->
        <div class="mb-3">
            <label for="expired_at" class="form-label">Expiration Date</label>
            <input type="date" name="expired_at" class="form-control" value="{{ old('expired_at') }}">
        </div>
        @endif

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" class="form-control">{{ old('description') }}</textarea>
        </div>

        <h5>Purchases:</h5>
        <div class="purchases-container">
            <div class="purchase-item mb-3">
                <label>Purchase Amount:</label>
                <input type="number" class="form-control purchase" id="purchase" name="purchase" min="0" value="0" required>
            </div>
            <div class="form-group">
                <label for="coa_id">Bayar Dari (Akun Kas/Bank)</label>
                <select class="form-control" id="coa_id" name="coa_id" required>
                    <option value="">-- Pilih Akun Kas/Bank --</option>
                    @foreach ($coa as $account)
                    <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                    @endforeach
                </select>
            </div>
            <label>Notes:</label>
            <textarea class="form-control" name="notes"></textarea>

            <div class="card mt-3 bg-warning text-dark p-2 w-50 mx-auto">
                <h5 class="text-center mb-0">Sisa Tagihan: Rp <span id="remaining-debt">0</span></h5>
            </div>
            <input type="hidden" id="total-debt" name="total_debt" value="0">
        </div>

        <button type="submit" class="btn btn-success">Save</button>
    </form>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('masuk');
        const amountInput = document.querySelector('.amount');
        const purchaseInput = document.querySelector('.purchase');
        const remainingDebtDisplay = document.getElementById('remaining-debt');
        const totalDebtInput = document.getElementById('total-debt');

        function formatNumber(value) {
            // Jika angka adalah bilangan bulat, tampilkan tanpa .00
            return value % 1 === 0 ? value : value.toFixed(2);
        }

        function calculateDebt() {
            const amount = parseFloat(amountInput.value) || 0;
            const purchase = parseFloat(purchaseInput.value) || 0;
            const remainingDebt = amount - purchase;

            // Update display dan hidden input tanpa .00
            remainingDebtDisplay.textContent = formatNumber(remainingDebt);
            totalDebtInput.value = remainingDebt;
        }

        // Event Listener untuk perhitungan otomatis
        amountInput.addEventListener('input', calculateDebt);
        purchaseInput.addEventListener('input', calculateDebt);

        // Panggil fungsi saat pertama kali halaman dimuat
        calculateDebt();
    });
</script>

@endsection