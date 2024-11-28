@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h3 class="text-center">Add Schedule</h3>
    <form action="{{ route('dashboard.schedules.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="doctor_id" class="form-label">Select Doctor</label>
            <select name="doctor_id" id="doctor_id" class="form-select" required>
                <option value="" disabled selected>Select Doctor</option>
                @foreach($doctors as $doctor)
                    <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" name="date" id="date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="time_start" class="form-label">Start Time</label>
            <input type="time" name="time_start" id="time_start" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="time_end" class="form-label">End Time</label>
            <input type="time" name="time_end" id="time_end" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Add Schedule</button>
    </form>
</div>
@endsection
