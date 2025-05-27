@extends('dashboard.layouts.main')
@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['text' => 'Procedure Types']
        ]
    ])
@endsection
@section('container')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h3 class="text-center">Procedure Types</h3>
        <a href="{{ route('dashboard.procedure_types.create') }}" class="btn btn-primary">Create Procedure Type</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped" id="procedureTypeTable">
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($procedureTypes as $type)
            <tr>
                <td>{{ $type->name }}</td>
                <td>{{ $type->description }}</td>
                <td>
                    <a href="{{ route('dashboard.procedure_types.edit', $type->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('dashboard.procedure_types.destroy', $type->id) }}" method="POST" style="display:inline;" class="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger btn-sm delete-button">Delete</button>
                    </form>
                </td>
            @endforeach
        </tbody>
    </table>
</div>
<script>
    $(document).ready(function () {
        $('#procedureTypeTable').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            responsive: true,
            columnDefs: [{
                orderable: false,
                targets: 2 // kolom "Aksi"
            }]
        });

        $('#procedureTypeTable').on('click', '.delete-button', function (e) {
            e.preventDefault();
            const form = $(this).closest('form');
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
