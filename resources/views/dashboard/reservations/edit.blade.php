@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h3 class="text-center">Edit Reservation</h3>
    <form action="{{ route('dashboard.reservations.update', $reservation->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Field Nama -->
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $reservation->nama }}" required>
        </div>

        <!-- Field Phone -->
        <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" name="phone" id="phone" class="form-control" value="{{ $reservation->nomor_telepon }}" required>
        </div>

        <!-- Field Reservation Date -->
        <div class="mb-3">
            <label for="reservation_date" class="form-label">Reservation Date</label>
            <input type="date" name="reservation_date" id="reservation_date" class="form-control" value="{{ $reservation->tanggal_reservasi }}" required>
        </div>

        <!-- Field Reservation Time -->
        <div class="mb-3">
            <label for="reservation_time" class="form-label">Reservation Time</label>
            <input type="time" name="reservation_time" id="reservation_time" class="form-control" value="{{ $reservation->jam_reservasi }}" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Update Reservation</button>
    </form>
</div>
@endsection
