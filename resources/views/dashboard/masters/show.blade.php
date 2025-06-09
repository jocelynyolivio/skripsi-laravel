@extends('dashboard.layouts.main')

@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['text' => 'Master Users', 'url' => route('dashboard.masters.index')],
            ['text' => 'Detail User'],
        ],
    ])
@endsection

@section('container')
    <div class="container mt-5 col-md-8">
        <h3 class="my-4">Detail User: {{ $user->name }}</h3>

        @if ($user->photo)
            <div class="text-center mb-4">
                <img src="{{ asset('storage/' . $user->photo) }}" class="img-thumbnail rounded-circle" alt="Foto Profil"
                    width="150">
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-header">
                <h5>Informasi Dasar</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Nama Lengkap</label>
                        <p class="form-control-plaintext">{{ $user->name }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <p class="form-control-plaintext">{{ $user->email }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Role</label>
                        <p class="form-control-plaintext">
                            @if ($user->role_id == 1)
                                Admin
                            @elseif($user->role_id == 2)
                                Doctor
                            @elseif($user->role_id == 3)
                                Manager
                            @else
                                -
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5>Detail Pribadi</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Tempat Lahir</label>
                        <p class="form-control-plaintext">{{ $user->tempat_lahir ?? '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Tanggal Lahir</label>
                        <p class="form-control-plaintext">
                            {{ $user->tanggal_lahir ? \Carbon\Carbon::parse($user->tanggal_lahir)->format('d F Y') : '-' }}
                        </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">NIK (Nomor Induk Kependudukan)</label>
                        <p class="form-control-plaintext">{{ $user->nik ?? '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Nomor Telepon</label>
                        <p class="form-control-plaintext">{{ $user->nomor_telepon ?? '-' }}</p>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Alamat</label>
                    <p class="form-control-plaintext" style="white-space: pre-wrap;">{{ $user->alamat ?? '-' }}</p>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5>Informasi Profesional</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Tanggal Bergabung</label>
                        <p class="form-control-plaintext">
                            {{ $user->tanggal_bergabung ? \Carbon\Carbon::parse($user->tanggal_bergabung)->format('d F Y') : '-' }}
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Nomor SIP</label>
                        <p class="form-control-plaintext">{{ $user->nomor_sip ?? '-' }}</p>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Nomor Rekening Bank</label>
                    <p class="form-control-plaintext">{{ $user->nomor_rekening ?? '-' }}</p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Deskripsi</label>
                    <p class="form-control-plaintext" style="white-space: pre-wrap;">{{ $user->deskripsi ?? '-' }}</p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Status</label>
                    <p class="form-control-plaintext">
                        @if ($user->is_active)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-danger">Tidak Aktif</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <a href="{{ route('dashboard.masters.index') }}" class="btn btn-secondary px-4">Kembali</a>
        </div>
    </div>
@endsection