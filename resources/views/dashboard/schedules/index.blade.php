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

    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Doctor</th>
                <th>Date</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Status</th>
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
                <td>{{ $schedule->is_available ? 'Available' : 'Reserved' }}</td>
                <td>
                    <a href="{{ route('dashboard.schedules.edit', $schedule->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('dashboard.schedules.destroy', $schedule->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection