@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5 col-md-6">
    <h1>Create Expense</h1>
    @if(isset($expense))
    <div class="alert alert-info">
        You are duplicating expense #{{ $expense->id }}. Feel free to adjust the values below.
    </div>
    @endif

    <form id="createExpenseForm" action="{{ route('dashboard.expenses.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="expense_date" class="form-label">Date</label>
            <input type="date" name="expense_date" class="form-control"
    value="{{ old('expense_date', isset($expense) ? \Carbon\Carbon::parse($expense->expense_date)->toDateString() : now()->toDateString()) }}"
    required>

        </div>

        <div class="mb-3">
            <label for="amount" class="form-label">Amount</label>
            <input type="number" step="0.01" name="amount" class="form-control amount" value="{{ old('amount', $expense->amount ?? '') }}" required>
        </div>

        <div class="mb-3">
            <label for="supplier_id" class="form-label">Supplier</label>
            <select name="supplier_id" class="form-control" required>
                <option value="">-- Select Supplier --</option>
                @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->id }}"
                    {{ old('supplier_id', $expense->supplier_id ?? '') == $supplier->id ? 'selected' : '' }}>
                    {{ $supplier->nama }}
                </option>
                @endforeach
            </select>
        </div>


        <div class="mb-3">
            <label for="payment_method" class="form-label">Cara Bayar</label>
            <select class="form-control" id="payment_method" name="payment_method" required>
                <option value="">-- Pilih Metode Pembayaran --</option>

                <optgroup label="Tunai">
                    <option value="tunai" {{ old('payment_method', $expense->payment_method ?? '') == 'tunai' ? 'selected' : '' }}>
                        Tunai
                    </option>
                </optgroup>



                <!-- QRIS -->
                <optgroup label="QRIS">
                    @foreach(['QRIS BCA', 'QRIS CIMB Niaga', 'QRIS Mandiri', 'QRIS BRI', 'QRIS BNI', 'QRIS Permata', 'QRIS Maybank', 'QRIS Danamon', 'QRIS Bank Mega'] as $method)
                    <option value="{{ $method }}" {{ old('payment_method', $expense->payment_method ?? '') == $method ? 'selected' : '' }}>
                        {{ $method }}
                    </option>
                    @endforeach
                </optgroup>

                <!-- Kartu Kredit/Debit -->
                <optgroup label="Kartu Kredit/Debit">
                    @foreach(['Visa', 'Mastercard', 'JCB', 'American Express', 'GPN', 'Kartu Kredit BCA', 'Kartu Kredit Mandiri', 'Kartu Kredit BRI', 'Kartu Kredit BNI', 'Kartu Kredit CIMB Niaga'] as $method)
                    <option value="{{ $method }}" {{ old('payment_method', $expense->payment_method ?? '') == $method ? 'selected' : '' }}>
                        {{ $method }}
                    </option>
                    @endforeach
                </optgroup>

                <!-- Transfer Bank -->
                <optgroup label="Transfer Bank">
                    @foreach(['Transfer Bank BCA', 'Transfer Bank Mandiri', 'Transfer Bank BRI', 'Transfer Bank BNI', 'Transfer Bank CIMB Niaga', 'Transfer Bank Permata', 'Transfer Bank Maybank'] as $method)
                    <option value="{{ $method }}" {{ old('payment_method', $expense->payment_method ?? '') == $method ? 'selected' : '' }}>
                        {{ $method }}
                    </option>
                    @endforeach
                </optgroup>

                <!-- E-Wallet -->
                <optgroup label="E-Wallet">
                    @foreach(['GoPay', 'OVO', 'Dana', 'LinkAja', 'ShopeePay', 'Doku Wallet', 'PayPal'] as $method)
                    <option value="{{ $method }}" {{ old('payment_method', $expense->payment_method ?? '') == $method ? 'selected' : '' }}>
                        {{ $method }}
                    </option>
                    @endforeach
                </optgroup>
            </select>
        </div>


        <div class="mb-3">
            <label for="coa_out" class="form-label">Pay From (Cash/Bank Account)</label>
            <select name="coa_out" class="form-control" required>
                @foreach($coa as $account)
                <option value="{{ $account->id }}"
                    {{ old('coa_out', $expense->coa_out ?? '') == $account->id ? 'selected' : '' }}>
                    {{ $account->code }} - {{ $account->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="coa_in" class="form-label">Charged To (Expense Account)
            </label>
            <select name="coa_in" class="form-control" required>
                @foreach($coa as $account)
                <option value="{{ $account->id }}"
                    {{ old('coa_in', $expense->coa_in ?? '') == $account->id ? 'selected' : '' }}>
                    {{ $account->code }} - {{ $account->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description (required)</label>
            <textarea name="description" class="form-control" required>{{ old('description', $expense->description ?? '') }}</textarea>
        </div>

         <div class="mb-3">
        <label for="attachment_file" class="form-label">Bukti/Lampiran (Opsional)</label>
        <input class="form-control @error('attachment_file') is-invalid @enderror" type="file" id="attachment_file" name="attachment_file">
        @error('attachment_file')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

        <button type="submit" class="btn btn-success">Save</button>
    </form>
</div>
<script>
    document.getElementById('createExpenseForm').addEventListener('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Confirm Expense',
            text: "Are you sure you want to create this expense?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, sure!'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
</script>
@endsection