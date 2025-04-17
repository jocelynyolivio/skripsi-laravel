@extends('dashboard.layouts.main')
@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['text' => 'Attendances'],
        ]
    ])
@endsection
@section('container')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h3 class="text-center">Data Presensi</h3>
        <a href="{{ route('dashboard.attendances.create') }}" class="btn btn-primary">Add Attendances</a>
    </div>

    <!-- Filter Per Bulan -->
    <form method="GET" action="{{ route('dashboard.attendances.index') }}" class="mb-3">
        <label for="bulan">Filter Bulan:</label>
        <input type="month" name="bulan" id="bulan" class="form-control d-inline-block w-auto"
               value="{{ request('bulan') }}">
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="{{ route('dashboard.attendances.index') }}" class="btn btn-secondary">Reset</a>
    </form>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($attendances->isEmpty())
        <p class="text-center mt-3">Belum ada data presensi.</p>
    @else
        <table class="table table-bordered mt-3" id="attendanceTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>No ID</th>
                    <th>Nama</th>
                    <th>Tanggal</th>
                    <th>Jam Masuk</th>
                    <th>Jam Pulang</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendances as $attendance)
                <tr>
                    <td>{{ $attendance->id }}</td>
                    <td>{{ $attendance->no_id }}</td>
                    <td>{{ $attendance->nama }}</td>
                    <td>{{ \Carbon\Carbon::parse($attendance->tanggal)->format('d M Y') }}</td>
                    <td>{{ $attendance->jam_masuk }}</td>
                    <td>{{ $attendance->jam_pulang }}</td>
                    <td>
                        <a href="{{ route('dashboard.attendances.edit', $attendance->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('dashboard.attendances.destroy', $attendance->id) }}" method="POST" class="d-inline delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-danger btn-sm delete-button">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<script>
    $(document).ready(function() {
        $('#attendanceTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,
            "columnDefs": [
                { "orderable": false, "targets": 6 } // Kolom ke-6 adalah kolom Actions
            ]
        });
    });

    // Event delegation for SweetAlert confirmation
    $('#attendanceTable').on('click', '.delete-button', function(e) {
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
</script>
@endsection
