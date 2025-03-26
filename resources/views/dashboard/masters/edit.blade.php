@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5 col-md-6">
    <h3 class="my-4">Edit User</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('dashboard.masters.update', ['id' => $user->id]) }}" method="POST">
    @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Nama</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}">
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" >
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password (Kosongkan jika tidak ingin diubah)</label>
            <div class="input-group">
                <input type="password" class="form-control" id="password" name="password">
                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>

        <div class="mb-3">
            <label for="role_id" class="form-label">Role</label>
            <select class="form-select" id="role_id" name="role_id" required>
                <option value="1" {{ old('role_id', $user->role_id) == 1 ? 'selected' : '' }}>Admin</option>
                <option value="2" {{ old('role_id', $user->role_id) == 2 ? 'selected' : '' }}>Doctor</option>
                <option value="3" {{ old('role_id', $user->role_id) == 3 ? 'selected' : '' }}>Manager</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="tanggal_bergabung" class="form-label">Tanggal Bergabung</label>
            <input type="date" class="form-control" id="tanggal_bergabung" name="tanggal_bergabung" 
                value="{{ old('tanggal_bergabung', $user->tanggal_bergabung ?? date('Y-m-d')) }}">
        </div>

        <div class="mb-3">
            <label for="nomor_sip" class="form-label">Nomor SIP</label>
            <input type="text" class="form-control" id="nomor_sip" name="nomor_sip" value="{{ old('nomor_sip', $user->nomor_sip) }}">
        </div>

        <div class="mb-3">
            <label for="nik" class="form-label">NIK</label>
            <input type="text" class="form-control" id="nik" name="nik" value="{{ old('nik', $user->nik) }}">
        </div>

        <div class="mb-3">
            <label for="nomor_telepon" class="form-label">Nomor Telepon</label>
            <input type="text" class="form-control" id="nomor_telepon" name="nomor_telepon" value="{{ old('nomor_telepon', $user->nomor_telepon) }}">
        </div>

        <div class="mb-3">
            <label for="alamat" class="form-label">Alamat</label>
            <textarea class="form-control" id="alamat" name="alamat">{{ old('alamat', $user->alamat) }}</textarea>
        </div>

        <div class="mb-3">
            <label for="nomor_rekening" class="form-label">Nomor Rekening</label>
            <input type="text" class="form-control" id="nomor_rekening" name="nomor_rekening" value="{{ old('nomor_rekening', $user->nomor_rekening) }}">
        </div>

        <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi</label>
            <textarea class="form-control" id="deskripsi" name="deskripsi">{{ old('deskripsi', $user->deskripsi) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
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
</script>
@endsection
