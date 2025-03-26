@extends('layouts.main')

@section('container')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <main class="form-registration w-100 m-auto" style="border: 1px solid #ccc; border-radius: 5px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); padding: 30px;">
                <h2 class="mb-4 text-center">Patient Login</h2>
                @if(session('loginError'))
                    <div class="alert alert-danger">
                        {{ session('loginError') }}
                    </div>
                @endif
                <form method="POST" action="{{ route('patient.login') }}">
                    @csrf
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                        <label for="email">Email</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        <label for="password">Password</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 mt-3">Login</button>
                </form>
                <div class="text-center">
                    <small class="d-block mt-3">
                        Don't have an account? <a href="/patient/register">Register here.</a>
                    </small>
                </div>
            </main>
        </div>
    </div>
</div>
@endsection
