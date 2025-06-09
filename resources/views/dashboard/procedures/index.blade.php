@extends('dashboard.layouts.main')
@section('breadcrumbs')
@include('dashboard.layouts.breadcrumbs', [
'customBreadcrumbs' => [
['text' => 'Procedures']
]
])
@endsection
@section('container')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h3 class="text-center">Procedures</h3>
        <a href="{{ route('dashboard.procedures.create') }}" class="btn btn-primary">Create Procedure</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table id="proceduresTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Code</th>
                <th>Type</th>
                <th>Description</th>
                <th>Requires Tooth</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($procedures as $procedure)
            <tr>
                <td>{{ $procedure->name }}</td>
                <td>{{ $procedure->item_code }}</td>
                <td>{{ $procedure->procedureType->name ?? '-' }}</td>
                <td>{{ $procedure->description }}</td>
                <td>{{ $procedure->requires_tooth ? 'Yes' : 'No' }}</td>
                <td>
                    <a href="{{ route('dashboard.procedures.edit', $procedure->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    @if(auth()->user()?->role?->role_name === 'manager')
                    <form action="{{ route('dashboard.procedures.destroy', $procedure->id) }}" method="POST" style="display:inline;" class="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger btn-sm delete-button">Delete</button>
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
        $('#proceduresTable').DataTable();

        $('.delete-button').click(function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            Swal.fire({
                title: 'Are you sure?',
                text: 'Data will be permanently deleted!',
                icon: 'warning',
                showCancelButton: true,
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