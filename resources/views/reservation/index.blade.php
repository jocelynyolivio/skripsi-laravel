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
    <form id="filterForm">
        <div class="form-group">
            <label for="date">Select Date:</label>
            <input type="date" id="date" name="date" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Find Available Schedules</button>
    </form>

    <div id="results" class="mt-4"></div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterForm = document.getElementById('filterForm');
    const resultsDiv = document.getElementById('results');

    filterForm.addEventListener('submit', async function (event) {
        event.preventDefault(); // Mencegah reload halaman

        const date = document.getElementById('date').value;
        if (!date) {
            alert('Please select a date first.');
            return;
        }

        try {
            // Fetch data dari backend menggunakan AJAX
            const response = await fetch(`/dashboard/schedules/get-doctors-by-date?date=${date}`);
            if (!response.ok) throw new Error('Failed to fetch schedules.');

            const data = await response.json();
            renderSchedules(data);
        } catch (error) {
            console.error('Error fetching schedules:', error);
            resultsDiv.innerHTML = '<div class="alert alert-danger">Failed to load schedules.</div>';
        }
    });

    function renderSchedules(data) {
        if (!data.doctors || data.doctors.length === 0) {
            resultsDiv.innerHTML = `<div class="alert alert-info">No available schedules found for ${data.date}.</div>`;
            return;
        }

        let tableHTML = `
            <h5>Schedules for ${data.date} (${data.day_of_week}):</h5>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Doctor</th>
                        <th>Available Times</th>
                    </tr>
                </thead>
                <tbody>
        `;

        data.doctors.forEach(doctorSchedules => {
            tableHTML += `
                <tr>
                    <td>${doctorSchedules.doctor.name}</td>
                    <td>
            `;
            doctorSchedules.schedules.forEach(time => {
                if (time.is_available) {
                    tableHTML += `
                        <form action="{{ route('reservation.store') }}" method="POST" style="display: inline;">
                            @csrf
                            <input type="hidden" name="patient_id" value="{{ auth()->id() }}">
                            <input type="hidden" name="doctor_id" value="${doctorSchedules.doctor.id}">
                            <input type="hidden" name="tanggal_reservasi" value="${data.date}">
                            <input type="hidden" name="jam_mulai" value="${time.time_start}">
                            <input type="hidden" name="jam_selesai" value="${time.time_end}">
                            <button type="submit" class="badge bg-success border-0">
                                ${time.time_start} - ${time.time_end}
                            </button>
                        </form>
                    `;
                }
            });
            tableHTML += `</td></tr>`;
        });

        tableHTML += `</tbody></table>`;
        resultsDiv.innerHTML = tableHTML;
    }
});
</script>
@endsection
