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
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
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
                    @if ($order->status !== 'received')
                    <a href="{{ route('dashboard.purchase_orders.receive', $order->id) }}" class="btn btn-sm btn-info">
                        Receive
                    </a>
                    @else
                    {{-- Jika sudah, tampilkan badge --}}
                    <span class="badge bg-success">Completed</span>
                    @endif

                    @if($order->purchaseInvoices->isEmpty() && auth()->user()?->role?->role_name === 'manager')

                    <form id="delete-form-{{ $order->id }}" action="{{ route('dashboard.purchase_orders.destroy', $order) }}" method="POST" style="display:inline-block">
                        @csrf @method('DELETE')
                        <button type="button" class="btn btn-danger btn-sm btn-delete" data-form-id="delete-form-{{ $order->id }}">Delete</button>
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

        $('.btn-delete').on('click', function(e) {
            e.preventDefault();
            const formId = $(this).data('form-id');

            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#' + formId).submit();
                }
            });
        });
    });
</script>
@endsection