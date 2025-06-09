@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h3 class="text-center">Select Medical Record for Transaction</h3>
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
    <!-- <table class="table table-striped mt-4"> -->
    <table id="selectMedicalForTransactionTable" class="table table-striped mt-4">
        <thead>
            <tr>
                <th>ID</th>
                <th>Patient</th>
                <th>Doctor</th>
                <th>Reservation Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($medicalRecords as $medicalRecord)
            <tr>
                <td>{{ $medicalRecord->id }}</td>
                <td>{{ $medicalRecord->patient->fname }} {{ $medicalRecord->patient->mname }} {{ $medicalRecord->patient->lname }}</td>
                <td>{{ $medicalRecord->doctor->name }}</td>
                <td>{{ $medicalRecord->tanggal_reservasi }}</td>
                <td>
                    <a href="{{ route('dashboard.transactions.create', $medicalRecord->id) }}" class="btn btn-success">
                        Create Transaction
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('#selectMedicalForTransactionTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,
        });
    });
</script>
@endsection
