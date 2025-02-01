@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h3 class="mb-4">Edit Reservation</h3>
    
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif
    
    <form id="editReservationForm" action="{{ route('dashboard.reservations.update', $reservation->id) }}" method="POST">
        @csrf
        @method('PUT') <!-- Laravel menangani PUT/PATCH menggunakan hidden input -->

        <input type="hidden" name="patient_id" id="patient_id" value="{{ $reservation->patient_id }}">
        <input type="hidden" name="doctor_id" id="doctor_id" value="{{ $reservation->doctor_id }}">
        <input type="hidden" name="tanggal_reservasi" id="reservation_date" value="{{ $reservation->tanggal_reservasi }}">
        <input type="hidden" name="jam_mulai" id="time_start" value="{{ $reservation->jam_mulai }}">
        <input type="hidden" name="jam_selesai" id="time_end" value="{{ $reservation->jam_selesai }}">

        <div class="mb-3">
            <label class="form-label">Nama Pasien</label>
            <input type="text" class="form-control" value="{{ $reservation->patient->name }}" disabled>
        </div>

        <div class="mb-3">
            <label class="form-label">Nama Dokter</label>
            <input type="text" class="form-control" value="{{ $reservation->doctor->name }}" disabled>
        </div>

        <div class="mb-3">
            <label class="form-label">Tanggal Reservasi</label>
            <input type="date" id="date" name="tanggal_reservasi" class="form-control" value="{{ $reservation->tanggal_reservasi }}" required>
        </div>

        <div id="results" class="mt-4">
            <!-- Available schedules will be displayed here -->
        </div>

        <button type="submit" class="btn btn-success mt-3" id="saveButton">Update Reservation</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editForm = document.getElementById('editReservationForm');
    const dateInput = document.getElementById('date');
    const resultsDiv = document.getElementById('results');
    const saveButton = document.getElementById('saveButton');
    const doctorIdInput = document.getElementById('doctor_id');
    const timeStartInput = document.getElementById('time_start');
    const timeEndInput = document.getElementById('time_end');

    dateInput.addEventListener('change', async function() {
        const date = dateInput.value;
        if (!date) return;

        try {
            const response = await fetch(`/dashboard/schedules/get-doctors-by-date?date=${date}`);
            const data = await response.json();

            resultsDiv.innerHTML = ` 
                <h5>Schedules for ${data.date} (${data.day_of_week}):</h5>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Doctor Name</th>
                                <th>Available Times</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${data.doctors.map(doctor => `
                                <tr data-doctor-id="${doctor.doctor.id}">
                                    <td>${doctor.doctor.name}</td>
                                    <td>
                                        ${doctor.schedules.filter(schedule => schedule.is_available).map(schedule => 
                                            `<span class="badge bg-success time-slot" data-time-start="${schedule.time_start}" data-time-end="${schedule.time_end}" data-doctor-id="${doctor.doctor.id}">
                                                ${schedule.time_start} - ${schedule.time_end}
                                            </span>`
                                        ).join(' ')}
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `;
        } catch (error) {
            console.error('Error:', error);
            resultsDiv.innerHTML = '<p class="text-danger">Error loading schedules</p>';
        }
    });

    resultsDiv.addEventListener('click', function(event) {
        if (event.target.classList.contains('time-slot')) {
            const selectedSchedule = {
                time_start: event.target.dataset.timeStart,
                time_end: event.target.dataset.timeEnd,
                doctor_id: event.target.dataset.doctorId
            };

            doctorIdInput.value = selectedSchedule.doctor_id;
            document.getElementById('reservation_date').value = dateInput.value;
            timeStartInput.value = selectedSchedule.time_start;
            timeEndInput.value = selectedSchedule.time_end;

            saveButton.style.display = 'block';
        }
    });

    editForm.addEventListener('submit', function() {
        saveButton.disabled = true; // Hindari double submit
    });
});
</script>

@endsection
