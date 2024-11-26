@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h1 class="h2">Profil Saya</h1>
    <hr>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Menampilkan informasi profil pengguna -->
    <div class="mb-3">
        <label class="form-label"><strong>Nama:</strong></label>
        <p>{{ $user->name }}</p>
    </div>

    <div class="mb-3">
        <label class="form-label"><strong>Email:</strong></label>
        <p>{{ $user->email }}</p>
    </div>

    <!-- Tombol Edit Profil -->
    <a href="{{ route('profile.edit') }}" class="btn btn-warning">Edit Profil</a>
</div>
@endsection
