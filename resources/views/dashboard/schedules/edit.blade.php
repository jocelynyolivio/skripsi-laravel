<!-- Edit Schedule Page -->
@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h3 class="text-center">Edit Schedule</h3>
    <form action="{{ route('dashboard.schedules.update', $schedule->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="doctor_id" class="form-label">Select Doctor</label>
            <select name="doctor_id" id="doctor_id" class="form-select" required>
                <option value="" disabled>Select Doctor</option>
                @foreach($doctors as $doctor)
                    <option value="{{ $doctor->id }}" {{ $doctor->id == $schedule->doctor_id ? 'selected' : '' }}>
                        {{ $doctor->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" name="date" id="date" class="form-control" value="{{ $schedule->date }}" required>
        </div>

        <div class="mb-3">
            <label for="time_start" class="form-label">Start Time</label>
            <input type="time" name="time_start" id="time_start" class="form-control" value="{{ $schedule->time_start }}" required>
        </div>

        <div class="mb-3">
            <label for="time_end" class="form-label">End Time</label>
            <input type="time" name="time_end" id="time_end" class="form-control" value="{{ $schedule->time_end }}" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Update Schedule</button>
    </form>
</div>
@endsection
