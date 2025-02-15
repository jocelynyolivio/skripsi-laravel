@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <div class="d-flex justify-content-between mb-3">
        <h3 class="text-center">Reservations List</h3>
    </div>

    <table id="reservationTable" class="table table-striped table-bordered">
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
                    Chat Pasien
                </a>
                @if($reservation->status_konfirmasi !== 'Sudah Dikonfirmasi')
                    <a href="{{ route('dashboard.reservations.whatsappConfirm', $reservation->id) }}" class="btn btn-sm btn-primary wa-confirmation">
                        Konfirmasi WA
                    </a>
                @endif
            </td>
            <td>
                {{ $reservation->status_konfirmasi ?? 'Belum Dikonfirmasi' }}
            </td>
            <td>
                <a href="{{ route('dashboard.reservations.edit', $reservation->id) }}" class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('dashboard.reservations.destroy', $reservation->id) }}" method="POST" style="display:inline;" class="delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-sm btn-danger delete-button">Delete</button>
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

        // Event delegation for SweetAlert confirmation
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

        // SweetAlert confirmation for WhatsApp
        $('#reservationTable').on('click', '.wa-confirmation', function(e) {
            e.preventDefault();
            var url = $(this).attr('href'); // Get the URL from the href attribute
            Swal.fire({
                title: 'Yakin sudah melakukan konfirmasi WA?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, sudah konfirmasi!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to the confirmation URL
                    window.location.href = url;
                }
            });
        });
    });
</script>

@endsection