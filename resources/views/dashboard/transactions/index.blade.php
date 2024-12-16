@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <div class="d-flex justify-content-between mb-3">
        <h3 class="text-center">Transaction List</h3>
        <a href="{{ route('dashboard.transactions.selectMedicalRecord') }}" class="btn btn-primary">Add Transaction</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <table class="table table-striped mt-4" id="transactionTable">
        <thead>
            <tr>
                <th>Transaction ID</th>
                <th>Patient</th>
                <th>Doctor</th>
                <th>Admin</th>
                <th>Amount</th>
                <th>Payment Type</th>
                <th>Payment Status</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
            <tr>
                <td>{{ $transaction->id }}</td>
                <td>{{ $transaction->patient->name }}</td>
                <td>{{ $transaction->doctor->name }}</td>
                <td>{{ $transaction->admin->name }}</td>
                <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                <td>{{ ucfirst($transaction->payment_type) }}</td>
                <td>{{ ucfirst($transaction->payment_status) }}</td>
                <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                <td>
                    <!-- Button untuk Show Struk -->
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