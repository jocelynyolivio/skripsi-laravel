@extends('dashboard.layouts.main')
@section('breadcrumbs')
@include('dashboard.layouts.breadcrumbs', [
'customBreadcrumbs' => [
['text' => 'Master Users', 'url' => route('dashboard.masters.index')],
['text' => 'Create New User']
]
])
@endsection
@section('container')
<div class="container mt-5 col-md-8">
    <h3 class="my-4">Add New User</h3>
    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    <form id="addUserForm" action="{{ route('dashboard.masters.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Basic Information Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Basic Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Full Name*</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email*</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Password*</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="role_id" class="form-label">Role*</label>
                        <select class="form-select" id="role_id" name="role_id" required>
                            <option value="">Select Role</option>
                            <option value="1">Admin</option>
                            <option value="2">Doctor</option>
                            <option value="3">Manager</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Personal Details Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Personal Details</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tempat_lahir" class="form-label">Place of Birth</label>
                        <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_lahir" class="form-label">Date of Birth</label>
                        <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nik" class="form-label">NIK (ID Number)*</label>
                        <input type="text" class="form-control" id="nik" name="nik" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="nomor_telepon" class="form-label">Phone Number*</label>
                        <input type="text" class="form-control" id="nomor_telepon" name="nomor_telepon" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="alamat" class="form-label">Address*</label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="3" required></textarea>
                </div>
            </div>
        </div>

        <!-- Professional Information Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Professional Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_bergabung" class="form-label">Join Date*</label>
                        <input type="date" class="form-control" id="tanggal_bergabung" name="tanggal_bergabung" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="nomor_sip" class="form-label">SIP Number (for doctors)</label>
                        <input type="text" class="form-control" id="nomor_sip" name="nomor_sip">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="nomor_rekening" class="form-label">Bank Account Number*</label>
                    <input type="text" class="form-control" id="nomor_rekening" name="nomor_rekening" required>
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Description</label>
                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
                </div>
            </div>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <button type="submit" class="btn btn-success px-4">Create User</button>
            <a href="{{ route('dashboard.masters.index') }}" class="btn btn-secondary px-4">Cancel</a>
        </div>
    </form>
</div>

<script>
    document.getElementById('togglePassword').addEventListener('click', function() {
        let passwordField = document.getElementById('password');
        let icon = this.querySelector('i');

        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            passwordField.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    });

    document.getElementById('addUserForm').addEventListener('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Confirm User Creation',
            text: "Are you sure you want to create this new user?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, create it!'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
</script>
@endsection