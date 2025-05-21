@extends('dashboard.layouts.main')
@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['text' => 'Reservations', 'url' => route('dashboard.reservations.index')],
            ['text' => 'Edit Reservation']
        ]
    ])
@endsection

@section('container')
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Edit Reservation</h3>
        </div>
        
        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <form id="editReservationForm" action="{{ route('dashboard.reservations.update', $reservation->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Hidden fields -->
                <input type="hidden" name="patient_id" id="patient_id" value="{{ $reservation->patient_id }}">
                <input type="hidden" name="doctor_id" id="doctor_id" value="{{ $reservation->doctor_id }}">
                <input type="hidden" name="tanggal_reservasi" id="reservation_date" value="{{ $reservation->tanggal_reservasi }}">
                <input type="hidden" name="jam_mulai" id="time_start" value="{{ $reservation->jam_mulai }}">
                <input type="hidden" name="jam_selesai" id="time_end" value="{{ $reservation->jam_selesai }}">

                <!-- Patient Info (readonly) -->
                <div class="mb-3">
                    <label class="form-label">Patient</label>
                    <input type="text" class="form-control" value="{{ $reservation->patient->fname }} {{ $reservation->patient->mname }} {{ $reservation->patient->lname }}" readonly>
                </div>

                <!-- Doctor Info (readonly) -->
                <div class="mb-3">
                    <label class="form-label">Current Doctor</label>
                    <input type="text" class="form-control" value="{{ $reservation->doctor->name }}" readonly>
                </div>

                <!-- Date Selection -->
                <div class="mb-3">
                    <label for="date" class="form-label">Change Reservation Date</label>
                    <input type="date" id="date" name="date" class="form-control" 
                           value="{{ $reservation->tanggal_reservasi }}" min="{{ date('Y-m-d') }}" required>
                </div>

                <!-- Available Schedules -->
                <div id="results" class="mt-4">
                    <!-- Results will appear here -->
                </div>

                <!-- Selected Schedule Preview -->
                <div id="resultsClicked" class="mt-4">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Current Appointment</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Date:</strong> {{ $reservation->tanggal_reservasi }}</p>
                                    <p><strong>Time:</strong> {{ $reservation->jam_mulai }} - {{ $reservation->jam_selesai }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Doctor:</strong> {{ $reservation->doctor->name }}</p>
                                    <p><strong>Patient:</strong> {{ $reservation->patient->fname }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="d-grid mt-3">
                    <button type="submit" class="btn btn-success btn-lg" id="saveButton">
                        <i class="bi bi-calendar-check me-2"></i> Update Reservation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .schedule-badge {
        transition: all 0.2s ease;
        margin-right: 5px;
        margin-bottom: 5px;
        padding: 8px 12px;
        font-size: 0.9rem;
        cursor: pointer;
    }
    
    .schedule-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .doctor-row:hover {
        background-color: #f8f9fa;
    }
    
    .selected-schedule {
        border-left: 4px solid #0d6efd;
        background-color: #f8f9fa;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editForm = document.getElementById('editReservationForm');
    const dateInput = document.getElementById('date');
    const resultsDiv = document.getElementById('results');
    const resultsClickedDiv = document.getElementById('resultsClicked');
    const saveButton = document.getElementById('saveButton');
    const doctorIdInput = document.getElementById('doctor_id');
    const timeStartInput = document.getElementById('time_start');
    const timeEndInput = document.getElementById('time_end');
    const reservationDateInput = document.getElementById('reservation_date');

    // Prevent form submission on Enter key
    document.addEventListener("keydown", function(event) {
        if (event.key === "Enter") {
            event.preventDefault();
        }
    });

    // Load available schedules when date changes
    dateInput.addEventListener('change', async function() {
        const date = dateInput.value;
        if (!date) return;

        try {
            const response = await fetch(`/dashboard/schedules/get-doctors-by-date?date=${date}`);
            const data = await response.json();

            resultsDiv.innerHTML = ` 
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Available Schedules for ${data.date} (${data.day_of_week})</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40%">Doctor</th>
                                        <th>Available Times</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${data.doctors.map(doctor => `
                                        <tr class="doctor-row" data-doctor-id="${doctor.doctor.id}">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-0">${doctor.doctor.name}</h6>
                                                    </div>
                                                </div>
                                            </td> 
                                            <td>
                                                ${doctor.schedules.filter(schedule => schedule.is_available).map(schedule => 
                                                    `<span class="badge bg-primary schedule-badge" 
                                                          data-time-start="${schedule.time_start}" 
                                                          data-time-end="${schedule.time_end}" 
                                                          data-doctor-id="${doctor.doctor.id}"
                                                          data-doctor-name="${doctor.doctor.name}">
                                                        ${schedule.time_start} - ${schedule.time_end}
                                                    </span>`
                                                ).join(' ')}
                                            </td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
        } catch (error) {
            console.error('Error:', error);
            resultsDiv.innerHTML = `
                <div class="alert alert-danger">
                    Error loading schedule data. Please try again later.
                </div>
            `;
        }
    });

    // Handle schedule selection
    resultsDiv.addEventListener('click', function(event) {
        if (event.target.classList.contains('schedule-badge')) {
            // Remove previous selection highlights
            document.querySelectorAll('.schedule-badge').forEach(badge => {
                badge.classList.remove('bg-success');
                badge.classList.add('bg-primary');
            });
            
            // Highlight selected badge
            event.target.classList.remove('bg-primary');
            event.target.classList.add('bg-success');
            
            // Get selected schedule data
            const selectedSchedule = {
                time_start: event.target.dataset.timeStart,
                time_end: event.target.dataset.timeEnd,
                doctor_id: event.target.dataset.doctorId,
                doctor_name: event.target.dataset.doctorName,
                date: dateInput.value
            };

            // Update hidden form values
            doctorIdInput.value = selectedSchedule.doctor_id;
            reservationDateInput.value = selectedSchedule.date;
            timeStartInput.value = selectedSchedule.time_start;
            timeEndInput.value = selectedSchedule.time_end;

            // Show updated selection
            resultsClickedDiv.innerHTML = `
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Updated Appointment Selection</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Date:</strong> ${selectedSchedule.date}</p>
                                <p><strong>Time:</strong> ${selectedSchedule.time_start} - ${selectedSchedule.time_end}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Doctor:</strong> ${selectedSchedule.doctor_name}</p>
                                <p><strong>Patient:</strong> {{ $reservation->patient->fname }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    });

    // Handle form submission
    editForm.addEventListener('submit', function() {
        saveButton.disabled = true;
        saveButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...';
    });
});
</script>
@endsection