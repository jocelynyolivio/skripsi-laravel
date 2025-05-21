@extends('dashboard.layouts.main')

@section('breadcrumbs')
@include('dashboard.layouts.breadcrumbs', [
'customBreadcrumbs' => [
['text' => 'Purchases Payment']
]
])
@endsection

@section('container')
<div class="container mt-5 col-md-8">
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-money-bill-wave me-2"></i>
                Create Payment for Invoice #{{ $invoice->invoice_number }}
            </h5>
        </div>
        <div class="card-body">
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <form action="{{ route('dashboard.purchase_payments.store') }}" method="POST">
                @csrf
                <input type="hidden" name="purchase_invoice_id" value="{{ $invoice->id }}">

                <!-- Invoice Summary -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Invoice Summary</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Total Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control" value="{{ number_format($invoice->grand_total, 0, ',', '.') }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Payment Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="payment_date" class="form-label">Payment Date</label>
                                <input type="date" class="form-control" id="payment_date" name="payment_date"
                                    value="{{ old('payment_date', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="purchase_amount" class="form-label">Payment Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="purchase_amount" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="coa_id" class="form-label">Payment Account (COA)</label>
                                <select name="coa_id" class="form-control" required>
                                    <option value="">-- Select Account --</option>
                                    @foreach($coas as $coa)
                                    <option value="{{ $coa->id }}">{{ $coa->code }} - {{ $coa->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select class="form-control" id="payment_method" name="payment_method" required>
                                    <option value="">-- Select Payment Method --</option>
                                    <option value="tunai" {{ old('payment_method', $expense->payment_method ?? '') == 'tunai' ? 'selected' : '' }}>
                                        Tunai
                                    </option>
                                    <optgroup label="QRIS">
                                        @foreach(['QRIS BCA', 'QRIS CIMB Niaga', 'QRIS Mandiri', 'QRIS BRI', 'QRIS BNI', 'QRIS Permata', 'QRIS Maybank', 'QRIS Danamon', 'QRIS Bank Mega'] as $method)
                                        <option value="{{ $method }}" {{ old('payment_method', $invoice->payment_method) == $method ? 'selected' : '' }}>{{ $method }}</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Credit/Debit Card">
                                        @foreach(['Visa', 'Mastercard', 'JCB', 'American Express', 'GPN', 'Kartu Kredit BCA', 'Kartu Kredit Mandiri', 'Kartu Kredit BRI', 'Kartu Kredit BNI', 'Kartu Kredit CIMB Niaga'] as $method)
                                        <option value="{{ $method }}" {{ old('payment_method', $invoice->payment_method) == $method ? 'selected' : '' }}>{{ $method }}</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Bank Transfer">
                                        @foreach(['Transfer Bank BCA', 'Transfer Bank Mandiri', 'Transfer Bank BRI', 'Transfer Bank BNI', 'Transfer Bank CIMB Niaga', 'Transfer Bank Permata', 'Transfer Bank Maybank'] as $method)
                                        <option value="{{ $method }}" {{ old('payment_method', $invoice->payment_method) == $method ? 'selected' : '' }}>{{ $method }}</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="E-Wallet">
                                        @foreach(['GoPay', 'OVO', 'Dana', 'LinkAja', 'ShopeePay', 'Doku Wallet', 'PayPal'] as $method)
                                        <option value="{{ $method }}" {{ old('payment_method', $invoice->payment_method) == $method ? 'selected' : '' }}>{{ $method }}</option>
                                        @endforeach
                                    </optgroup>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Optional notes">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('dashboard.purchases.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Save Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection