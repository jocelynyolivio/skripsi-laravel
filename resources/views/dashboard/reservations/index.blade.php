@extends('dashboard.layouts.main')
@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['text' => 'Reservation']
        ]
    ])
@endsection

@section('container')
<div class="container mt-5">
    <div class="d-flex justify-content-between mb-3">
        <h3 class="text-center">Reservations List</h3>
        <a href="{{ route('dashboard.schedules.index') }}" class="btn btn-primary mb-3">Create New Reservation</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    {{-- Filter Form --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Filter Reservations</h5>
        </div>
        <div class="card-body">
            <form id="filterReservationsForm" action="{{ route('dashboard.reservations.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-5">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100 me-2">Apply Filter</button>
                        <a href="{{ route('dashboard.reservations.index') }}" class="btn btn-secondary w-100">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <table id="reservationTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Patient</th>
                <th>Doctor</th>
                <th>Reservation Date</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reservations as $index => $reservation)
            <tr>
                <td>{{ $reservation->patient->fname }} {{ $reservation->patient->mname }} {{ $reservation->patient->lname }}</td>
                <td>{{ $reservation->doctor->name }}</td>
                <td>{{ \Carbon\Carbon::parse($reservation->tanggal_reservasi)->format('d M Y') }}</td> {{-- Format tanggal --}}
                <td>{{ $reservation->jam_mulai }}</td>
                <td>{{ $reservation->jam_selesai }}</td>
                <td>
                    @php
                    $statusClass = [
                    'Sudah Dikonfirmasi' => 'bg-success',
                    'Belum Dikonfirmasi' => 'bg-warning text-dark',
                    'Batal' => 'bg-danger',
                    'Selesai' => 'bg-primary'
                    ][$reservation->status_konfirmasi ?? 'Belum Dikonfirmasi'] ?? 'bg-secondary';
                    @endphp
                    <span class="badge {{ $statusClass }} rounded-pill">
                        {{ $reservation->status_konfirmasi ?? 'Belum Dikonfirmasi' }}
                    </span>
                </td>
                <td>
                    @if(auth()->user()?->role?->role_name === 'admin' || auth()->user()?->role?->role_name === 'manager')
                    <a href="{{ route('dashboard.reservations.whatsapp', $reservation->id) }}"
                        class="btn btn-sm btn-success"
                        target="_blank">
                        Chat Patient
                    </a>

                    @if($reservation->status_konfirmasi !== 'Sudah Dikonfirmasi')
                    <a href="{{ route('dashboard.reservations.whatsappConfirm', $reservation->id) }}" class="btn btn-sm btn-primary wa-confirmation">
                        Konfirmasi WA
                    </a>
                    @endif
                    @endif

                    @if(!$reservation->teeth_condition && !$reservation->subjective && !$reservation->objective &&!$reservation->assessment && !$reservation->plan && !$reservation->procedures()->exists())
                    <a href="{{ route('dashboard.reservations.edit', $reservation->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('dashboard.reservations.destroy', $reservation->id) }}" method="POST" style="display:inline;" class="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-sm btn-danger delete-button">Delete</button>
                    </form>
                    @else
                    <span class="badge bg-secondary">Locked</span>
                    <small class="text-muted d-block">Cannot edit/delete</small>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>

<script>
    $(document).ready(function() {
        // Inisialisasi DataTables tanpa fitur searching bawaan
        $('#reservationTable').DataTable({
            "paging": true,
            "searching": true, // Tetap aktifkan searching bawaan jika ingin search box di pojok kanan atas
            "ordering": true,
            "info": true,
            "responsive": true,
            "columnDefs": [
                { "orderable": false, "targets": [6] } // Kolom Actions tidak bisa diurutkan
            ]
        });

        // Event delegation for SweetAlert confirmation (kode yang sudah ada)
        $('#reservationTable').on('click', '.delete-button', function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        $('#reservationTable').on('click', '.wa-confirmation', function(e) {
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
    });
</script>
@endsection