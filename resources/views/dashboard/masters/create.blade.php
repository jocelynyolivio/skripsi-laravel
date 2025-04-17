@extends('dashboard.layouts.main')
@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['text' => 'Master Users', 'url' => route('dashboard.masters.index')],
            ['text' => 'Add New User']
        ]
    ])
@endsection
@section('container')
<div class="container mt-5 col-md-6">
    <h3 class="my-4">Add New User</h3>
    

    <form id="addUserForm" action="{{ route('dashboard.masters.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Name*</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email*</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password*</label>
            <div class="input-group">
                <input type="password" class="form-control" id="password" name="password" required>
                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>

        <div class="mb-3">
            <label for="role_id" class="form-label">Role*</label>
            <select class="form-select" id="role_id" name="role_id" required>
                <option value="1">Admin</option>
                <option value="2">Doctor</option>
                <option value="3">Manager</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="tanggal_bergabung" class="form-label">Date Joined*</label>
            <input type="date" class="form-control" id="tanggal_bergabung" name="tanggal_bergabung" value="{{ date('Y-m-d') }}">
        </div>

        <div class="mb-3">
            <label for="nomor_sip" class="form-label">SIP Number (for doctors)</label>
            <input type="text" class="form-control" id="nomor_sip" name="nomor_sip">
        </div>

        <div class="mb-3">
            <label for="nik" class="form-label">NIK*</label>
            <input type="text" class="form-control" id="nik" name="nik" required>
        </div>

        <div class="mb-3">
            <label for="nomor_telepon" class="form-label">Mobile Phone*</label>
            <input type="text" class="form-control" id="nomor_telepon" name="nomor_telepon" required>
        </div>

        <div class="mb-3">
            <label for="alamat" class="form-label">Address*</label>
            <textarea class="form-control" id="alamat" name="alamat" required></textarea>
        </div>

        <div class="mb-3">
            <label for="nomor_rekening" class="form-label">Account Number (Rekening)*</label>
            <input type="text" class="form-control" id="nomor_rekening" name="nomor_rekening" required>
        </div>

        <div class="mb-3">
            <label for="deskripsi" class="form-label">Description</label>
            <textarea class="form-control" id="deskripsi" name="deskripsi"></textarea>
        </div>

        <button type="submit" class="btn btn-success w-100 mt-4">Add Users</button>
    </form>
</div>

{{-- JavaScript untuk toggle password --}}
<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
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

    // SweetAlert2 confirmation before form submit
    document.getElementById('addUserForm').addEventListener('submit', function (e) {
        e.preventDefault();  // Prevent the form from submitting immediately
        
        Swal.fire({
            title: 'Are you sure?',
            text: "You are about to add a new user.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, save it!',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();  // Submit the form if confirmed
            }
        });
    });
</script>
@endsection
