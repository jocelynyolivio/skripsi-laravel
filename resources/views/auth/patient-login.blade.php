@extends('layouts.main')

@section('container')
<div class="container mt-5">
    <h2 class="mb-4">Patient Login</h2>
    @if(session('loginError'))
        <div class="alert alert-danger">
            {{ session('loginError') }}
        </div>
    @endif
    <form method="POST" action="{{ route('patient.login') }}">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
</div>
@endsection
