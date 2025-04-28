@extends('dashboard.layouts.main')
@section('breadcrumbs')
@include('dashboard.layouts.breadcrumbs', [
'customBreadcrumbs' => [
['text' => 'Purchases Payment']
]])
@endsection
@section('container')
<div class="container">
    <h1>Create Payment for Invoice #{{ $invoice->invoice_number }}</h1>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('dashboard.purchase_payments.store') }}" method="POST">
        @csrf
        <input type="hidden" name="purchase_invoice_id" value="{{ $invoice->id }}">

        <div class="form-group mb-3">
            <label>Total Debt: </label>
            <input type="text" class="form-control" value="Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}" readonly>
        </div>

        <div class="mb-3">
            <label for="payment_date" class="form-label">Payment Date</label>
            <input type="date" class="form-control" id="payment_date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required>
        </div>

        <div class="form-group mb-3">
            <label>Payment Amount</label>
            <input type="number" name="purchase_amount" class="form-control" required>
</div>

        <div class="form-group mb-3">
            <label>Payment Account (COA)</label>
            <select name="coa_id" class="form-control" required>
                <option value="">-- Select Account --</option>
                @foreach($coas as $coa)
                    <option value="{{ $coa->id }}">{{ $coa->code }} - {{ $coa->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="payment_method" class="form-label">Cara Bayar</label>
            <select class="form-control" id="payment_method" name="payment_method" required>
                <option value="">-- Pilih Metode Pembayaran --</option>

                <!-- QRIS -->
                <optgroup label="QRIS">
                    @foreach(['QRIS BCA', 'QRIS CIMB Niaga', 'QRIS Mandiri', 'QRIS BRI', 'QRIS BNI', 'QRIS Permata', 'QRIS Maybank', 'QRIS Danamon', 'QRIS Bank Mega'] as $method)
                    <option value="{{ $method }}" {{ old('payment_method', $invoice->payment_method) == $method ? 'selected' : '' }}>{{ $method }}</option>
                    @endforeach
                </optgroup>

                <!-- Kartu Kredit/Debit -->
                <optgroup label="Kartu Kredit/Debit">
                    @foreach(['Visa', 'Mastercard', 'JCB', 'American Express', 'GPN', 'Kartu Kredit BCA', 'Kartu Kredit Mandiri', 'Kartu Kredit BRI', 'Kartu Kredit BNI', 'Kartu Kredit CIMB Niaga'] as $method)
                    <option value="{{ $method }}" {{ old('payment_method', $invoice->payment_method) == $method ? 'selected' : '' }}>{{ $method }}</option>
                    @endforeach
                </optgroup>

                <!-- Transfer Bank -->
                <optgroup label="Transfer Bank">
                    @foreach(['Transfer Bank BCA', 'Transfer Bank Mandiri', 'Transfer Bank BRI', 'Transfer Bank BNI', 'Transfer Bank CIMB Niaga', 'Transfer Bank Permata', 'Transfer Bank Maybank'] as $method)
                    <option value="{{ $method }}" {{ old('payment_method', $invoice->payment_method) == $method ? 'selected' : '' }}>{{ $method }}</option>
                    @endforeach
                </optgroup>

                <!-- E-Wallet -->
                <optgroup label="E-Wallet">
                    @foreach(['GoPay', 'OVO', 'Dana', 'LinkAja', 'ShopeePay', 'Doku Wallet', 'PayPal'] as $method)
                    <option value="{{ $method }}" {{ old('payment_method', $invoice->payment_method) == $method ? 'selected' : '' }}>{{ $method }}</option>
                    @endforeach
                </optgroup>
            </select>
        </div>

        <div class="form-group mb-3">
            <label>Notes</label>
            <textarea name="notes" class="form-control" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Save Payment</button>
        <a href="{{ route('dashboard.purchases.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
