@extends('layouts.main')

@section('container')
<style>
    body {
        background-color: #f9f9f9;
    }

    .register-card {
        background: white;
        border-radius: 12px;
        padding: 40px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .register-title {
        font-weight: 600;
        color: #4b5320; /* olive tone */
    }

    .btn-register {
        background-color: #6c7344; /* olive tone */
        color: white;
        border: none;
        padding: 10px;
        border-radius: 6px;
        transition: background-color 0.3s ease;
    }

    .btn-register:hover {
        background-color: #5b613a;
    }

    a {
        color: #6c7344;
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }
</style>

<div class="container mt-5">
    <div class="row justify-content-center text-center">
        <div class="col-md-6">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <main class="register-card">
                <h2 class="mb-4 text-center register-title">Patient Registration</h2>

                <form action="/patient/register" method="post">
                    @csrf
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control @error('fname') is-invalid @enderror" id="fname" name="fname" placeholder="First Name" required value="{{ old('fname') }}">
                        <label for="fname">First Name</label>
                        @error('fname')
                        <div class="invalid-feedback">Please input first name.</div>
                        @enderror
                    </div>

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control @error('mname') is-invalid @enderror" id="mname" name="mname" placeholder="Middle Name" value="{{ old('mname') }}">
                        <label for="mname">Middle Name</label>
                        @error('mname')
                        <div class="invalid-feedback">Please input middle name.</div>
                        @enderror
                    </div>

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control @error('lname') is-invalid @enderror" id="lname" name="lname" placeholder="Last Name" value="{{ old('lname') }}">
                        <label for="lname">Last Name</label>
                        @error('lname')
                        <div class="invalid-feedback">Please input last name.</div>
                        @enderror
                    </div>

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control @error('home_mobile') is-invalid @enderror" id="home_mobile" name="home_mobile" placeholder="Mobile Phone" required value="{{ old('home_mobile') }}">
                        <label for="home_mobile">Mobile Phone</label>
                        @error('home_mobile')
                        <div class="invalid-feedback">Please input nomor telepon.</div>
                        @enderror
                    </div>

                    <div class="form-floating mb-3">
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Email" required value="{{ old('email') }}">
                        <label for="email">Email address</label>
                        @error('email')
                        <div class="invalid-feedback">Please input a valid email.</div>
                        @enderror
                    </div>

                    <div class="form-floating mb-3">
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Password" required>
                        <label for="password">Password</label>
                        @error('password')
                        <div class="invalid-feedback">Password must be at least 5 characters.</div>
                        @enderror
                    </div>

                    <button class="btn-register w-100 mt-2" type="submit">Register</button>
                </form>

                <small class="d-block mt-3">
                    Already have an account? <a href="/patient/login">Login here.</a>
                </small>
            </main>
        </div>
    </div>
</div>
@endsection
