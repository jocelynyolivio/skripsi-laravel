{{-- resources/views/dashboard/dental_materials/index.blade.php --}}
@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h3 class="text-center">Dental Materials</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('dashboard.dental-materials.create') }}" class="btn btn-primary mb-3">
        Add New Material
    </a>

    <table id="dentalMaterialTable" class="display">
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
                        <form action="{{ route('dashboard.dental-materials.destroy', $material->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
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
</script>
@endsection
