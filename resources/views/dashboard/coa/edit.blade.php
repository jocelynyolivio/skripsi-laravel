@extends('dashboard.layouts.main')
@section('breadcrumbs')
@include('dashboard.layouts.breadcrumbs', [
'customBreadcrumbs' => [
['url' => route('dashboard.coa.index'), 'text' => 'Chart Of Account'],
['text' => 'Edit Chart Of Account']
]
])
@endsection
@section('container')
<div class="container mt-5 col-md-6">
    <h2>Edit Chart of Account</h2>
    <form id="editCOAForm" action="{{ route('dashboard.coa.update', $coa->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="code">Code</label>
            <input type="text" name="code" value="{{ $coa->code }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="name">Name</label>
            <input type="text" name="name" value="{{ $coa->name }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="type">Type</label>
            <input type="text" name="type" value="{{ $coa->type }}" class="form-control" required>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <button class="btn btn-primary">Update</button>
            <a href="{{ route('dashboard.coa.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
<script>
    document.getElementById('editCOAForm').addEventListener('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Update Confirmation',
            text: "Are you sure to update this COA data?",
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