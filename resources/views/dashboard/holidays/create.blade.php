@extends('dashboard.layouts.main')
@section('breadcrumbs')
@include('dashboard.layouts.breadcrumbs', [
'customBreadcrumbs' => [
['url' => route('dashboard.holidays.index'), 'text' => 'Holidays'],
['text' => 'Create Holidays']
]
])
@endsection
@section('container')
<div class="container mt-5 col-md-6">
    <h2>Create Holidays</h2>
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <form id="createHolidayForm" action="{{ route('dashboard.holidays.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Date <span class="text-danger">*</span></label>
            <input type="date" name="tanggal" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Description <span class="text-danger">*</span></label></label>
            <input type="text" name="keterangan" class="form-control" required>
        </div>
        <br>
        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="{{ route('dashboard.coa.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
</div>
<script>
    document.getElementById('createHolidayForm').addEventListener('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Confirm Holiday',
            text: "Are you sure to create this Holiday data?",
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