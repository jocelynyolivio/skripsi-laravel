@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h1 class="mb-4">Data Reservasi</h1>

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
                <td>{{ $reservation->nama }}</td>
                <td>{{ $reservation->nomor_telepon }}</td>
                <td>{{ $reservation->tanggal_reservasi }}</td>
                <td>{{ $reservation->jam_reservasi }}</td>
                <td>{{ $reservation->doctor->name }}</td>
                    <td>
                        <!-- Tombol Edit -->
                        <a href="" class="btn btn-sm btn-warning">Edit</a>
                        <!-- Tombol Delete -->
                        <form action="" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
