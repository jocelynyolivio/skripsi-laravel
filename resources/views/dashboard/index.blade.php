@extends('dashboard.layouts.main')

@section('container')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Welcome Back, {{ $user->name }} as {{ $role }}</h1>
</div>

@if ($role === 'manager')
<!-- Data Kunjungan Pasien & Omzet Hari Ini -->
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title">Kunjungan Pasien Hari Ini</h5>
                <p class="card-text display-5 fw-bold" id='jumlahPasienHariIni'>{{ $jumlahPasienHariIni }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title">Pendapatan Hari Ini</h5>
                <p class="card-text display-5 fw-bold" id='pendapatanHariIni'>Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Filter Data Pasien -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title">Filter Data Pasien</h5>
                <form method="GET" action="{{ route('dashboard') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <input type="number" name="usia_min" class="form-control" placeholder="Usia Minimal">
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="usia_max" class="form-control" placeholder="Usia Maksimal">
                        </div>
                        <div class="col-md-3">
                            <select name="jenis_kelamin" class="form-control">
                                <option value="">Jenis Kelamin</option>
                                <option value="Male">Laki-laki</option>
                                <option value="Female">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="domisili" class="form-control" placeholder="Domisili">
                        </div>
                        <div class="col-md-3 mt-2">
                            <input type="text" name="jasa" class="form-control" placeholder="Jasa">
                        </div>
                        <div class="col-md-3 mt-2">
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Hasil Filter -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title">Hasil Filter Pasien</h5>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Usia</th>
                            <th>Jenis Kelamin</th>
                            <th>Domisili</th>
                            <th>Jasa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($filteredPatients as $pasien)
                        <tr>
                            <td>{{ $pasien->name }}</td>
                            <td>{{ \Carbon\Carbon::parse($pasien->date_of_birth)->age }} Tahun</td>
                            <td>{{ $pasien->gender }}</td>
                            <td>{{ $pasien->home_city }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Grafik Rata-rata Kunjungan Pasien dalam 1 Bulan -->
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title">Tren Kunjungan Pasien dalam 1 Bulan</h5>
                <canvas id="chartKunjungan"></canvas>
            </div>
        </div>
    </div>
</div>

@endif

@if ($role === 'admin')
<!-- Data Admin -->
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title">Pasien Akan Datang Minggu Ini</h5>
                <p class="card-text display-5 fw-bold" id='pasienAkanDatang'>{{ $pasienAkanDatang }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title">Pasien Perlu Reminder</h5>
                <p class="card-text display-5 fw-bold" id="pasienPerluReminder">{{ $pasienPerluReminder }}</p>
            </div>
        </div>
    </div>
</div>
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title">Daftar Pasien yang Belum Konfirmasi</h5>
                <div id="reservationTableContainer">
                    <p class="text-muted">Memuat data...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@if ($role === 'dokter tetap')
<!-- Data Dokter Tetap -->
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title">Pasien Akan Datang Hari Ini</h5>
                <p class="card-text display-5 fw-bold" id='pasienAkanDatangDokter'>{{ $pasienAkanDatang }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title">Rekam Medis Belum Diisi</h5>
                <p class="card-text display-5 fw-bold" id='rekamMedisBelumDiisi'>{{$rekamMedisBelumDiisi}}</p>
            </div>
        </div>
    </div>
</div>
<div class="card mt-4 shadow-sm">
    <div class="card-body">
        <h5 class="card-title">Rekam Medis Belum Diisi</h5>
        <div id="rekamMedisBelumDiisiContainer">
            <p class="text-muted">Memuat data...</p>
        </div>
    </div>
</div>

@endif
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<script>
    $(document).ready(function() {
        const renderPasienBelumKonfirmasi = (reservasiList) => {
            if (reservasiList.length === 0) {
                $('#reservationTableContainer').html('<p class="text-success">Tidak ada pasien yang perlu dikonfirmasi ✅</p>');
                return;
            }

            let html = `
        <div class="table-responsive">
            <table class="table table-striped" id="reservationTable">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Reservation Date</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>WA Confirmation</th>
                    </tr>
                </thead>
                <tbody>
    `;

            reservasiList.forEach(reservasi => {
                html += `
            <tr>
                <td>${reservasi.patient?.name || '-'}</td>
                <td>${reservasi.doctor?.name || '-'}</td>
                <td>${reservasi.tanggal_reservasi}</td>
                <td>${reservasi.jam_mulai}</td>
                <td>${reservasi.jam_selesai}</td>
                <td>
                    <a href="${reservasi.whatsapp_url}" class="btn btn-sm btn-success" target="_blank">Chat Pasien</a>
                    ${reservasi.status_konfirmasi !== 'Sudah Dikonfirmasi' ? `
                    <a href="${reservasi.whatsapp_confirm_url}" class="btn btn-sm btn-primary wa-confirmation">Konfirmasi WA</a>` : ''}
                </td>
            </tr>
        `;
            });

            html += `</tbody></table></div>`;
            $('#reservationTableContainer').html(html);

            $('#reservationTable').DataTable({
                responsive: true,
                pageLength: 5
            });
        };



        const renderRekamMedisBelumDiisi = (records) => {
            if (records.length === 0) {
                $('#rekamMedisBelumDiisiContainer').html('<p class="text-success">Semua rekam medis sudah diisi 🎉</p>');
                return;
            }

            let html = `
        <div class="table-responsive">
            <table class="table table-sm table-bordered mb-0">
                <thead>
                    <tr>
                        <th>Tanggal Reservasi</th>
                        <th>Nama Pasien</th>
                        <th>Dokter</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
    `;

            records.forEach(record => {
                const tanggal = record.tanggal_reservasi || '-';
                const pasien = record.patient?.name || '-';
                const dokter = record.doctor?.name || '-';
                const editUrl = `/dashboard/patients/${record.patient_id}/medical_records/${record.id}/edit`;


                html += `
            <tr>
                <td>${tanggal}</td>
                <td>${pasien}</td>
                <td>${dokter}</td>
                <td><a href="${editUrl}" class="btn btn-sm btn-warning">Isi Rekam Medis</a></td>
            </tr>
        `;
            });

            html += `</tbody></table></div>`;
            $('#rekamMedisBelumDiisiContainer').html(html);
        };


        function fetchData() {
            // Check the user's role to determine what data to fetch
            let role = '{{ $role }}'; // Get the role from the Blade template

            // Based on the role, send an AJAX request
            if (role === 'manager') {
                $.ajax({
                    url: '{{ route("dashboard") }}', // Ensure this route points to your controller method
                    method: 'GET',
                    success: function(response) {
                        // Update Manager-specific data
                        $('#jumlahPasienHariIni').text(response.jumlahPasienHariIni);
                        $('#pendapatanHariIni').text('Rp ' + response.pendapatanHariIni);

                        const labels = response.kunjunganBulanan.map(item => item.date);
                        const data = response.kunjunganBulanan.map(item => item.jumlah);

                        const ctx = document.getElementById('chartKunjungan').getContext('2d');
                        const chartKunjungan = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Jumlah Kunjungan',
                                    data: data,
                                    fill: true,
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                                    tension: 0.3
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'top',
                                    },
                                    title: {
                                        display: true,
                                        text: 'Grafik Tren Kunjungan Pasien Bulanan',
                                        font: {
                                            size: 18
                                        }
                                    },
                                    datalabels: {
                                        anchor: 'center',
                                        align: 'center',
                                        color: '#000',
                                        font: {
                                            weight: 'bold',
                                            size: 12
                                        },
                                        formatter: function(value) {
                                            return value;
                                        }
                                    }
                                },
                                interaction: {
                                    mode: 'index',
                                    intersect: false,
                                },
                                scales: {
                                    x: {
                                        title: {
                                            display: true,
                                            text: 'Tanggal'
                                        }
                                    },
                                    y: {
                                        title: {
                                            display: true,
                                            text: 'Jumlah Kunjungan'
                                        },
                                        beginAtZero: true
                                    }
                                }
                            },
                            plugins: [ChartDataLabels]
                        });

                    },
                    error: function(xhr, status, error) {
                        console.log("Error fetching data: " + error);
                    }
                });
            } else if (role === 'admin') {
                $.ajax({
                    url: '{{ route("dashboard") }}',
                    method: 'GET',
                    success: function(response) {
                        // Update Admin-specific data
                        $('#pasienAkanDatang').text(response.pasienAkanDatang);
                        $('#pasienPerluReminder').text(response.pasienPerluReminder);
                        renderPasienBelumKonfirmasi(response.pasienReminderList);

                    },
                    error: function(xhr, status, error) {
                        console.log("Error fetching data: " + error);
                    }
                });
            } else if (role === 'dokter tetap') {
                $.ajax({
                    url: '{{ route("dashboard") }}',
                    method: 'GET',
                    success: function(response) {
                        // Update Dokter-specific data
                        $('#pasienAkanDatangDokter').text(response.pasienAkanDatang);
                        $('#rekamMedisBelumDiisi').text(response.rekamMedisBelumDiisi);
                        renderRekamMedisBelumDiisi(response.listRekamMedisBelumDiisi);

                    },
                    error: function(xhr, status, error) {
                        console.log("Error fetching data: " + error);
                    }
                });
            }
        }

        // Fetch data initially
        fetchData();

        // Set an interval to refresh data every 5 seconds
        setInterval(fetchData, 5000); // Refresh data every 5 seconds
    });

    // Confirmation on WA button click
    $('#reservationTableContainer').on('click', '.wa-confirmation', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        Swal.fire({
            title: 'Yakin sudah melakukan konfirmasi WA?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, sudah konfirmasi!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });
</script>
@endsection