@extends('dashboard.layouts.main')

@section('breadcrumbs')
@include('dashboard.layouts.breadcrumbs', [
'customBreadcrumbs' => [
['text' => 'Transactions']
]
])
@endsection

@section('container')
<div class="container mt-5">
    <div class="d-flex justify-content-between mb-3">
        <h3 class="text-center">Transaction List</h3>
        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                Create Transaction
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('dashboard.transactions.createWithoutMedicalRecord') }}">Without Medical Record</a></li>
                <li><a class="dropdown-item" href="{{ route('dashboard.transactions.selectMedicalRecord') }}">With Medical Record</a></li>
            </ul>
        </div>

    </div>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <table id="transactionTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Transaction ID</th>
                <th>Date</th>
                <th>Pasien</th>
                <th>Admin</th>
                <th>Amount</th>
                <th>Sisa Tagihan</th>
                <th>Created At</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
            <tr>
                <td>{{ $transaction->id }}</td>
                <td>{{ $transaction->created_at->translatedFormat('d F Y H:i') }}</td>
                <td>
                    @php
                    $patient = $transaction->medicalRecord?->patient ?? $transaction->patient;
                    $fullName = trim("{$patient->fname} {$patient->mname} {$patient->lname}");
                    @endphp

                    {{ $fullName ?: 'N/A' }}
                </td>
                <td>{{ $transaction->admin->name }}</td>
                <td>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                <td>
                    Rp {{ number_format($transaction->remaining_amount, 0, ',', '.') }}
                </td>
                <td>

                    <p>Last edited by: {{ $transaction->editor->name ?? 'Unknown' }}</p>
                    <p>Last edited at: {{ $transaction->created_at->format('d M Y H:i') }}</p>
                </td>
                <td>
                    <span class="badge bg-{{ $transaction->payment_status == 'belum lunas' ? 'danger' : 'success' }}">
                        {{ ucfirst($transaction->payment_status) }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('dashboard.transactions.showStruk', $transaction->id) }}" class="btn btn-info btn-sm">
                        Show Struk
                    </a>

                    <a href="{{ route('dashboard.transactions.show', $transaction->id) }}" class="btn btn-info btn-sm">
                        Show
                    </a>
                    @if($transaction->payment_status == 'belum lunas')
                    <!-- Button Add Payments -->
                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addPaymentModal-{{ $transaction->id }}">
                        Add Payments
                    </button>
                    <a href="{{ route('dashboard.transactions.show', $transaction->id) }}">show</a>

                    <!-- Modal Add Payments -->
                    <div class="modal fade" id="addPaymentModal-{{ $transaction->id }}" tabindex="-1" aria-labelledby="addPaymentModalLabel-{{ $transaction->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('dashboard.transactions.payRemaining', $transaction->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addPaymentModalLabel-{{ $transaction->id }}">Add Payments</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label>Amount:</label>
                                            <input type="number" name="amount" class="form-control" min="1" max="{{ $transaction->remaining_amount }}" value="{{ $transaction->remaining_amount}}" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="coa_id">Menerima Ke (Akun Kas/Bank)</label>
                                            <select class="form-control" id="coa_id" name="coa_id" required>
                                                <option value="">-- Pilih Akun Kas/Bank --</option>
                                                @foreach ($cashAccounts as $account)
                                                <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="payment_method" class="form-label">Cara Bayar</label>
                                            <select class="form-control" id="payment_method" name="payments[0][method]" required>

                                                <option value="">-- Pilih Metode Pembayaran --</option>
                                                <option value="tunai" {{ old('payment_method', $expense->payment_method ?? '') == 'tunai' ? 'selected' : '' }}>
                                                    Tunai
                                                </option>

                                                <!-- QRIS -->
                                                <optgroup label="QRIS">
                                                    <option value="QRIS BCA">QRIS BCA</option>
                                                    <option value="QRIS CIMB Niaga">QRIS CIMB Niaga</option>
                                                    <option value="QRIS Mandiri">QRIS Mandiri</option>
                                                    <option value="QRIS BRI">QRIS BRI</option>
                                                    <option value="QRIS BNI">QRIS BNI</option>
                                                    <option value="QRIS Permata">QRIS Permata</option>
                                                    <option value="QRIS Maybank">QRIS Maybank</option>
                                                    <option value="QRIS Danamon">QRIS Danamon</option>
                                                    <option value="QRIS Bank Mega">QRIS Bank Mega</option>
                                                </optgroup>

                                                <!-- Kartu Kredit/Debit -->
                                                <optgroup label="Kartu Kredit/Debit">
                                                    <option value="Visa">Visa</option>
                                                    <option value="Mastercard">Mastercard</option>
                                                    <option value="JCB">JCB</option>
                                                    <option value="American Express">American Express (AMEX)</option>
                                                    <option value="GPN">GPN (Gerbang Pembayaran Nasional)</option>
                                                    <option value="Kartu Kredit BCA">Kartu Kredit BCA</option>
                                                    <option value="Kartu Kredit Mandiri">Kartu Kredit Mandiri</option>
                                                    <option value="Kartu Kredit BRI">Kartu Kredit BRI</option>
                                                    <option value="Kartu Kredit BNI">Kartu Kredit BNI</option>
                                                    <option value="Kartu Kredit CIMB Niaga">Kartu Kredit CIMB Niaga</option>
                                                </optgroup>

                                                <!-- Transfer Bank -->
                                                <optgroup label="Transfer Bank">
                                                    <option value="Transfer Bank BCA">Transfer Bank BCA</option>
                                                    <option value="Transfer Bank Mandiri">Transfer Bank Mandiri</option>
                                                    <option value="Transfer Bank BRI">Transfer Bank BRI</option>
                                                    <option value="Transfer Bank BNI">Transfer Bank BNI</option>
                                                    <option value="Transfer Bank CIMB Niaga">Transfer Bank CIMB Niaga</option>
                                                    <option value="Transfer Bank Permata">Transfer Bank Permata</option>
                                                    <option value="Transfer Bank Maybank">Transfer Bank Maybank</option>
                                                </optgroup>

                                                <!-- E-Wallet -->
                                                <optgroup label="E-Wallet">
                                                    <option value="GoPay">GoPay</option>
                                                    <option value="OVO">OVO</option>
                                                    <option value="Dana">Dana</option>
                                                    <option value="LinkAja">LinkAja</option>
                                                    <option value="ShopeePay">ShopeePay</option>
                                                    <option value="Doku Wallet">Doku Wallet</option>
                                                    <option value="PayPal">PayPal</option>
                                                </optgroup>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label>Notes:</label>
                                            <input type="text" name="notes" class="form-control">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-success">Add Payment</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>

<script>
    $(document).ready(function() {
        $('#transactionTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,
        });
    });
</script>
@endsection