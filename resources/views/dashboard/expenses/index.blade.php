@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">

    <div class="d-flex justify-content-between mb-3">
        <h3 class="text-center">Expenses List</h3>
        <a href="{{ route('dashboard.expenses.create') }}" class="btn btn-primary">Add Expense</a>
    </div>

    <table id="expensesTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Expense Date</th>
                <th>COA Out</th>
                <th>COA In</th>
                <th>Amount</th>
                <th>Description</th>
                <th>Reference Number</th>
                <th>Created At</th>
                <th>Created By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($expenses as $expense)
            <tr>
                <td>{{ $expense->id }}</td>
                <td>{{ $expense->expense_date }}</td>
                <td>{{ $expense->coaOut->name ?? '-' }}</td>
                <td>{{ $expense->coaIn->name ?? '-' }}</td>
                <td>Rp. {{ number_format($expense->amount, 2, ',', '.') }}</td>
                <td>{{ $expense->description }}</td>
                <td>{{ $expense->reference_number ?? '-' }}</td>
                <td>{{ $expense->created_at }}</td>
                <td>{{ $expense->admin?->name ?? 'N/A' }}</td>
                <td>
                    <a href="{{ route('dashboard.expenses.edit', $expense->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('dashboard.expenses.destroy', $expense->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>

                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('#expensesTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,
        });
    });
</script>
@endsection