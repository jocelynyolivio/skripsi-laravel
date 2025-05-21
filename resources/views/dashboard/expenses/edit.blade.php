@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5 col-md-6">
    <h1>Edit Expense</h1>

    <!-- Form Edit Expense -->
    <form action="{{ route('dashboard.expenses.update', $expense->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="expense_date" class="form-label">Date</label>
            <input type="date" name="expense_date" class="form-control" value="{{ old('expense_date', $expense->expense_date) }}" required>
        </div>

        <div class="mb-3">
            <label for="amount" class="form-label">Amount</label>
            <input type="number" step="0.01" name="amount" class="form-control amount" value="{{ old('amount', $expense->amount) }}" required>
        </div>

        <div class="mb-3">
            <label for="supplier_id" class="form-label">Supplier</label>
            <select name="supplier_id" class="form-control">
                <option value="">-- Select Supplier --</option>
                @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->id }}" {{ old('supplier_id', $expense->supplier_id) == $supplier->id ? 'selected' : '' }}>
                    {{ $supplier->nama }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="payment_method" class="form-label">Cara Bayar</label>
            <select class="form-control" id="payment_method" name="payment_method" required>
                <option value="">-- Pilih Metode Pembayaran --</option>

                <option value="tunai" {{ old('payment_method', $expense->payment_method ?? '') == 'tunai' ? 'selected' : '' }}>
                    Tunai
                </option>

                <!-- QRIS -->
                <optgroup label="QRIS">
                    @foreach(['QRIS BCA', 'QRIS CIMB Niaga', 'QRIS Mandiri', 'QRIS BRI', 'QRIS BNI', 'QRIS Permata', 'QRIS Maybank', 'QRIS Danamon', 'QRIS Bank Mega'] as $method)
                    <option value="{{ $method }}" {{ old('payment_method', $expense->payment_method) == $method ? 'selected' : '' }}>{{ $method }}</option>
                    @endforeach
                </optgroup>

                <!-- Kartu Kredit/Debit -->
                <optgroup label="Kartu Kredit/Debit">
                    @foreach(['Visa', 'Mastercard', 'JCB', 'American Express', 'GPN', 'Kartu Kredit BCA', 'Kartu Kredit Mandiri', 'Kartu Kredit BRI', 'Kartu Kredit BNI', 'Kartu Kredit CIMB Niaga'] as $method)
                    <option value="{{ $method }}" {{ old('payment_method', $expense->payment_method) == $method ? 'selected' : '' }}>{{ $method }}</option>
                    @endforeach
                </optgroup>

                <!-- Transfer Bank -->
                <optgroup label="Transfer Bank">
                    @foreach(['Transfer Bank BCA', 'Transfer Bank Mandiri', 'Transfer Bank BRI', 'Transfer Bank BNI', 'Transfer Bank CIMB Niaga', 'Transfer Bank Permata', 'Transfer Bank Maybank'] as $method)
                    <option value="{{ $method }}" {{ old('payment_method', $expense->payment_method) == $method ? 'selected' : '' }}>{{ $method }}</option>
                    @endforeach
                </optgroup>

                <!-- E-Wallet -->
                <optgroup label="E-Wallet">
                    @foreach(['GoPay', 'OVO', 'Dana', 'LinkAja', 'ShopeePay', 'Doku Wallet', 'PayPal'] as $method)
                    <option value="{{ $method }}" {{ old('payment_method', $expense->payment_method) == $method ? 'selected' : '' }}>{{ $method }}</option>
                    @endforeach
                </optgroup>
            </select>
        </div>

        <div class="mb-3">
            <label for="coa_out" class="form-label">Pay From (Cash/Bank Account)</label>
            <select name="coa_out" class="form-control" required>
                @foreach($coa as $account)
                <option value="{{ $account->id }}" {{ old('coa_out', $expense->coa_out) == $account->id ? 'selected' : '' }}>
                    {{ $account->code }} - {{ $account->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="coa_in" class="form-label">Charged To (Expense Account)</label>
            <select name="coa_in" class="form-control" required>
                @foreach($coa as $account)
                <option value="{{ $account->id }}" {{ old('coa_in', $expense->coa_in) == $account->id ? 'selected' : '' }}>
                    {{ $account->code }} - {{ $account->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" class="form-control" required>{{ old('description', $expense->description) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
