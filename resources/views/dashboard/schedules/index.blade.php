@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h3 class="mb-4">Schedules</h3>
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

    <!-- Dropdown Pasien -->
    <form id="filterForm" action="{{ route('dashboard.schedules.get-doctors-by-date') }}" method="GET">
        <div class="form-group">
            <label for="patient">Select Patient:</label>
            <select id="patient" name="patient" class="form-control" required>
                <option value="">-- Select a Patient --</option>
            </select>
        </div>

        <div class="form-group mt-3">
            <label for="date">Select Date:</label>
            <input type="date" id="date" name="date" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Filter</button>
    </form>

    <div id="results" class="mt-4">
        <!-- Results will appear here -->
    </div>

    <!-- Reservation Form (without AJAX) -->
    <form id="reservationForm" action="{{ route('dashboard.schedules.store-reservation') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="patient_id" id="patient_id">
        <input type="hidden" name="doctor_id" id="doctor_id">
        <input type="hidden" name="tanggal_reservasi" id="reservation_date">
        <input type="hidden" name="jam_mulai" id="time_start">
        <input type="hidden" name="jam_selesai" id="time_end">
        
        <button type="submit" class="btn btn-success mt-3">Make Reservation</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', async function() {
    const patientSelect = document.getElementById('patient');
    const reservationForm = document.getElementById('reservationForm');
    let selectedSchedule = null;

    // Fetch patients and populate dropdown
    try {
        const response = await fetch('/dashboard/schedules/get-patients');
        const patients = await response.json();

        patients.forEach(patient => {
            const option = document.createElement('option');
            option.value = patient.id;
            option.textContent = patient.name;
            patientSelect.appendChild(option);
        });
    } catch (error) {
        console.error('Error fetching patients:', error);
    }

    // Handle form submission
    document.getElementById('filterForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const date = document.getElementById('date').value;
    const patientId = document.getElementById('patient').value;

    if (!patientId) {
        alert('Please select a patient first.');
        return;
    }

    try {
        const response = await fetch(`/dashboard/schedules/get-doctors-by-date?date=${date}`);
        const data = await response.json();

        let resultsDiv = document.getElementById('results');
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
                                        `<span class="badge bg-success">
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
        document.getElementById('results').innerHTML = '<p class="text-danger">Error loading schedules</p>';
    }
});


    document.getElementById('results').addEventListener('click', function (event) {
        if (event.target.tagName === 'SPAN' && event.target.classList.contains('badge')) {
            const timeRange = event.target.textContent.split(' - ');
            const doctorRow = event.target.closest('tr');
            const doctorId = doctorRow.getAttribute('data-doctor-id');

            selectedSchedule = {
                time_start: timeRange[0],
                time_end: timeRange[1],
                doctor_id: doctorId,
            };

            // Set hidden input values
            document.getElementById('patient_id').value = document.getElementById('patient').value;
            document.getElementById('doctor_id').value = selectedSchedule.doctor_id;
            document.getElementById('reservation_date').value = document.getElementById('date').value;
            document.getElementById('time_start').value = selectedSchedule.time_start;
            document.getElementById('time_end').value = selectedSchedule.time_end;

            // Show the reservation form
            reservationForm.style.display = 'block';
        }
    });
});
</script>
@endsection
