@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h3 class="mb-4">Schedule Overrides</h3>
    <a href="{{ route('dashboard.schedules.overrides.create') }}" class="btn btn-primary mb-3">Add New Override</a>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Doctor</th>
                <th>Date</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Available</th>
                <th>Reason</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($overrides as $override)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $override->doctor->name }}</td>
                <td>{{ $override->override_date }}</td>
                <td>{{ $override->start_time }}</td>
                <td>{{ $override->end_time }}</td>
                <td>{{ $override->is_available ? 'Yes' : 'No' }}</td>
                <td>{{ $override->reason }}</td>
                <td>
                    <a href="{{ route('dashboard.schedules.overrides.edit', $override->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('dashboard.schedules.overrides.destroy', $override->id) }}" method="POST" class="d-inline">
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