@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5 col-md-6">
    <h3 class="mb-4">Edit Schedule Override</h3>

    <form action="{{ route('dashboard.schedules.overrides.update', $override->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="doctor_id" class="form-label">Doctor</label>
            <select name="doctor_id" id="doctor_id" class="form-select">
                @foreach($doctors as $doctor)
                <option value="{{ $doctor->id }}" {{ $doctor->id == $override->doctor_id ? 'selected' : '' }}>{{ $doctor->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="override_date" class="form-label">Date</label>
            <input type="date" name="override_date" id="override_date" class="form-control" value="{{ $override->override_date }}">
        </div>
        <div class="mb-3">
            <label for="start_time" class="form-label">Start Time</label>
            <input type="time" name="start_time" id="start_time" class="form-control" value="{{ $override->start_time }}">
        </div>
        <div class="mb-3">
            <label for="end_time" class="form-label">End Time</label>
            <input type="time" name="end_time" id="end_time" class="form-control" value="{{ $override->end_time }}">
        </div>
        <div class="mb-3">
            <label for="is_available" class="form-label">Available</label>
            <select name="is_available" id="is_available" class="form-select">
                <option value="1" {{ $override->is_available ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ !$override->is_available ? 'selected' : '' }}>No</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="reason" class="form-label">Reason</label>
            <input type="text" name="reason" id="reason" class="form-control" value="{{ $override->reason }}">
        </div>
        <button type="submit" class="btn btn-primary">Update Override</button>
    </form>
</div>
@endsection