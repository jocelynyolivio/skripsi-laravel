@extends('dashboard.layouts.main')

@section('container')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Welcome Back, {{ $user->name }} as {{ $role ?? 'Role Not Assigned' }}</h1>
</div>

<div class="row">
    <!-- Card: Pasien Hari Ini -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title">Pasien Hari Ini</h5>
                <p class="card-text display-5 fw-bold">{{ $jumlahPasienHariIni }}</p>
                <p class="text-muted">Jumlah pasien yang datang hari ini</p>
            </div>
        </div>
    </div>

    <!-- Card: Reservasi yang Belum Diproses -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title">Reservasi Belum Diproses</h5>
                <p class="card-text display-5 fw-bold">{{ $reservasiBelumDiproses }}</p>
                <p class="text-muted">Jumlah reservasi yang menunggu konfirmasi</p>
            </div>
        </div>
    </div>

    <!-- Card: Pendapatan Hari Ini -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title">Pendapatan Hari Ini</h5>
                <p class="card-text display-5 fw-bold">Rp2.500.000</p> <!-- Ganti dengan data dari database -->
                <p class="text-muted">Total pendapatan dari pasien hari ini</p>
            </div>
        </div>
    </div>
</div>

<!-- Section Tambahan -->
<div class="row">
    <!-- Statistik Dokter -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title">Statistik Dokter</h5>
                <p class="card-text">Dokter dengan pasien terbanyak hari ini:</p>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Dokter A
                        <span class="badge bg-primary rounded-pill">5</span> <!-- Ganti dengan data dari database -->
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Dokter B
                        <span class="badge bg-primary rounded-pill">3</span> <!-- Ganti dengan data dari database -->
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Dokter C
                        <span class="badge bg-primary rounded-pill">2</span> <!-- Ganti dengan data dari database -->
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Tugas Hari Ini -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title">Tugas Hari Ini</h5>
                <ul class="list-group">
                    <li class="list-group-item">Pemeriksaan pasien (10:00 AM)</li> <!-- Ganti dengan data dari database -->
                    <li class="list-group-item">Meeting staf klinik (01:00 PM)</li> <!-- Ganti dengan data dari database -->
                    <li class="list-group-item">Follow-up pasien lama (03:00 PM)</li> <!-- Ganti dengan data dari database -->
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
