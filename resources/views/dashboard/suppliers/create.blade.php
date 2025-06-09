@extends('dashboard.layouts.main')

@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['text' => 'Suppliers', 'url' => route('dashboard.suppliers.index')],
            ['text' => 'Create New Supplier']
        ]
    ])
@endsection
@section('container')
<div class="container mt-5 col-md-6">
    <h2>Create New Supplier</h2>
    <form id="createSupplierForm" action="{{ route('dashboard.suppliers.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Name <span class="text-danger">*</span></label>
            <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required>
            @error('name')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label>Address</label>
            <input type="text" name="alamat" class="form-control" value="{{ old('alamat') }}">
            @error('alamat')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label>Mobile Phone Number</label>
            <input type="text" name="nomor_telepon" class="form-control" value="{{ old('nomor_telepon') }}">
            @error('nomor_telepon')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
            @error('email')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <br>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
        <button type="submit" class="btn btn-primary">Create</button>
            <a href="{{ route('dashboard.suppliers.index') }}" class="btn btn-secondary px-4">Cancel</a>
        </div>
    </form>
</div>
<script>
    document.getElementById('createSupplierForm').addEventListener('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Confirm Supplier',
            text: "Are you sure you want to create this supplier?",
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
