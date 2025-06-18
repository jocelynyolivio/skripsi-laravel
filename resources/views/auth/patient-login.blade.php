@extends('layouts.main')

@section('container')
<style>
    body {
        background-color: #f9f9f9;
    }

    .login-card {
        background: white;
        border-radius: 12px;
        padding: 40px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .login-title {
        font-weight: 600;
        color: #4b5320;
        /* olive tone */
    }

    .btn-login {
        background-color: #6c7344;
        /* olive tone */
        color: white;
        border: none;
        padding: 10px;
        border-radius: 6px;
        transition: background-color 0.3s ease;
    }

    .btn-login:hover {
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
            <main class="login-card">
                <h2 class="mb-4 text-center login-title">Patient Login</h2>

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
                    <div class="form-floating mb-3 position-relative">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        <label for="password">Password</label>
                        <span class="position-absolute top-50 end-0 translate-middle-y me-3" onclick="togglePassword()" style="cursor: pointer;">
                            <i id="togglePasswordIcon" class="bi bi-eye-slash"></i>
                        </span>
                    </div>


                    <!-- TOMBOL LOGIN -->
                    <button type="submit" class="btn-login w-100 mt-2">Login</button>
                </form>

                <div class="text-center">
                    <small class="d-block mt-3">
                        Don't have an account? <a href="/patient/register">Register here.</a>
                    </small>
                    <small class="d-block mt-2">
                        <a href="{{ route('patient.password.request') }}">Forgot your password?</a>
                    </small>
                </div>
            </main>
        </div>
    </div>
</div>
<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const icon = document.getElementById('togglePasswordIcon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        }
    }
</script>

@endsection