@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">

    <div class="d-flex justify-content-between mb-3">
        <h3 class="text-center">Expense Requests</h3>
        <a href="{{ route('dashboard.expense_requests.create') }}" class="btn btn-primary mb-3">Create Request</a>

    </div>
    <table class="table">
        <thead>
            <tr>
                <th>Item</th>
                <th>Quantity</th>
                <th>Estimated Cost</th>
                <th>Status</th>
                <th>Requested By</th>
                <th>Approved By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($requests as $request)
            <tr>
                <td>{{ $request->item_name }}</td>
                <td>{{ $request->quantity }}</td>
                <td>{{ $request->estimated_cost }}</td>
                <td>{{ $request->status }}</td>
                <td>{{ $request->requester->name }}</td>
                <td>{{ $request->approver->name ?? '-' }}</td>
                <td>
                    @if($request->status === 'Requested')
                    <form action="{{ route('dashboard.expense_requests.approve', $request->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button class="btn btn-success btn-sm">Approve</button>
                    </form>
                    <form action="{{ route('dashboard.expense_requests.reject', $request->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button class="btn btn-danger btn-sm">Reject</button>
                    </form>
                    @elseif($request->status === 'Approved')
                    <form action="{{ route('dashboard.expense_requests.done', $request->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button class="btn btn-info btn-sm">Mark as Done</button>
                    </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection