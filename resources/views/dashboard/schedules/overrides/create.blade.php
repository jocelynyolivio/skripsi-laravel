@extends('dashboard.layouts.main')
@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['text' => 'Schedule Overrides', 'url' => route('dashboard.schedules.overrides.index')],
            ['text' => 'Create Schedule Overrides']
        ]
    ])
@endsection
@section('container')
<div class="container mt-5 col-md-6">
    <h3 class="mb-4">Add New Schedule Override</h3>

    <form id="override-form" action="{{ route('dashboard.schedules.overrides.store') }}" method="POST">
    @csrf
        <div class="mb-3">
            <label for="doctor_id" class="form-label">Doctor</label>
            <select name="doctor_id" id="doctor_id" class="form-select">
                @foreach($doctors as $doctor)
                <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="override_date" class="form-label">Date</label>
            <input type="date" name="override_date" id="override_date" class="form-control" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}">
        </div>
        <div class="mb-3">
            <label for="start_time" class="form-label">Start Time</label>
            <input type="time" name="start_time" id="start_time" class="form-control" value="09:00">
        </div>
        <div class="mb-3">
            <label for="end_time" class="form-label">End Time</label>
            <input type="time" name="end_time" id="end_time" class="form-control" value="10:00">
        </div>
        <input type="hidden" name="is_available" value="0">
        <div class="mb-3">
            <label for="reason" class="form-label">Reason</label>
            <input type="text" name="reason" id="reason" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Save Override</button>
    </form>
</div>
<script>
document.getElementById('override-form').addEventListener('submit', function(e) {
    e.preventDefault(); // cegah submit langsung

    Swal.fire({
        title: 'Are you sure?',
        text: "You are about to save this override.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#aaa',
        confirmButtonText: 'Yes, save it!'
    }).then((result) => {
        if (result.isConfirmed) {
            e.target.submit(); // submit form jika konfirmasi
        }
    });
});
</script>
@endsection 