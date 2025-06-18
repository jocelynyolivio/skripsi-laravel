@extends('layouts.main')

@section('container')
<style>
    body {
        background-color: #f9f9f9;
    }

    .card-form {
        background: white;
        border-radius: 12px;
        padding: 40px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .form-title {
        font-weight: 600;
        color: #4b5320;
    }

    .btn-submit {
        background-color: #6c7344;
        color: white;
        border: none;
        padding: 10px;
        border-radius: 6px;
        transition: background-color 0.3s ease;
    }

    .btn-submit:hover {
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
    <div class="row justify-content-center">
        <div class="col-md-6">
            <main class="card-form">
                <h2 class="mb-4 text-center form-title">Reset Password</h2>

                <form method="POST" action="{{ route('patient.password.update') }}">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-floating mb-3">
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="name@example.com" value="{{ $email ?? old('email') }}" required readonly>
                        <label for="email">Email address</label>
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-floating mb-3">
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="New Password" required autocomplete="new-password">
                        <label for="password">New Password</label>
                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm New Password" required autocomplete="new-password">
                        <label for="password_confirmation">Confirm New Password</label>
                    </div>

                    <button type="submit" class="btn-submit w-100 mt-2">Reset Password</button>
                </form>
            </main>
        </div>
    </div>
</div>
@endsection