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
        <!-- Results will appear here -->
    </div>
</div>

<script>
document.getElementById('filterForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const date = document.getElementById('date').value;
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
                            <tr>
                                <td>${doctor.doctor.name}</td>
                                <td>
                                    ${doctor.schedules.map(schedule => 
                                        `<span class="badge ${schedule.is_available ? 'bg-success' : 'bg-warning'}">
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
</script>
@endsection