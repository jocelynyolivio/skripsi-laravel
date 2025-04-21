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

    <table class="table table-bordered table-striped">
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
    document.querySelectorAll('.delete-button').forEach(btn => {
        btn.addEventListener('click', function () {
            if (confirm('Yakin ingin menghapus data ini?')) {
                this.closest('form').submit();
            }
        });
    });
</script>
@endsection
