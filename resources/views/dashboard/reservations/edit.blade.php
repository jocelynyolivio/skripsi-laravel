@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h3 class="text-center">Edit Reservation</h3>
    <form action="{{ route('dashboard.reservations.update', $reservation->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Tampilkan nama pasien yang tidak bisa diubah -->
        <div class="mb-3">
            <label for="patient_name" class="form-label">Patient Name</label>
            <input type="text" name="patient_name" id="patient_name" class="form-control" value="{{ $reservation->patient->name }}" disabled>
        </div>

        <!-- Pilih Jadwal -->
        <div class="mb-3">
            <label for="schedule_id" class="form-label">Available Schedules</label>
            <select name="schedule_id" id="schedule_id" class="form-select" required>
                <option value="" disabled selected>Select Schedule</option>
                @foreach($schedules as $schedule)
                    <option value="{{ $schedule->id }}" 
                        @if($schedule->id == $reservation->schedule_id) selected @endif>
                        {{ $schedule->date }} ({{ $schedule->time_start }} - {{ $schedule->time_end }}) - Doctor: {{ $schedule->doctor->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary w-100">Update Reservation</button>
    </form>
</div>
@endsection
