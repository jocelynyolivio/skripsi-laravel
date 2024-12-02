@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <div class="d-flex justify-content-between mb-3">
        <h3 class="text-center">Reservations List</h3>
        <a href="{{ route('dashboard.reservations.create') }}" class="btn btn-primary mb-3">Add Reservation</a>
    </div>

    <table id="reservationTable" class="display">
    <thead>
        <tr>
            <th>Patient</th>
            <th>Doctor</th>
            <th>Reservation Date</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>WA Confirmation</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($reservations as $index => $reservation)
        <tr>
            <td>{{ $reservation->patient->name }}</td>
            <td>{{ $reservation->doctor->name }}</td>
            <td>{{ $reservation->tanggal_reservasi }}</td>
            <td>{{ $reservation->jam_mulai }}</td>
            <td>{{ $reservation->jam_selesai }}</td>
            <td>
                <a href="{{ route('dashboard.reservations.whatsapp', $reservation->id) }}" 
                   class="btn btn-sm btn-success" 
                   target="_blank">
                    Konfirmasi WA
                </a>
            </td>
            <td>
                {{ $reservation->status_konfirmasi ?? 'Belum Dikonfirmasi' }}
            </td>
            <td>
                <a href="{{ route('dashboard.reservations.edit', $reservation->id) }}" class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('dashboard.reservations.destroy', $reservation->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

</div>
<script>
    $(document).ready(function() {
        $('#reservationTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,
        });
    });
</script>

@endsection