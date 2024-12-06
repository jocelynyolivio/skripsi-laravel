@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <div class="d-flex justify-content-between mb-3">
        <h3 class="text-center">Schedule List</h3>
        <a href="{{ route('dashboard.schedules.create') }}" class="btn btn-primary">Add Schedule</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <table id="scheduleTable" class="display">
        <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Doctor</th>
                <th>Date</th>
                <th>Start Time</th>
                <th>End Time</th>
                <!-- <th>Status</th> -->
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($schedules as $schedule)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $schedule->doctor->name }}</td>
                <td>{{ $schedule->date }}</td>
                <td>{{ $schedule->time_start }}</td>
                <td>{{ $schedule->time_end }}</td>
                <!-- <td>{{ $schedule->is_available ? 'Available' : 'Reserved' }}</td> -->
                <td>
                    @if($schedule->is_available)
                    <a href="{{ route('dashboard.schedules.edit', $schedule->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('dashboard.schedules.destroy', $schedule->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                    @else
                    <span class="badge bg-danger">Reserved</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<script>
    $(document).ready(function() {
        $('#scheduleTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,
        });
    });
</script>
@endsection