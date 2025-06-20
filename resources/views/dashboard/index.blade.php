@extends('dashboard.layouts.main')

@section('container')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Welcome Back, {{ $user->name }} as {{ $role }}</h1>
    <a class="{{ Request::is('dashboard/profile*') ? 'active' : '' }}" href="/dashboard/profile">
        <i class="bi bi-person-circle me-3 fs-5" style="color: black; font-weight: bold;"></i>
    </a>
</div>

@if ($role === 'manager')
<!-- Patient Visits & Today's Revenue -->
<div class="row">
    <div class="col-md-4 mb-4">
        <a href="{{ route('dashboard.reservations.index') }}" class="text-decoration-none card-hover-effect">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="card-title">Today's Patient Visits</h5>
                    <p class="card-text display-5 fw-bold" id='jumlahPasienHariIni'>{{ $jumlahPasienHariIni }}</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4 mb-4">
        <a href="{{ route('dashboard.transactions.index') }}" class="text-decoration-none card-hover-effect">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="card-title">Today's Revenue</h5>
                    <p class="card-text display-5 fw-bold" id='pendapatanHariIni'>Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4 mb-4">
        <a href="{{ route('dashboard.purchase_requests.index') }}" class="text-decoration-none card-hover-effect">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="card-title">Unapproved Purchase Request</h5>
                    <p class="card-text display-5 fw-bold" id='purchaseRequestBelumApprove'>{{ $purchaseRequestBelumApprove }}</p>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Charts: Patient Visit (left) & Doctor Performance (right) -->
<div class="row">
    <!-- Kunjungan Pasien -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title">Patient Visit Trends (1 Month)</h5>
                <canvas id="chartKunjungan" height="250"></canvas>
            </div>
        </div>
    </div>

    <!-- Performa Dokter -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title">Doctor Performance (Omzet)</h5>
                <canvas id="performaDokterChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>
<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">Rekap Pasien per Procedure Type ({{ now()->format('F Y') }})</h5>
    </div>
    <div class="card-body">
        <!-- <table id="tableTipeProsedur" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Procedure Type</th>
                    <th>Jumlah Pasien</th>
                    <th>Jumlah Prosedur</th>
                    <th>Nomor Medical Record</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table> -->
        <table id="tableStatistik" class="table table-striped table-bordered w-100">
            <thead>
                <tr>
                    <th>Procedure Type</th>
                    <th>Total Patients</th>
                    <th>Male Patients</th>
                    <th>Female Patients</th>
                    <th>Total Revenue</th>
                    <th>Male Revenue</th>
                    <th>Female Revenue</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

    </div>
</div>


@endif


@if ($role === 'admin')
<!-- Admin Data -->
<div class="row">
    <!-- Patients Coming This Week -->
    <div class="col-md-3 mb-4">
        <a href="{{ route('dashboard.masters.patients.birthday') }}" class="text-decoration-none card-hover-effect">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="card-title">Patients Birthday Today</h5>
                    <p class="card-text display-5 fw-bold" id="pasienUltah">{{ $pasienUltah }}</p>
                    <p class="text-muted">
                        {{ $pasienUltah < 1 ? 'No patients birthday today' : 'Click to see data' }}
                    </p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-3 mb-4">
        <a href="{{ route('dashboard.reservations.index') }}" class="text-decoration-none card-hover-effect">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="card-title">Patients Coming This Week</h5>
                    <p class="card-text display-5 fw-bold" id="pasienAkanDatang">{{ $pasienAkanDatang }}</p>
                    <p class="text-muted">
                        {{ $pasienAkanDatang < 1 ? 'No patients coming this week' : 'Click to see data' }}
                    </p>
                </div>
            </div>
        </a>
    </div>

    <!-- Patients Needing Reminder -->
    <div class="col-md-3 mb-4">
        <a href="{{ route('dashboard.reservations.index') }}" class="text-decoration-none card-hover-effect">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="card-title">Patients Needing Reminder</h5>
                    <p class="card-text display-5 fw-bold" id="pasienPerluReminder">{{ $pasienPerluReminder }}</p>
                    <p class="text-muted">
                        {{ $pasienPerluReminder < 1 ? 'No patients need reminders' : 'See data below' }}
                    </p>
                </div>
            </div>
        </a>
    </div>

    <!-- Low Stock Materials -->
    <div class="col-md-3 mb-4">
        <a href="{{ route('dashboard.stock_cards.index') }}" class="text-decoration-none card-hover-effect">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="card-title">Low Stock Materials</h5>
                    <p class="card-text display-5 fw-bold">{{ $lowStockItems->count() }} items</p>
                    @if($lowStockItems->count() > 0)
                    <ul class="mb-0">
                        @foreach($lowStockItems as $item)
                        <li>{{ $item->dentalMaterial->name ?? 'Unknown Material' }}</li>
                        @endforeach
                    </ul>
                    @else
                    <p class="text-muted">All stock is safe</p>
                    @endif
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Unconfirmed Patient List -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title">Unconfirmed Patient List</h5>
                <div id="reservationTableContainer">
                    <p class="text-muted">Loading data...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Doctor Performance Chart -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title">Doctor Performance (Omzet)</h5>
                <canvas id="performaDokterChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

@endif

@if ($role === 'dokter tetap')
<!-- Permanent Doctor Data -->
<div class="row">
    <div class="col-md-6 mb-4">
        <a href="{{ route('dashboard.reservations.index') }}" class="text-decoration-none card-hover-effect">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="card-title">Patients Coming Today</h5>
                    <p class="card-text display-5 fw-bold" id='pasienAkanDatangDokter'>{{ $pasienAkanDatang }}</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6 mb-4">
        <a href="{{ route('dashboard.medical_records.select')}}" class="text-decoration-none card-hover-effect">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h5 class="card-title">Unfilled Medical Records</h5>
                    <p class="card-text display-5 fw-bold" id='rekamMedisBelumDiisi'>{{$rekamMedisBelumDiisi}}</p>
                </div>
            </div>
        </a>
    </div>
</div>
<div class="card mt-4 shadow-sm">
    <div class="card-body">
        <h5 class="card-title">Unfilled Medical Records</h5>
        <div id="rekamMedisBelumDiisiContainer">
            <p class="text-muted">Loading data...</p>
        </div>
    </div>
</div>
<div class="row">
    <!-- Performa Dokter -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title">Doctor Performance (Omzet)</h5>
                <canvas id="performaDokterChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>
@endif

<style>
    /* Consistent hover effect for all clickable cards */
    .card-hover-effect:hover .card {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15) !important;
        border-color: #0d6efd !important;
    }

    .card-hover-effect .card {
        transition: all 0.3s ease;
    }

    .card-hover-effect {
        display: block;
    }
</style>
<!-- <script src="{{ asset('assets/js/chart.js') }}"></script>
<script src="{{ asset('assets/js/chartjs-plugin-datalabels.min.js') }}"></script> -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
    function formatRupiah(value) {
        if (!value) return 'Rp 0';
        return 'Rp ' + parseInt(value).toLocaleString('id-ID');
    }

    $(document).ready(function() {
        const renderPasienBelumKonfirmasi = (reservasiList) => {
            if (reservasiList.length === 0) {
                $('#reservationTableContainer').html('<p class="text-success">No patients need confirmation ✅</p>');
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
<td>${([reservasi.patient?.fname, reservasi.patient?.mname, reservasi.patient?.lname].filter(Boolean).join(' ')) || '-'}</td>                <td>${reservasi.doctor?.name || '-'}</td>
                <td>${reservasi.tanggal_reservasi}</td>
                <td>${reservasi.jam_mulai}</td>
                <td>${reservasi.jam_selesai}</td>
                <td>
                    <a href="${reservasi.whatsapp_url}" class="btn btn-sm btn-success" target="_blank">Chat Patient</a>
                    ${reservasi.status_konfirmasi !== 'Sudah Dikonfirmasi' ? `
                    <a href="${reservasi.whatsapp_confirm_url}" class="btn btn-sm btn-primary wa-confirmation">Confirm via WA</a>` : ''}
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
        $('#rekamMedisBelumDiisiContainer').html('<p class="text-success">All medical records are complete 🎉</p>');
        return;
    }

    let html = `
    <div class="table-responsive">
        <table id="unfilledRecordsTable" class="table table-sm table-bordered mb-0">
            <thead>
                <tr>
                    <th>Reservation Date</th>
                    <th>Patient Name</th>
                    <th>Doctor</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>`;

    records.forEach(record => {
        const tanggal = record.tanggal_reservasi || '-';
        const p = record.patient;
        const pasien = p && (p.fname || p.mname || p.lname) ?
            [p.fname, p.mname, p.lname].filter(Boolean).join(' ') :
            '-';

        const dokter = record.doctor?.name || '-';
        const editUrl = `/dashboard/patients/${record.patient_id}/medical_records/${record.id}/edit`;

        html += `
        <tr>
            <td>${tanggal}</td>
            <td>${pasien}</td>
            <td>${dokter}</td>
            <td><a href="${editUrl}" class="btn btn-sm btn-warning">Fill Medical Record</a></td>
        </tr>`;
    });

    html += `</tbody></table></div>`;
    
    $('#rekamMedisBelumDiisiContainer').html(html);
    
    // Initialize DataTable after the table is rendered
    $('#unfilledRecordsTable').DataTable({
        responsive: true,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search records...",
        },
        dom: '<"top"f>rt<"bottom"lip><"clear">',
        pageLength: 5,
        lengthMenu: [5, 10, 20, 50]
    });
};

        function fetchData() {
            let role = '{{ $role }}';

            if (role === 'manager') {
                $.ajax({
                    url: '{{ route("dashboard") }}',
                    method: 'GET',
                    success: function(response) {
                        $('#jumlahPasienHariIni').text(response.jumlahPasienHariIni);
                        $('#pendapatanHariIni').text('Rp ' + response.pendapatanHariIni);

                        // === TABEL TIPE PROSEDUR ===
                        // let table = $('#tableTipeProsedur').DataTable();
                        // table.clear().draw();

                        // response.tipeProsedur.forEach(function(item) {
                        //     table.row.add([
                        //         item.procedure_type,
                        //         item.total_patients,
                        //         item.total_procedures, // ✅ Tambahan
                        //         item.medical_record_refs
                        //     ]).draw(false);
                        // });

                        let table = $('#tableStatistik').DataTable({
                            destroy: true,
                            searching: false,
                            paging: false,
                            info: false,
                            ordering: false
                        });

                        table.clear();

                        response.statistik.forEach(function(item) {
                            table.row.add([
                                item.procedure_type,
                                item.total_patients,
                                item.male_patients,
                                item.female_patients,
                                formatRupiah(item.total_revenue),
                                formatRupiah(item.male_revenue),
                                formatRupiah(item.female_revenue)
                            ]);
                        });

                        table.draw();




                        const labels = response.kunjunganBulanan.map(item => item.date);
                        const data = response.kunjunganBulanan.map(item => item.jumlah);

                        const ctx = document.getElementById('chartKunjungan').getContext('2d');
                        const chartKunjungan = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Visit Count',
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
                                        text: 'Monthly Patient Visit Trend Chart',
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
                                            text: 'Date'
                                        }
                                    },
                                    y: {
                                        title: {
                                            display: true,
                                            text: 'Visit Count'
                                        },
                                        beginAtZero: true
                                    }
                                }
                            },
                            plugins: [ChartDataLabels]
                        });

                        // === CHART PERFORMA DOKTER ===
                        const dokterLabels = response.performaDokter.map(item => item.doctor_name);
                        const dokterData = response.performaDokter.map(item => item.total_amount);

                        const ctxDokter = document.getElementById('performaDokterChart').getContext('2d');
                        const chartDokter = new Chart(ctxDokter, {
                            type: 'bar',
                            data: {
                                labels: dokterLabels,
                                datasets: [{
                                    label: 'Total Pendapatan (Rp)',
                                    data: dokterData,
                                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            callback: function(value) {
                                                return 'Rp ' + value.toLocaleString('id-ID');
                                            }
                                        }
                                    }
                                },
                                plugins: {
                                    datalabels: {
                                        anchor: 'end',
                                        align: 'top',
                                        formatter: function(value) {
                                            return 'Rp ' + value.toLocaleString('id-ID');
                                        },
                                        font: {
                                            weight: 'bold'
                                        }
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                            }
                                        }
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
                        $('#pasienAkanDatang').text(response.pasienAkanDatang);
                        $('#pasienUltah').text(response.pasienUltah);
                        $('#pasienPerluReminder').text(response.pasienPerluReminder);
                        renderPasienBelumKonfirmasi(response.pasienReminderList);

                        // === CHART PERFORMA DOKTER ===
                        const dokterLabels = response.performaDokter.map(item => item.doctor_name);
                        const dokterData = response.performaDokter.map(item => item.total_amount);

                        const ctxDokter = document.getElementById('performaDokterChart').getContext('2d');
                        const chartDokter = new Chart(ctxDokter, {
                            type: 'bar',
                            data: {
                                labels: dokterLabels,
                                datasets: [{
                                    label: 'Total Pendapatan (Rp)',
                                    data: dokterData,
                                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            callback: function(value) {
                                                return 'Rp ' + value.toLocaleString('id-ID');
                                            }
                                        }
                                    }
                                },
                                plugins: {
                                    datalabels: {
                                        anchor: 'end',
                                        align: 'top',
                                        formatter: function(value) {
                                            return 'Rp ' + value.toLocaleString('id-ID');
                                        },
                                        font: {
                                            weight: 'bold'
                                        }
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                            }
                                        }
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
            } else if (role === 'dokter tetap') {
                $.ajax({
                    url: '{{ route("dashboard") }}',
                    method: 'GET',
                    success: function(response) {
                        $('#pasienAkanDatangDokter').text(response.pasienAkanDatang);
                        $('#rekamMedisBelumDiisi').text(response.rekamMedisBelumDiisi);
                        renderRekamMedisBelumDiisi(response.listRekamMedisBelumDiisi);

                        // === CHART PERFORMA DOKTER ===
                        const dokterLabels = response.performaDokter.map(item => item.doctor_name);
                        const dokterData = response.performaDokter.map(item => item.total_amount);

                        const ctxDokter = document.getElementById('performaDokterChart').getContext('2d');
                        const chartDokter = new Chart(ctxDokter, {
                            type: 'bar',
                            data: {
                                labels: dokterLabels,
                                datasets: [{
                                    label: 'Total Pendapatan (Rp)',
                                    data: dokterData,
                                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            callback: function(value) {
                                                return 'Rp ' + value.toLocaleString('id-ID');
                                            }
                                        }
                                    }
                                },
                                plugins: {
                                    datalabels: {
                                        anchor: 'end',
                                        align: 'top',
                                        formatter: function(value) {
                                            return 'Rp ' + value.toLocaleString('id-ID');
                                        },
                                        font: {
                                            weight: 'bold'
                                        }
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                            }
                                        }
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
            }
        }

        // Fetch data initially
        fetchData();

        // Set an interval to refresh data every 5 seconds
        setInterval(fetchData, 5000);
    });

    // Confirmation on WA button click
    $('#reservationTableContainer').on('click', '.wa-confirmation', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        Swal.fire({
            title: 'Have you confirmed via WhatsApp?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, already confirmed!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });

    $('#tableTipeProsedur').DataTable({
        paging: true,
        searching: false,
        info: false,
        ordering: false,
        language: {
            emptyTable: "Tidak ada data untuk bulan ini"
        }
    });
</script>

@endsection