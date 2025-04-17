@extends('dashboard.layouts.main')
@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['text' => 'Reservations', 'url' => route('dashboard.reservations.index')],
            ['text' => 'Make Reservation']
        ]
    ])
@endsection
@section('container')
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Make Reservations</h3>
        </div>
        
        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <!-- Search Form -->
            <form id="filterForm" action="{{ route('dashboard.schedules.get-doctors-by-date') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="patient" class="form-label">Select Patient</label>
                        <select id="patient" name="patient" class="form-select" required>
                            <option value="">-- Select a Patient --</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="date" class="form-label">Select Date</label>
                        <input type="date" id="date" name="date" class="form-control" 
                               value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" required>
                    </div>
                    
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-2"></i> Search Available Schedule
                        </button>
                    </div>
                </div>
            </form>

            <!-- Search Results -->
            <div id="results" class="mt-4">
                <!-- Results will appear here -->
            </div>

            <!-- Selected Schedule -->
            <div id="resultsClicked" class="mt-4">
                <!-- Selected schedule will appear here -->
            </div>

            <!-- Reservation Form -->
            <form id="reservationForm" action="{{ route('dashboard.schedules.store-reservation') }}" method="POST" class="mt-4" style="display: none;">
                @csrf
                <input type="hidden" name="patient_id" id="patient_id">
                <input type="hidden" name="doctor_id" id="doctor_id">
                <input type="hidden" name="tanggal_reservasi" id="reservation_date">
                <input type="hidden" name="jam_mulai" id="time_start">
                <input type="hidden" name="jam_selesai" id="time_end">
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="bi bi-calendar-check me-2"></i> Confirm Reservation
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
document.addEventListener('DOMContentLoaded', async function() {
    const patientSelect = document.getElementById('patient');
    const reservationForm = document.getElementById('reservationForm');
    const resultsDiv = document.getElementById('results');
    const resultsClickedDiv = document.getElementById('resultsClicked');
    let selectedSchedule = null;

    // Fetch patients data
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
        patientSelect.innerHTML = '<option value="">Error loading patients</option>';
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

            if (data.doctors.length === 0) {
                resultsDiv.innerHTML = `
                    <div class="alert alert-warning">
                        No available schedules found for ${data.date} (${data.day_of_week}).
                    </div>
                `;
                return;
            }

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
            selectedSchedule = {
                time_start: event.target.dataset.timeStart,
                time_end: event.target.dataset.timeEnd,
                doctor_id: event.target.dataset.doctorId,
                doctor_name: event.target.dataset.doctorName,
                date: document.getElementById('date').value
            };

            // Set hidden form values
            document.getElementById('patient_id').value = document.getElementById('patient').value;
            document.getElementById('doctor_id').value = selectedSchedule.doctor_id;
            document.getElementById('reservation_date').value = selectedSchedule.date;
            document.getElementById('time_start').value = selectedSchedule.time_start;
            document.getElementById('time_end').value = selectedSchedule.time_end;

            // Show confirmation
            resultsClickedDiv.innerHTML = `
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Selected Appointment</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Date:</strong> ${selectedSchedule.date}</p>
                                <p><strong>Time:</strong> ${selectedSchedule.time_start} - ${selectedSchedule.time_end}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Doctor:</strong> ${selectedSchedule.doctor_name}</p>
                                <p><strong>Patient:</strong> ${patientSelect.options[patientSelect.selectedIndex].text}</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Show reservation form
            reservationForm.style.display = 'block';
            
            // Scroll to confirmation
            resultsClickedDiv.scrollIntoView({ behavior: 'smooth' });
        }
    });
});
</script>
@endsection