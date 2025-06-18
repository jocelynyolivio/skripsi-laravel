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
                <h2 class="mb-4 text-center form-title">Forgot Password</h2>

                @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
                @endif

                <form method="POST" action="{{ route('patient.password.email') }}">
                    @csrf
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="name@example.com" value="{{ old('email') }}" required autofocus>
                        <label for="email">Email address</label>
                        @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <button type="submit" class="btn-submit w-100 mt-2">Send Password Reset Link</button>
                </form>

                <div class="text-center">
                    <small class="d-block mt-3">
                        Remember your password? <a href="{{ route('patient.login') }}">Login here.</a>
                    </small>
                </div>
            </main>
        </div>
    </div>
</div>
@endsection