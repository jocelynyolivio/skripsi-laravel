@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">

    <div class="d-flex justify-content-between mb-3">
    <h3 class="mb-4">Schedule Templates</h3>
    <a href="{{ route('dashboard.schedules.templates.create') }}" class="btn btn-primary mb-3">Add New Template</a>
    </div>

    @if($templates->isEmpty())
        <p>No templates available.</p>
    @else
        <table class="table table-striped" id="templateTable">
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
                        <form action="{{ route('dashboard.schedules.templates.destroy', $template->id) }}" method="POST" class="d-inline delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-sm btn-danger delete-button">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
<script>
    $(document).ready(function() {
        $('#templateTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,   
            "info": true,
            "responsive": true,
        });

        // Ensure the delete confirmation is set up correctly
        $('#templateTable').on('click', '.delete-button', function(e) {
            e.preventDefault(); // Prevent the default form submission
            var form = $(this).closest('form'); // Get the closest form element
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
                    form.submit(); // Submit the form if confirmed
                }
            });
        });
    });
</script>


@endsection