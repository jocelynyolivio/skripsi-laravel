@extends('dashboard.layouts.main')

@section('breadcrumbs')
@include('dashboard.layouts.breadcrumbs', [
'customBreadcrumbs' => [
['text' => 'Purchases Orders']
]
])
@endsection

@section('container')
<div class="container mt-5">
    <div class="d-flex justify-content-between mb-3">
        <h3>Purchase Orders</h3>
        @if(auth()->user()?->role?->role_name === 'manager')
        <!-- <a href="{{ route('dashboard.purchase_orders.create') }}" class="btn btn-primary mb-3">Create New</a> -->
         <a href="{{ route('dashboard.purchase_orders.select_request') }}" class="btn btn-primary mb-3">Create New</a>
        @endif
    </div>
    <table id="purchaseOrderTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Order Number</th>
                <th>Supplier</th>
                <th>Order Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $order->order_number }}</td>
                <td>{{ $order->supplier->nama ?? '-' }}</td>
                <td>{{ $order->order_date }}</td>
                <td>
                    <a href="{{ route('dashboard.purchase_orders.show', $order) }}" class="btn btn-info btn-sm">View</a>


                    @if($order->purchaseInvoices->isEmpty() && auth()->user()?->role?->role_name === 'manager')
                    <a href="{{ route('dashboard.purchase_orders.edit', $order) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('dashboard.purchase_orders.destroy', $order) }}" method="POST" style="display:inline-block">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('Are you sure?')" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                    @endif


                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<script>
    $(document).ready(function() {
        $('#purchaseOrderTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,
        });
    });
</script>
@endsection