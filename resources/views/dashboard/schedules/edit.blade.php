@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h3>Edit Reservation</h3>
    <form action="{{ route('dashboard.reservations.update', $reservation->id) }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Patient:</label>
            <select name="patient_id" class="form-control" required>
                @foreach($patients as $patient)
                    <option value="{{ $patient->id }}" {{ $patient->id == $reservation->patient_id ? 'selected' : '' }}>
                        {{ $patient->name }}
                    </option>
                @endforeach
            </select>
        </div>

       

        <div class="form-group mt-3">
            <label>Reservation Date:</label>
            <input type="date" name="tanggal_reservasi" class="form-control" value="{{ $reservation->tanggal_reservasi }}" required>
        </div>

        <div class="form-group mt-3">
            <label>Start Time:</label>
            <input type="time" name="jam_mulai" class="form-control" value="{{ $reservation->jam_mulai }}" required>
        </div>

        <div class="form-group mt-3">
            <label>End Time:</label>
            <input type="time" name="jam_selesai" class="form-control" value="{{ $reservation->jam_selesai }}" required>
        </div>

        <button type="submit" class="btn btn-success mt-3">Update Reservation</button>
    </form>
</div>
@endsection
w