@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h3 class="mb-4">Schedule Templates</h3>
    <a href="{{ route('dashboard.schedules.templates.create') }}" class="btn btn-primary mb-3">Add New Template</a>


    @if($templates->isEmpty())
        <p>No templates available.</p>
    @else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Doctor</th>
                    <th>Day of Week</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Active</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($templates as $template)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $template->doctor->name }}</td>
                    <td>{{ $template->day_of_week }}</td>
                    <td>{{ $template->start_time }}</td>
                    <td>{{ $template->end_time }}</td>
                    <td>{{ $template->is_active ? 'Yes' : 'No' }}</td>
                    <td>
                        <a href="{{ route('dashboard.schedules.templates.edit', $template->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('dashboard.schedules.templates.destroy', $template->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection