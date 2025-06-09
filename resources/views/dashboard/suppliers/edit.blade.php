@extends('dashboard.layouts.main')

@section('breadcrumbs')
@include('dashboard.layouts.breadcrumbs', [
'customBreadcrumbs' => [
['text' => 'Suppliers', 'url' => route('dashboard.suppliers.index')],
['text' => 'Edit Supplier for ' . $supplier->nama]
]
])
@endsection

@section('container')
<div class="container mt-5 col-md-6">
    <h2>Edit Supplier</h2>
    <form id="editSupplierForm" action="{{ route('dashboard.suppliers.update', $supplier->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>Name <span class="text-danger">*</span></label>
            <input type="text" name="nama" class="form-control" value="{{ old('nama', $supplier->nama) }}" required>
            @error('nama')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label>Address</label>
            <input type="text" name="alamat" class="form-control" value="{{ old('alamat', $supplier->alamat) }}">
            @error('alamat')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label>Mobile Phone Number</label>
            <input type="text" name="nomor_telepon" class="form-control" value="{{ old('nomor_telepon', $supplier->nomor_telepon) }}">
            @error('nomor_telepon')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $supplier->email) }}">
            @error('email')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('dashboard.suppliers.index') }}" class="btn btn-secondary">Cancel</a>
        </div>

    </form>
</div>
<script>
    document.getElementById('editSupplierForm').addEventListener('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Update Confirmation',
            text: "Are you sure to update this supplier data?",
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
