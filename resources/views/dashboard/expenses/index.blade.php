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
                <th>Supplier</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Category</th>
                <th>Description</th>
                <th>Material</th>
                <th>Quantity</th>
                <th>Expired at</th>
                <th>Created By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($expenses as $expense)

            <tr>
                <td>{{ $expense->supplier->nama ?? '-' }}</td>
                <td>{{ $expense->date }}</td>
                <td>Rp. {{ number_format($expense->amount, 2, ',', '.') }}</td>
                <td>{{ $expense->category->name }}</td>
                <td>{{ $expense->description }}</td>
                <td>{{ $expense->dentalMaterial?->name ?? '-' }}</td>
                <td>{{ $expense->quantity ?? '-' }}</td>
                <td>{{ $expense->expired_at ? date('d M Y', strtotime($expense->expired_at)) : '-' }}</td>

                <td>{{ $expense->admin?->name ?? 'N/A' }}</td>
                <td>
                    <a href="{{ route('dashboard.expenses.edit', $expense->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('dashboard.expenses.destroy', $expense->id) }}" method="POST" style="display:inline;" class="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-sm btn-danger delete-button">Delete</button>
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

    // Event delegation for SweetAlert confirmation
    $('#expensesTable').on('click', '.delete-button', function(e) {
        e.preventDefault();
        var form = $(this).closest('form');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
</script>
@endsection