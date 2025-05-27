@extends('dashboard.layouts.main')

@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['text' => 'Suppliers']
        ]
    ])
@endsection
@section('container')
<div class="container mt-5">
    <div class="d-flex justify-content-between mb-3">
        <h3 class="mb-4">Suppliers List</h3>
        <a href="{{ route('dashboard.suppliers.create') }}" class="btn btn-success mb-3">Create New Suppliers</a>
    </div>

    <table class="table table-striped table-bordered" id="suppliersTable">
        <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>Address</th>
                <th>Number</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($suppliers as $index => $supplier)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $supplier->nama }}</td>
                <td>{{ $supplier->alamat }}</td>
                <td>{{ $supplier->nomor_telepon }}</td>
                <td>{{ $supplier->email }}</td>
                <td>
                    <a href="{{ route('dashboard.suppliers.edit', $supplier->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('dashboard.suppliers.destroy', $supplier->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm delete-button">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<script>
    $(document).ready(function() {
        $('#suppliersTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,
            "columnDefs": [{
                    "orderable": false,
                    "targets": 4
                } // Kolom ke-4 adalah kolom Actions
            ]
        });

        // Event delegation for SweetAlert confirmation
        $('#suppliersTable').on('click', '.delete-button', function(e) {
            e.preventDefault();
            console.log('Delete button clicked');
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
    });
</script>
@endsection