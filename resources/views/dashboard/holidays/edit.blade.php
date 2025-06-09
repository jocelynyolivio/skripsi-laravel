@extends('dashboard.layouts.main')

@section('breadcrumbs')
@include('dashboard.layouts.breadcrumbs', [
'customBreadcrumbs' => [
['url' => route('dashboard.holidays.index'), 'text' => 'Holidays'],
['text' => 'Edit Holiday'] {{-- Judul breadcrumb disesuaikan --}}
]
])
@endsection

@section('container')
<div class="container mt-5 col-md-6">
    <h2>Edit Holiday</h2>

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <form id="editHolidayForm" action="{{ route('dashboard.holidays.update', $holiday->id) }}" method="POST">
        @csrf
        @method('PUT') {{-- Metode untuk update --}}

        <div class="form-group">
            <label for="tanggal">Date <span class="text-danger">*</span></label>
            <input type="date" id="tanggal" name="tanggal" class="form-control" value="{{ old('tanggal', $holiday->tanggal) }}" required>
        </div>

        <div class="form-group mt-3">
            <label for="keterangan">Description <span class="text-danger">*</span></label>
            <input type="text" id="keterangan" name="keterangan" class="form-control" value="{{ old('keterangan', $holiday->keterangan) }}" required>
        </div>

        <br>
        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('dashboard.holidays.index') }}" class="btn btn-secondary">Cancel</a>

        </div>

    </form>
</div>
<script>
    document.getElementById('editHolidayForm').addEventListener('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Update Confirmation',
            text: "Are you sure to update this Holiday data?",
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