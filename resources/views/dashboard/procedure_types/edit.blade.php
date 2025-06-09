@extends('dashboard.layouts.main')
@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['url' => route('dashboard.procedure_types.index'), 'text' => 'Procedure Types'],
            ['text' => 'Edit Procedure Type']
        ]
    ])
@endsection
@section('container')
<div class="container col-md-6 mt-5">
    <h2>Edit Procedure Type</h2>
    <form id="editPTForm" action="{{ route('dashboard.procedure_types.update', $procedureType->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="name" class="form-control" required value="{{ old('name', $procedureType->name) }}">
        </div>
        <div class="mb-3">
            <label>Deskripsi</label>
            <textarea name="description" class="form-control">{{ old('description', $procedureType->description) }}</textarea>
        </div>
        
        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
<button type="submit" class="btn btn-primary">Update</button>            <a href="{{ route('dashboard.procedure_types.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
<script>
    document.getElementById('editPTForm').addEventListener('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Update Confirmation',
            text: "Are you sure to update this Procedure Type data?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, sure!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
</script>
@endsection
