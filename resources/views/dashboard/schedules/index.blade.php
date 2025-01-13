@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h3 class="mb-4">Schedules</h3>

    <form id="filterForm" action="{{ route('dashboard.schedules.get-doctors-by-date') }}" method="GET">
        <div class="form-group">
            <label for="date">Select Date:</label>
            <input type="date" id="date" name="date" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary mt-2">Filter</button>
    </form>

    <div id="results" class="mt-4">
        <!-- Hasil dokter yang tersedia akan muncul di sini -->
    </div>
</div>

<script>
    document.getElementById('filterForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const date = document.getElementById('date').value;
        const response = await fetch(`{{ route('dashboard.schedules.get-doctors-by-date') }}?date=${date}`);
        const data = await response.json();

        let resultsDiv = document.getElementById('results');
        resultsDiv.innerHTML = `
            <h5>Available Doctors for ${data.date} (${data.day_of_week}):</h5>
        `;

        if (data.doctors.length > 0) {
            let list = '<ul>';
            data.doctors.forEach(doctor => {
                list += `<li>${doctor.name}</li>`;
            });
            list += '</ul>';
            resultsDiv.innerHTML += list;
        } else {
            resultsDiv.innerHTML += `<p>No doctors available on this date.</p>`;
        }
    });
</script>
@endsection