@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <div class="d-flex justify-content-between mb-3">
        <h3 class="text-center">Transaction List</h3>
        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                Add Transaction
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
                <th>User</th>
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
                <td>
                    {{ optional($transaction->medicalRecord?->reservation->patient)->name 
                    ?? optional($transaction->patient)->name 
                    ?? 'N/A' }}
                </td>
                <td>{{ $transaction->admin->name }}</td>
                <td>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                <td>
                    Rp {{ number_format($transaction->remaining_amount, 0, ',', '.') }}
                </td>
                <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                <td>
                    <span class="badge bg-{{ $transaction->payment_status == 'belum lunas' ? 'danger' : 'success' }}">
                        {{ ucfirst($transaction->payment_status) }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('dashboard.transactions.showStruk', $transaction->id) }}" class="btn btn-info btn-sm">
                        Show Struk
                    </a>
                    @if($transaction->payment_status == 'belum lunas')
                    <!-- Button Add Payments -->
                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addPaymentModal-{{ $transaction->id }}">
                        Add Payments
                    </button>

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