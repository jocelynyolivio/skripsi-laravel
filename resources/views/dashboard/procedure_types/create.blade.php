@extends('dashboard.layouts.main')
@section('breadcrumbs')
@include('dashboard.layouts.breadcrumbs', [
'customBreadcrumbs' => [
['url' => route('dashboard.procedure_types.index'), 'text' => 'Procedure Types'],
['text' => 'Create Procedure Type']
]
])
@endsection
@section('container')
<div class="container col-md-6 mt-5">
    <h2>Create Procedure Type</h2>
    <form id="createPTForm" action="{{ route('dashboard.procedure_types.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Nama <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
        </div>
        <div class="form-group">
            <label>Deskripsi <span class="text-danger">*</span></label>
            <textarea name="description" class="form-control">{{ old('description') }}</textarea>
        </div>
        <br>
        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="{{ route('dashboard.procedure_types.index') }}" class="btn btn-secondary">Cancel</a>

        </div>
    </form>
</div>
<script>
    document.getElementById('createPTForm').addEventListener('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Confirm Account',
            text: "Are you sure you want to create this procedure type?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, sure!'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
</script>
@endsection