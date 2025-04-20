@extends('dashboard.layouts.main')
@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['text' => 'Master Patients']
        ]
    ])
@endsection
@section('container')
<div class="container mt-5">
    <div class="d-flex justify-content-between mb-3">
        <h3 class="text-center">Master Patients</h3>
        <a href="{{ route('dashboard.masters.patients.create') }}" class="btn btn-primary mb-3">Create New Patient</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    <table id="patientTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Patient ID</th>
                <th>Name</th>
                <th>Gender</th>
                <th>Date of Birth</th>
                <th>Mobile Number</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($patients as $patient)
            <tr>
                <td>{{ $patient->id }}</td>
                <td>{{ $patient->patient_id }}</td>
                <td>{{ $patient->fname }} {{ $patient->mname }} {{ $patient->lname }}</td>
                <td>{{ $patient->gender }}</td>
                <td>{{ $patient->date_of_birth}}</td>
                <td>{{ $patient->home_mobile }}</td>
                <td>
                    <a href="{{ route('dashboard.masters.patients.edit', $patient->id) }}" class="btn btn-sm btn-warning">Show/Edit</a>
                    <form action="{{ route('dashboard.masters.patients.destroy', $patient->id) }}" method="POST" style="display:inline;" class="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-sm btn-danger delete-button">Delete</button>
                    </form>
                    <!-- Tombol baru untuk melihat rekam medis pasien -->
                    <a href="{{ route('dashboard.medical_records.index', ['patientId' => $patient->id]) }}" class="btn btn-sm btn-info">View Medical Records</a>
                    <a href="{{ route('dashboard.odontograms.index', ['patientId' => $patient->id]) }}" class="btn btn-sm btn-primary">View Odontogram</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        setTimeout(function() {
            $('#patientTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "responsive": true,
                "columnDefs": [
                    { "orderable": false, "targets": 4 } // Kolom ke-4 adalah kolom Actions
                ]
            });
        }, 100);
    });

    // Event delegation for SweetAlert confirmation
    $('#patientTable').on('click', '.delete-button', function(e) {
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
