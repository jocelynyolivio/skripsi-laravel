@extends('dashboard.layouts.main')

@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['text' => 'Expenses']
        ]
    ])
@endsection

@section('container')
<div class="container mt-5">

    <div class="d-flex justify-content-between mb-3">
        <h3 class="text-center">Expenses List</h3>
        <a href="{{ route('dashboard.expenses.create') }}" class="btn btn-primary">Create Expense</a>
    </div>

    <table id="expensesTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Expense Date</th>
                <th>Amount</th>
                <th>Description</th>
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
                <td>Rp. {{ number_format($expense->amount, 2, ',', '.') }}</td>
                <td>{{ $expense->description }}</td>
                <td>{{ $expense->created_at }}</td>
                <td>{{ $expense->admin?->name ?? 'N/A' }}</td>
                <td>
                    <a href="{{ route('dashboard.expenses.show', $expense->id) }}" class="btn btn-sm btn-info me-1" title="View">Show
                    </a>
                    <!-- @if(auth()->user()?->role?->role_name === 'manager')
                    <a href="{{ route('dashboard.expenses.edit', $expense->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('dashboard.expenses.destroy', $expense->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                    @endif -->
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