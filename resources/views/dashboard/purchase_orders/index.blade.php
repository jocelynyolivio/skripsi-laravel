@extends('dashboard.layouts.main')

@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['text' => 'Purchases Orders']
        ]
    ])
@endsection

@section('container')
<div class="container">
    <h1>Purchase Orders</h1>
    <a href="{{ route('dashboard.purchase_orders.create') }}" class="btn btn-primary mb-3">Create New</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Order Number</th>
                <th>Supplier</th>
                <th>Order Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $order->order_number }}</td>
                <td>{{ $order->supplier->name ?? '-' }}</td>
                <td>{{ $order->order_date }}</td>
                <td>{{ ucfirst($order->status) }}</td>
                <td>
                    <a href="{{ route('dashboard.purchase_orders.show', $order) }}" class="btn btn-info btn-sm">View</a>
                    <a href="{{ route('dashboard.purchase_orders.edit', $order) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('dashboard.purchase_orders.destroy', $order) }}" method="POST" style="display:inline-block">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('Are you sure?')" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
