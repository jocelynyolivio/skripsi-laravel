{{-- resources/views/dashboard/dental_materials/index.blade.php --}}
@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <div class="d-flex justify-content-between mb-3">
        <h3 class="text-center">Dental Materials</h3>
        <a href="{{ route('dashboard.dental-materials.create') }}" class="btn btn-primary mb-3">
        Add New Material
    </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table id="dentalMaterialTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Stock Quantity</th>
                <th>Unit Price</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dentalMaterials as $material)
            <tr>
                <td>{{ $material->name }}</td>
                <td>{{ $material->description }}</td>
                <td>{{ $material->stock_quantity }}</td>
                <td>{{ $material->unit_price }}</td>
                <td>
                    <a href="{{ route('dashboard.dental-materials.edit', $material->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('dashboard.dental-materials.destroy', $material->id) }}" method="POST" class="d-inline delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger btn-sm delete-button">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

</div>
<script>
    $(document).ready(function() {
        $('#dentalMaterialTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,
        });
    });

    // Event delegation for SweetAlert confirmation
    $('#dentalMaterialTable').on('click', '.delete-button', function(e) {
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