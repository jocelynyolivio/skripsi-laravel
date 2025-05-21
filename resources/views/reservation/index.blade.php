@extends('layouts.main')

@section('container')
<style>
    :root {
        --primary-color: #8c8d5e;
        --primary-color-light: #a3a47a;
        --primary-color-dark: #75764d;
        --secondary-color: #f8f9fa;
        --text-color: #333;
        --light-text: #f8f9fa;
    }

    .reservation-container {
        background-color: var(--secondary-color);
        border: 1px solid #ddd;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        color: var(--text-color);
    }

    .reservation-container h3 {
        color: var(--primary-color-dark);
        font-weight: bold;
    }

    body {
        color: var(--text-color);
    }

    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: var(--light-text);
    }

    .btn-primary:hover {
        background-color: var(--primary-color-dark);
        border-color: var(--primary-color-dark);
    }

    .btn-outline-success {
        color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .btn-outline-success:hover {
        background-color: var(--primary-color);
        color: var(--light-text);
    }

    .card {
        border-color: var(--primary-color-light);
    }

    .card-header,
    .card-body {
        background-color: var(--secondary-color);
    }

    h3,
    h5,
    h6 {
        color: var(--primary-color-dark);
    }
</style>

<div class="container mt-5 col-md-6 justify-content-center reservation-container">
    <h3 class="text-center mb-4">Make a Reservation</h3>

    <div class="row justify-content-center">
        <div class="col">
            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-body">
                    <form id="filterForm">
                        <div class="mb-3">
                            <label for="date" class="form-label">Select Date:</label>
                            <input type="date" id="date" name="date" class="form-control" min="{{ date('Y-m-d') }}" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Find Available Schedules</button>
                    </form>
                </div>
            </div>

            <div id="results" class="mt-4"></div>
        </div>
    </div>

    <!-- Modal Konfirmasi -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0">
                <div class="modal-header bg-light">
                    <h5 class="modal-title">Confirm Your Reservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Doctor:</strong> <span id="confirmDoctor"></span></p>
                    <p><strong>Date:</strong> <span id="confirmDate"></span></p>
                    <p><strong>Time:</strong> <span id="confirmTime"></span></p>
                </div>
                <div class="modal-footer">
                    <form id="confirmForm" action="{{ route('reservation.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="patient_id" value="{{ auth()->id() }}">
                        <input type="hidden" name="doctor_id" id="confirmDoctorId">
                        <input type="hidden" name="tanggal_reservasi" id="confirmDateInput">
                        <input type="hidden" name="jam_mulai" id="confirmTimeStart">
                        <input type="hidden" name="jam_selesai" id="confirmTimeEnd">
                        <button type="submit" class="btn btn-primary w-100">Confirm Reservation</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterForm = document.getElementById('filterForm');
        const resultsDiv = document.getElementById('results');
        const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));

        filterForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const date = document.getElementById('date').value;

            if (!date) {
                alert('Please select a date first.');
                return;
            }

            try {
                const response = await fetch(`/dashboard/schedules/get-doctors-by-date?date=${date}`);
                if (!response.ok) throw new Error('Failed to fetch schedules.');

                const data = await response.json();
                renderSchedules(data);
            } catch (error) {
                resultsDiv.innerHTML = `<div class="alert alert-danger">Error loading schedules.</div>`;
            }
        });

        function renderSchedules(data) {
            if (!data.doctors || data.doctors.length === 0) {
                resultsDiv.innerHTML = `<div class="alert alert-info">No available schedules for ${data.date}.</div>`;
                return;
            }

            let html = `
            <h5 class="mb-3">Available Schedules on ${data.date} (${data.day_of_week})</h5>
            <div class="list-group">
        `;

            data.doctors.forEach(doctor => {
                html += `
                <div class="mb-3 border rounded p-3 shadow-sm">
                    <h6 class="mb-2">${doctor.doctor.name}</h6>
            `;

                doctor.schedules.forEach(schedule => {
                    if (schedule.is_available) {
                        html += `
                        <button class="btn btn-outline-success btn-sm me-2 mb-2 select-time"
                            data-doctor-id="${doctor.doctor.id}"
                            data-doctor-name="${doctor.doctor.name}"
                            data-date="${data.date}"
                            data-time-start="${schedule.time_start}"
                            data-time-end="${schedule.time_end}">
                            ${schedule.time_start} - ${schedule.time_end}
                        </button>
                    `;
                    }
                });

                html += `</div>`;
            });

            html += `</div>`;
            resultsDiv.innerHTML = html;

            document.querySelectorAll('.select-time').forEach(btn => {
                btn.addEventListener('click', function() {
                    const doctorName = this.dataset.doctorName;
                    const date = this.dataset.date;
                    const timeStart = this.dataset.timeStart;
                    const timeEnd = this.dataset.timeEnd;
                    const doctorId = this.dataset.doctorId;

                    Swal.fire({
                        title: 'Confirm Your Reservation',
                        html: `
                <p><strong>Doctor:</strong> ${doctorName}</p>
                <p><strong>Date:</strong> ${date}</p>
                <p><strong>Time:</strong> ${timeStart} - ${timeEnd}</p>
            `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Confirm',
                        confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--primary-color').trim(),
                        cancelButtonColor: '#aaa',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Submit the reservation manually
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = "{{ route('reservation.store') }}";

                            const token = document.querySelector('input[name="_token"]').value;
                            form.innerHTML = `
                    <input type="hidden" name="_token" value="${token}">
                    <input type="hidden" name="patient_id" value="{{ auth()->id() }}">
                    <input type="hidden" name="doctor_id" value="${doctorId}">
                    <input type="hidden" name="tanggal_reservasi" value="${date}">
                    <input type="hidden" name="jam_mulai" value="${timeStart}">
                    <input type="hidden" name="jam_selesai" value="${timeEnd}">
                `;
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                });
            });
        }
    });
</script>
@endsection