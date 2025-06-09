@extends('dashboard.layouts.main')
@section('breadcrumbs')
@include('dashboard.layouts.breadcrumbs', [
'customBreadcrumbs' => [
['url' => route('dashboard.coa.index'), 'text' => 'Chart Of Account'],
['text' => 'Create Chart Of Account']
]
])
@endsection
@section('container')
<div class="container mt-5 col-md-6">
    <h2>Create New Chart of Account</h2>
    <form id="createCOAForm" action="{{ route('dashboard.coa.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="code">Code <span class="text-danger">*</span></label>
            <input type="text" name="code" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="name">Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="type">Type <span class="text-danger">*</span></label>
            <select name="type" class="form-control" required>
                <option value="">-- Select Type --</option>
                <option value="asset">Asset</option>
                <option value="liability">Liability</option>
                <option value="equity">Equity</option>
                <option value="expense">Expense</option>
            </select>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <button class="btn btn-primary">Save</button>
            <a href="{{ route('dashboard.coa.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
<script>
    document.getElementById('createCOAForm').addEventListener('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Confirm Account',
            text: "Are you sure you want to create this account?",
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