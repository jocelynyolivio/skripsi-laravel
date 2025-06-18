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

            <form id="filterForm" action="{{ route('dashboard.schedules.get-doctors-by-date') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="patient" class="form-label">Select Patient</label>
                        <select id="patient" name="patient" class="form-select" required>
                            <option value="">-- Search or Select a Patient --</option>
                            {{-- Options akan dimuat via JavaScript atau Select2 AJAX --}}
                        </select>
                    </div>

                    {{-- Tambahkan div untuk detail pasien --}}
                    <div class="col-md-6" id="patient-details" style="display: none;">
                        <label class="form-label">Patient Details</label>
                        <div class="card card-body bg-light">
                            <p class="mb-1"><strong>Phone:</strong> <span id="patient-phone"></span></p>
                            <p class="mb-0"><strong>Birth Date:</strong> <span id="patient-dob"></span></p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="date" class="form-label">Select Date</label>
                        <input type="date" id="date" name="date" class="form-control"
                            value="{{ date('Y-m-d') }}"
                            min="{{ $today }}"         {{-- Set tanggal minimum ke hari ini --}}
                            max="{{ $oneMonthFromNow }}" {{-- Set tanggal maksimum ke satu bulan ke depan --}}
                            required>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-2"></i> Search Available Schedule
                        </button>
                    </div>
                </div>
            </form>

            <div id="results" class="mt-4">
            </div>

            <div id="resultsClicked" class="mt-4">
            </div>

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
    /* ... (CSS Anda yang sudah ada, termasuk Select2) ... */
</style>

<script>
document.addEventListener('DOMContentLoaded', async function() {
    // ... (Kode JavaScript Select2 Anda yang sudah ada) ...

    const patientSelect = document.getElementById('patient');
    const patientDetailsDiv = document.getElementById('patient-details');
    const patientPhoneSpan = document.getElementById('patient-phone');
    const patientDobSpan = document.getElementById('patient-dob');
    const reservationForm = document.getElementById('reservationForm');
    const resultsDiv = document.getElementById('results');
    const resultsClickedDiv = document.getElementById('resultsClicked');
    let selectedSchedule = null;
    let selectedScheduleElement = null;

    // Inisialisasi Select2 untuk dropdown pasien (kode yang sudah ada)
    $(patientSelect).select2({
        placeholder: '-- Search or Select a Patient --',
        allowClear: true,
        ajax: {
            url: '{{ route('dashboard.schedules.get-patients') }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { q: params.term };
            },
            processResults: function (data) {
                return {
                    results: data.map(patient => {
                        let fullName = [patient.fname, patient.mname, patient.lname].filter(name => name).join(' ');
                        return {
                            id: patient.id,
                            text: fullName + (patient.home_mobile ? ` (${patient.home_mobile})` : ''),
                            mobile: patient.home_mobile,
                            dob: patient.date_of_birth
                        };
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 1
    });

    $(patientSelect).on('select2:select', function (e) {
        const selectedData = e.params.data;
        if (selectedData) {
            patientPhoneSpan.textContent = selectedData.mobile || 'N/A';
            patientDobSpan.textContent = selectedData.dob || 'N/A';
            patientDetailsDiv.style.display = 'block';
        } else {
            patientDetailsDiv.style.display = 'none';
        }
    });

    $(patientSelect).on('select2:unselect', function (e) {
        patientDetailsDiv.style.display = 'none';
        patientPhoneSpan.textContent = '';
        patientDobSpan.textContent = '';
    });

    // Handle form submission (kode yang sudah ada)
    document.getElementById('filterForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        resultsClickedDiv.innerHTML = '';
        reservationForm.style.display = 'none';
        selectedSchedule = null;
        if (selectedScheduleElement) {
            selectedScheduleElement.classList.remove('btn-success', 'text-white');
            selectedScheduleElement.classList.add('btn-outline-success');
            selectedScheduleElement = null;
        }

        const date = document.getElementById('date').value;
        const patientId = document.getElementById('patient').value;

        if (!patientId) {
            Swal.fire({ icon: 'warning', title: 'Oops...', text: 'Please select a patient first.' });
            return;
        }
        if (!date) {
            Swal.fire({ icon: 'warning', title: 'Oops...', text: 'Please select a date first.' });
            return;
        }

        // Klien-side validation untuk tanggal
        const selectedDateObj = new Date(date);
        const minDateObj = new Date("{{ $today }}"); // Gunakan variabel dari Laravel
        const maxDateObj = new Date("{{ $oneMonthFromNow }}"); // Gunakan variabel dari Laravel

        if (selectedDateObj < minDateObj || selectedDateObj > maxDateObj) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Date',
                text: `Please select a date between {{ \Carbon\Carbon::parse($today)->format('d M Y') }} and {{ \Carbon\Carbon::parse($oneMonthFromNow)->format('d M Y') }}.`
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

    // Handle schedule selection (kode yang sudah ada)
    resultsDiv.addEventListener('click', function(event) {
        if (event.target.classList.contains('schedule-badge')) {
            const clickedBadge = event.target;

            if (selectedScheduleElement && selectedScheduleElement !== clickedBadge) {
                selectedScheduleElement.classList.remove('btn-success', 'text-white');
                selectedScheduleElement.classList.add('btn-outline-success');
            }

            if (clickedBadge.classList.contains('btn-outline-success')) {
                clickedBadge.classList.remove('btn-outline-success');
                clickedBadge.classList.add('btn-success', 'text-white');
                selectedScheduleElement = clickedBadge;

                selectedSchedule = {
                    time_start: clickedBadge.dataset.timeStart,
                    time_end: clickedBadge.dataset.timeEnd,
                    doctor_id: clickedBadge.dataset.doctorId,
                    doctor_name: clickedBadge.dataset.doctorName,
                    date: document.getElementById('date').value,
                    date_formatted: document.querySelector('#results .card-header h5 strong') ? document.querySelector('#results .card-header h5 strong').textContent : document.getElementById('date').value
                };

                document.getElementById('patient_id').value = document.getElementById('patient').value;
                document.getElementById('doctor_id').value = selectedSchedule.doctor_id;
                document.getElementById('reservation_date').value = selectedSchedule.date;
                document.getElementById('time_start').value = selectedSchedule.time_start;
                document.getElementById('time_end').value = selectedSchedule.time_end;

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
                clickedBadge.classList.remove('btn-success', 'text-white');
                clickedBadge.classList.add('btn-outline-success');
                selectedScheduleElement = null;
                selectedSchedule = null;
                resultsClickedDiv.innerHTML = '';
                reservationForm.style.display = 'none';
            }
        }
    });
});
</script>
@endsection