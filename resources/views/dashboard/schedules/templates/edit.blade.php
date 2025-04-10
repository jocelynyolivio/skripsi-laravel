@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5 col-md-6">
    <h3 class="mb-4">Edit Schedule Template</h3>

    <form id="updateForm" action="{{ route('dashboard.schedules.templates.update', $template->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="doctor_id" class="form-label">Doctor</label>
            <select name="doctor_id" id="doctor_id" class="form-select" required>
                @foreach($doctors as $doctor)
                <option value="{{ $doctor->id }}" {{ $template->doctor_id == $doctor->id ? 'selected' : '' }}>
                    {{ $doctor->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="day_of_week" class="form-label">Day of Week</label>
            <select name="day_of_week" id="day_of_week" class="form-select" required>
                @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
                <option value="{{ $day }}" {{ $template->day_of_week == $day ? 'selected' : '' }}>
                    {{ $day }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="start_time" class="form-label">Start Time</label>
            <input type="time" name="start_time" id="start_time" class="form-control"
                value="{{ \Carbon\Carbon::parse($template->start_time)->format('H:i') }}">
        </div>

        <div class="mb-3">
            <label for="end_time" class="form-label">End Time</label>
            <input type="time" name="end_time" id="end_time" class="form-control"
                value="{{ \Carbon\Carbon::parse($template->end_time)->format('H:i') }}">
        </div>

        {{-- Active Checkbox --}}
        <div class="mb-3 form-check form-switch">
            <input type="checkbox" class="form-check-input" name="is_active" id="is_active" value="1"
                {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">Active Schedule?</label>
            <small class="text-muted d-block">Toggle to enable/disable this schedule template</small>
        </div>

        <button type="button" class="btn btn-primary" id="submitBtn">Update Template</button>
    </form>
</div>

<script>
    document.getElementById('submitBtn').addEventListener('click', function(e) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You are about to update this schedule template.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Yes, update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('updateForm').submit();
            }
        });
    });
</script>

@endsection