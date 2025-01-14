@extends('layouts.main')

@section('container')
<div class="container mt-5">
    <h3 class="mb-4">Make a Reservation</h3>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Form untuk memilih tanggal -->
    <form id="filterForm" action="{{ route('reservation.index') }}" method="GET">
        <div class="form-group">
            <label for="date">Select Date:</label>
            <input type="date" id="date" name="date" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Find Available Schedules</button>
    </form>

    <div id="results" class="mt-4">
        <!-- Tabel jadwal akan dimuat di sini -->
    </div>

    <!-- Form untuk reservasi -->
    <form id="reservationForm" action="{{ route('reservation.store') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="doctor_id" id="doctor_id">
        <input type="hidden" name="tanggal_reservasi" id="reservation_date">
        <input type="hidden" name="jam_mulai" id="time_start">
        <input type="hidden" name="jam_selesai" id="time_end">
        
        <button type="submit" class="btn btn-success mt-3">Confirm Reservation</button>
    </form>
</div>

<script>document.addEventListener('DOMContentLoaded', async function() {
    const reservationForm = document.getElementById('reservationForm');
    let selectedSchedule = null;

    document.getElementById('filterForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const date = document.getElementById('date').value;

        try {
            // Ganti URL API dengan rute yang sesuai di Laravel
            const response = await fetch(`/dashboard/schedules/get-doctors-by-date?date=${date}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
            });
            const data = await response.json();

            // Memastikan data terkirim dengan benar
            if (data.doctors && data.doctors.length > 0) {
                let resultsDiv = document.getElementById('results');
                resultsDiv.innerHTML = `
                    <h5>Schedules for ${data.date} (${data.day_of_week}):</h5>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Doctor</th>
                                <th>Available Times</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${data.doctors.map(schedule => `
                                <tr>
                                    <td>${schedule.doctor.name}</td>
                                    <td>
                                        ${schedule.schedules.map(time => `
                                            <span class="badge bg-success" data-time-start="${time.time_start}" data-time-end="${time.time_end}" data-doctor-id="${schedule.doctor.id}">
                                                ${time.time_start} - ${time.time_end}
                                            </span>
                                        `).join(' ')}
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                `;
            } else {
                document.getElementById('results').innerHTML = '<p class="text-danger">No available schedules found for this date.</p>';
            }
        } catch (error) {
            console.error('Error fetching schedules:', error);
            document.getElementById('results').innerHTML = '<p class="text-danger">Error loading schedules</p>';
        }
    });

    document.getElementById('results').addEventListener('click', function(event) {
        if (event.target.tagName === 'SPAN' && event.target.classList.contains('badge')) {
            const timeStart = event.target.dataset.timeStart;
            const timeEnd = event.target.dataset.timeEnd;
            const doctorId = event.target.dataset.doctorId;

            document.getElementById('doctor_id').value = doctorId;
            document.getElementById('reservation_date').value = document.getElementById('date').value;
            document.getElementById('time_start').value = timeStart;
            document.getElementById('time_end').value = timeEnd;

            reservationForm.style.display = 'block';
        }
    });
});

</script>
@endsection
