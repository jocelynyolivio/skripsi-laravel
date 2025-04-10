@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">


    <div class="d-flex justify-content-between mb-3">
    <h3 class="mb-4">Schedule Overrides</h3>
    <a href="{{ route('dashboard.schedules.overrides.create') }}" class="btn btn-primary mb-3">Add New Override</a>
    </div>

    <table class="table table-striped" id="overrideTable">
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
                    <form action="{{ route('dashboard.schedules.overrides.destroy', $override->id) }}" method="POST" class="d-inline delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-sm btn-danger delete-button">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('#overrideTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,
        });
    });
    $('#overrideTable').on('click', '.delete-button', function(e) {
        e.preventDefault();
        var form = $(this).closest('form');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
</script>

@endsection 