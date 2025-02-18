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
                <th>Payment Method</th>
                <th>Created At</th>
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
                <td>{{ ucfirst($transaction->payment_method) }}</td>
                <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                <td>
                    <a href="{{ route('dashboard.transactions.showStruk', $transaction->id) }}" class="btn btn-info btn-sm">
                        Show Struk
                    </a>
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
