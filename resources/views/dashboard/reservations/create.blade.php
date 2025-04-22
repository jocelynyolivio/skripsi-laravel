@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h3 class="text-center">Create Reservation</h3>
    <form action="{{ route('dashboard.reservations.store') }}" method="POST">
        @csrf

        <input type="hidden" name="user_type" value="admin">

        <!-- Pilih Jadwal -->
        <div class="mb-3">
            <label for="schedule_id" class="form-label">Available Schedules</label>
            <select name="schedule_id" id="schedule_id" class="form-select" required>
                <option value="" disabled selected>Select Schedule</option>
                @foreach($schedules as $schedule)
                    <option value="{{ $schedule->id }}">
                        {{ $schedule->date }} ({{ $schedule->time_start }} - {{ $schedule->time_end }}) - Doctor: {{ $schedule->doctor->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Pilih Pasien -->
        <div class="mb-3">
            <label for="patient_id" class="form-label">Select Patient</label>
            <select name="patient_id" id="patient_id" class="form-select" required>
                <option value="" disabled selected>Select Patient</option>
                @foreach($patients as $patient)
                    <option value="{{ $patient->id }}">{{ $patient->fname }} {{ $patient->mname }} {{ $patient->lname }} ({{ $patient->email }})</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary w-100">Create Reservation</button>
    </form>
</div>
@endsection
