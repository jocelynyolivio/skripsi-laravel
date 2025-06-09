@extends('dashboard.layouts.main')

@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['text' => 'Master Users', 'url' => route('dashboard.masters.index')],
            ['text' => 'Create New User'],
        ],
    ])
@endsection

@section('container')
    <div class="container mt-5 col-md-8">
    <h3 class="text-center mb-4">Create New User</h3>
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form id="addUserForm" action="{{ route('dashboard.masters.store') }}" method="POST"
            enctype="multipart/form-data">
            @csrf

            <div class="card mb-4">
                <div class="card-header">
                    <h5>Basic Information</h5>
                </div>
                <div class="card-body">
                    {{-- Bagian Nama, Email, dan Role tidak berubah --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required
                                value="{{ old('name') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required
                                value="{{ old('email') }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation" required>
                                <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            {{-- --- BARU ---: Div untuk menampilkan pesan status password --}}
                            <div id="password-match-status" class="form-text mt-1"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="role_id" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="role_id" name="role_id" required>
                                <option value="" selected disabled>Select Role</option>
                                <option value="1" {{ old('role_id') == 1 ? 'selected' : '' }}>Admin</option>
                                <option value="2" {{ old('role_id') == 2 ? 'selected' : '' }}>Doctor</option>
                                <option value="3" {{ old('role_id') == 3 ? 'selected' : '' }}>Manager</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card untuk Personal & Professional Details tidak ada perubahan --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Personal Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tempat_lahir" class="form-label">Place of Birth</label>
                            <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir"
                                value="{{ old('tempat_lahir') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_lahir" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir"
                                value="{{ old('tanggal_lahir') }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nik" class="form-label">NIK (ID Number) <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nik" name="nik" required
                                value="{{ old('nik') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nomor_telepon" class="form-label">Phone Number <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nomor_telepon" name="nomor_telepon" required
                                value="{{ old('nomor_telepon') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="alamat" class="form-label">Address <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3" required>{{ old('alamat') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5>Professional Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_bergabung" class="form-label">Join Date <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal_bergabung" name="tanggal_bergabung"
                                value="{{ old('tanggal_bergabung', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nomor_sip" class="form-label">SIP Number <small>(for doctors,
                                    optional)</small></label>
                            <input type="text" class="form-control" id="nomor_sip" name="nomor_sip"
                                value="{{ old('nomor_sip') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="nomor_rekening" class="form-label">Bank Account Number <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nomor_rekening" name="nomor_rekening" required
                            value="{{ old('nomor_rekening') }}">
                    </div>

                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Description</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="submit" id="createUserBtn" class="btn btn-primary px-4">Create User</button>
                <a href="{{ route('dashboard.masters.index') }}" class="btn btn-secondary px-4">Cancel</a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function setupPasswordToggle(toggleBtnId, passwordFieldId) {
                const toggleButton = document.getElementById(toggleBtnId);
                const passwordField = document.getElementById(passwordFieldId);
                if (!toggleButton || !passwordField) return;
                const icon = toggleButton.querySelector('i');
                toggleButton.addEventListener('click', function() {
                    const isPassword = passwordField.type === 'password';
                    passwordField.type = isPassword ? 'text' : 'password';
                    icon.classList.toggle('bi-eye', !isPassword);
                    icon.classList.toggle('bi-eye-slash', isPassword);
                });
            }

            setupPasswordToggle('togglePassword', 'password');
            setupPasswordToggle('toggleConfirmPassword', 'password_confirmation');

            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('password_confirmation');
            const statusMessage = document.getElementById('password-match-status');
            const submitButton = document.getElementById('createUserBtn');

            function checkPasswordMatch() {
                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;

                if (confirmPassword.length === 0) {
                    statusMessage.textContent = '';
                    submitButton.disabled = false; 
                    return;
                }

                if (password === confirmPassword) {
                    statusMessage.textContent = 'Passwords match!';
                    statusMessage.classList.remove('text-danger');
                    statusMessage.classList.add('text-success');
                    submitButton.disabled = false; 
                } else {
                    statusMessage.textContent = 'Passwords do not match!';
                    statusMessage.classList.remove('text-success');
                    statusMessage.classList.add('text-danger');
                    submitButton.disabled = true;
                }
            }

            passwordInput.addEventListener('keyup', checkPasswordMatch);
            confirmPasswordInput.addEventListener('keyup', checkPasswordMatch);

            const addUserForm = document.getElementById('addUserForm');
            if (addUserForm) {
                addUserForm.addEventListener('submit', function(e) {
                    if (passwordInput.value !== confirmPasswordInput.value) {
                         e.preventDefault();
                         Swal.fire({
                            title: 'Error!',
                            text: 'Password and Confirm Password do not match.',
                            icon: 'error',
                            confirmButtonColor: '#d33'
                         });
                         return;
                    }
                    
                    e.preventDefault();
                    Swal.fire({
                        title: 'Confirm User Creation',
                        text: "Are you sure you want to create this new user?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, create it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            }
        });
    </script>
@endsection