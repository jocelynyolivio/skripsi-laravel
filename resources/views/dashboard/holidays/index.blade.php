@extends('dashboard.layouts.main')

@section('container')
<div class="container">

    <div class="d-flex justify-content-between mb-3">
        <h3 class="text-center">Holidays</h3>
        <a href="{{ route('dashboard.holidays.create') }}" class="btn btn-primary">Add Holiday</a>

    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table id="holidaysTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($holidays as $holiday)
            <tr>
                <td>{{ $holiday->tanggal }}</td>
                <td>{{ $holiday->keterangan }}</td>
                <td>
                    <a href="{{ route('dashboard.holidays.edit', $holiday->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('dashboard.holidays.destroy', $holiday->id) }}" method="POST" style="display:inline;" class="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger btn-sm delete-button">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<script>
    $(document).ready(function() {
        $('#holidaysTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,
        });
    });

    // Event delegation for SweetAlert confirmation
    $('#holidaysTable').on('click', '.delete-button', function(e) {
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
