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
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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
    let selectedScheduleElement = null; // Untuk menyimpan elemen yang dipilih

    // Fetch patients data
    try {
        const response = await fetch('/dashboard/schedules/get-patients');
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        const patients = await response.json();

        patients.forEach(patient => {
            const option = document.createElement('option');
            option.value = patient.id;
            // Gabungkan nama dengan aman, tangani jika ada bagian nama yang null/kosong
            option.textContent = [patient.fname, patient.mname, patient.lname].filter(name => name).join(' ');
            patientSelect.appendChild(option);
        });
    } catch (error) {
        console.error('Error fetching patients:', error);
        // Berikan pesan error yang lebih informatif atau fallback UI jika perlu
        const errorOption = document.createElement('option');
        errorOption.value = "";
        errorOption.textContent = "Error loading patients";
        errorOption.disabled = true;
        patientSelect.innerHTML = ''; // Kosongkan dulu jika ada opsi default
        patientSelect.appendChild(errorOption);
        // Mungkin nonaktifkan form atau beri tahu pengguna
    }

    // Handle form submission
    document.getElementById('filterForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        // Reset tampilan schedule yang dipilih sebelumnya dan sembunyikan form reservasi
        resultsClickedDiv.innerHTML = '';
        reservationForm.style.display = 'none';
        selectedSchedule = null;
        if (selectedScheduleElement) {
            // Pastikan selectedScheduleElement direset ke style default jika ada
             selectedScheduleElement.classList.remove('btn-success', 'text-white');
             selectedScheduleElement.classList.add('btn-outline-success');
             selectedScheduleElement = null;
        }


        const date = document.getElementById('date').value;
        const patientId = document.getElementById('patient').value;

        if (!patientId) {
            Swal.fire({ // Menggunakan Swal untuk alert yang lebih baik
                icon: 'warning',
                title: 'Oops...',
                text: 'Please select a patient first.',
            });
            return;
        }
        if (!date) {
            Swal.fire({
                icon: 'warning',
                title: 'Oops...',
                text: 'Please select a date first.',
            });
            return;
        }


        try {
            const response = await fetch(`/dashboard/schedules/get-doctors-by-date?date=${date}`);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            const data = await response.json();

            if (!data.doctors || data.doctors.length === 0) {
                resultsDiv.innerHTML = `
                <div class="alert alert-warning mt-3">
                    No available schedules found for <strong>${data.date_formatted || data.date}</strong> (${data.day_of_week}).
                </div>
                `;
                return;
            }

            resultsDiv.innerHTML = `
            <div class="card mt-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Available Schedules for <strong>${data.date_formatted || data.date}</strong> (${data.day_of_week})</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="35%" class="ps-3">Doctor</th>
                                    <th>Available Times</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${data.doctors.map(doctor => `
                                    <tr class="doctor-row" data-doctor-id="${doctor.doctor.id}">
                                        <td class="ps-3">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-0">${doctor.doctor.name}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap">
                                            ${doctor.schedules.filter(schedule => schedule.is_available).map(schedule =>
                                                // PERUBAHAN DI SINI: Gunakan btn-outline-success sebagai default
                                                // dan ganti <span> menjadi <button> untuk semantik yang lebih baik
                                                `<button type="button" class="btn btn-sm btn-outline-success schedule-badge"
                                                    data-time-start="${schedule.time_start}"
                                                    data-time-end="${schedule.time_end}"
                                                    data-doctor-id="${doctor.doctor.id}"
                                                    data-doctor-name="${doctor.doctor.name}">
                                                    ${schedule.time_start} - ${schedule.time_end}
                                                </button>`
                                            ).join('') || '<span class="text-muted fst-italic">No specific time slots, contact directly.</span>'}
                                            </div>
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
            console.error('Error fetching schedules:', error);
            resultsDiv.innerHTML = `
            <div class="alert alert-danger mt-3">
                Error loading schedule data. Please try again later. Details: ${error.message}
            </div>
            `;
        }
    });

    // Handle schedule selection
    resultsDiv.addEventListener('click', function(event) {
        // Hanya target elemen dengan kelas 'schedule-badge'
        if (event.target.classList.contains('schedule-badge')) {
            const clickedBadge = event.target;

            // Jika ada badge yang sudah dipilih sebelumnya, reset stylenya
            if (selectedScheduleElement && selectedScheduleElement !== clickedBadge) {
                selectedScheduleElement.classList.remove('btn-success', 'text-white');
                selectedScheduleElement.classList.add('btn-outline-success');
            }

            // Toggle style untuk badge yang diklik
            if (clickedBadge.classList.contains('btn-outline-success')) {
                // Jika belum dipilih, pilih
                clickedBadge.classList.remove('btn-outline-success');
                clickedBadge.classList.add('btn-success', 'text-white');
                selectedScheduleElement = clickedBadge; // Simpan elemen yang dipilih

                // Get selected schedule data
                selectedSchedule = {
                    time_start: clickedBadge.dataset.timeStart,
                    time_end: clickedBadge.dataset.timeEnd,
                    doctor_id: clickedBadge.dataset.doctorId,
                    doctor_name: clickedBadge.dataset.doctorName,
                    date: document.getElementById('date').value,
                    date_formatted: document.querySelector('#results .card-header h5 strong') ? document.querySelector('#results .card-header h5 strong').textContent : document.getElementById('date').value
                };

                // Set hidden form values
                document.getElementById('patient_id').value = document.getElementById('patient').value;
                document.getElementById('doctor_id').value = selectedSchedule.doctor_id;
                document.getElementById('reservation_date').value = selectedSchedule.date; // Kirim YYYY-MM-DD
                document.getElementById('time_start').value = selectedSchedule.time_start;
                document.getElementById('time_end').value = selectedSchedule.time_end;

                // Show confirmation
                resultsClickedDiv.innerHTML = `
                <div class="card border-success mt-3">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Selected Appointment</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Date:</strong> ${selectedSchedule.date_formatted}</p>
                                <p class="mb-0"><strong>Time:</strong> ${selectedSchedule.time_start} - ${selectedSchedule.time_end}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Doctor:</strong> ${selectedSchedule.doctor_name}</p>
                                <p class="mb-0"><strong>Patient:</strong> ${patientSelect.options[patientSelect.selectedIndex].text}</p>
                            </div>
                        </div>
                    </div>
                </div>
                `;
                reservationForm.style.display = 'block';
                resultsClickedDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });

            } else {
                // Jika sudah dipilih, batalkan pilihan (deselect)
                clickedBadge.classList.remove('btn-success', 'text-white');
                clickedBadge.classList.add('btn-outline-success');
                selectedScheduleElement = null; // Tidak ada yang dipilih
                selectedSchedule = null;
                resultsClickedDiv.innerHTML = ''; // Kosongkan konfirmasi
                reservationForm.style.display = 'none'; // Sembunyikan form reservasi
            }
        }
    });
});
</script>
@endsection