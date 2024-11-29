@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <div class="d-flex justify-content-between mb-3">
        <h3 class="text-center">Reservations List</h3>
        <a href="{{ route('dashboard.reservations.create') }}" class="btn btn-primary mb-3">Add Reservation</a>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Nomor Telepon</th>
                <th>Tanggal</th>
                <th>Jam</th>
                <th>Dokter</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reservations as $index => $reservation)
            <tr>
                <td>{{ $reservation->patient->name }}</td>
                <td>{{ $reservation->patient->nomor_telepon }}</td>
                <td>{{ $reservation->tanggal_reservasi }}</td>
                <td>{{ $reservation->jam_reservasi }}</td>
                <td>{{ $reservation->doctor->name }}</td>
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
@endsection