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
        color: #4b5320;
        /* olive tone */
    }

    .btn-register {
        background-color: #6c7344;
        /* olive tone */
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

    .required-star {
        color: red;
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

                <form action="/patient/register" method="post" id="registrationForm">
                    @csrf
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control @error('fname') is-invalid @enderror" id="fname" name="fname" placeholder="First Name" required value="{{ old('fname') }}">
                        <label for="fname">First Name <span class="required-star">*</span></label>
                        @error('fname')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control @error('mname') is-invalid @enderror" id="mname" name="mname" placeholder="Middle Name" value="{{ old('mname') }}">
                        <label for="mname">Middle Name</label>
                        @error('mname')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control @error('lname') is-invalid @enderror" id="lname" name="lname" placeholder="Last Name" value="{{ old('lname') }}">
                        <label for="lname">Last Name</label>
                        @error('lname')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control @error('home_mobile') is-invalid @enderror"
                               id="home_mobile" name="home_mobile" placeholder="Mobile Phone" required
                               value="{{ old('home_mobile', '62') }}">
                        <label for="home_mobile">Mobile Phone <span class="required-star">*</span></label>
                        @error('home_mobile')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        {{-- Element untuk pesan error dari JavaScript --}}
                        <div class="invalid-feedback" id="home_mobile_js_error_message"></div>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Email" required value="{{ old('email') }}">
                        <label for="email">Email address <span class="required-star">*</span></label>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-floating mb-3 position-relative">
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Password" required>
                        <label for="password">Password <span class="required-star">*</span></label>
                        <span class="position-absolute top-50 end-0 translate-middle-y me-3" onclick="togglePassword()" style="cursor: pointer;">
                            <i id="togglePasswordIcon" class="bi bi-eye-slash"></i>
                        </span>
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
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

    const mobileInput = document.getElementById('home_mobile');
    const initialPrefix = '62';

    // Fungsi untuk memastikan input selalu dimulai dengan '62' dan hanya berisi angka setelahnya
    function sanitizeMobileInput() {
        let value = mobileInput.value;
        if (!value.startsWith(initialPrefix)) {
            // Jika pengguna mencoba menghapus '62' atau '62' tidak ada di awal
            const numericPart = value.replace(/\D/g, ''); // Ambil semua digit
            // Gabungkan kembali dengan prefix, hilangkan '62' jika pengguna mengetiknya lagi setelah prefix
            mobileInput.value = initialPrefix + numericPart.replace(/^62/, '');
        } else {
            // Jika sudah diawali '62', pastikan hanya angka yang mengikuti
            const afterPrefix = value.substring(initialPrefix.length);
            mobileInput.value = initialPrefix + afterPrefix.replace(/\D/g, '');
        }
    }

    // Panggil sanitize saat ada input
    mobileInput.addEventListener('input', sanitizeMobileInput);

    // Panggil sanitize saat field kehilangan fokus (blur) untuk memastikan format akhir
    mobileInput.addEventListener('blur', sanitizeMobileInput);


    // Inisialisasi nilai jika field kosong dan tidak ada old value
    // (value dari blade `old('home_mobile', '62')` sudah menangani ini pada page load)
    if (mobileInput.value === '') { // Ini terjadi jika old('home_mobile') adalah string kosong
         mobileInput.value = initialPrefix;
    } else if (!mobileInput.value.startsWith(initialPrefix)) { // Jika old() ada tapi tidak dimulai 62
        sanitizeMobileInput(); // Coba perbaiki saat load
    }


    document.getElementById('registrationForm').addEventListener('submit', function(e) {
        const mobileValue = mobileInput.value;
        const jsErrorElement = document.getElementById('home_mobile_js_error_message');
        const isServerErrorActive = mobileInput.classList.contains('is-invalid') && mobileInput.parentElement.querySelector('.invalid-feedback.d-block');

        // Regex: harus diawali '62' dan diikuti 9 sampai 13 digit angka.
        // Jika Anda ingin spesifik "minimal 11 angka dibelakangnya": /^62\d{11,}$/
        // Jika Anda ingin spesifik "tepat 11 angka dibelakangnya": /^62\d{11}$/
        const pattern = /^62\d{9,13}$/;

        // Bersihkan pesan error JS sebelumnya
        mobileInput.classList.remove('is-invalid'); // Hapus dulu, tambahkan lagi jika error
        if (jsErrorElement) {
            jsErrorElement.textContent = '';
            jsErrorElement.style.display = 'none';
        }

        if (!pattern.test(mobileValue)) {
            e.preventDefault(); // Hentikan submit form

            // Tampilkan pesan error JS hanya jika tidak ada pesan error dari server yang aktif
            if (!isServerErrorActive) {
                mobileInput.classList.add('is-invalid');
                if (jsErrorElement) {
                    jsErrorElement.textContent = 'Nomor telepon harus diawali 62 dan diikuti 9-13 digit angka (misal: 6281234567890).';
                    jsErrorElement.style.display = 'block';
                }
            } else {
                // Jika ada error server, pastikan .is-invalid tetap ada
                mobileInput.classList.add('is-invalid');
            }
        }
        // Jika valid, pesan error JS sudah dibersihkan. Validasi server akan berjalan jika form disubmit.
    });
</script>

@endsection 