@extends('dashboard.layouts.main')
@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['text' => 'Profile']
        ]
    ])
@endsection

@section('container')
<div class="container mt-5">
    <h1 class="h2">My Profile</h1>
    <hr>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Menampilkan informasi profil pengguna -->
    <div class="mb-3">
        <label class="form-label"><strong>Name:</strong></label>
        <p>{{ $user->name }}</p>
    </div>

    <div class="mb-3">
        <label class="form-label"><strong>Email:</strong></label>
        <p>{{ $user->email }}</p>
    </div>

    <!-- Tombol Edit Profil -->
    <a href="{{ route('dashboard.profile.edit') }}" class="btn btn-warning">Edit Profile</a>
</div>
@endsection
