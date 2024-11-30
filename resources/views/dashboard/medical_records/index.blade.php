@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h3 class="text-center">Medical Records for Patient ID: {{ $patientName }}</h3>
    <a href="{{ route('dashboard.medical_records.create', ['patientId' => $patientId]) }}" class="btn btn-primary mb-3">Add Medical Record</a>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif


    <table class="table table-striped">
        <thead>
            <tr>
                <th>Date</th>
                <th>Teeth Condition</th>
                <th>Treatment</th>
                <th>Notes</th>
                <th>Doctor</th>
            </tr>
        </thead>
        <tbody>
            @foreach($medicalRecords as $record)
            <tr>
                <td>{{ $record->date }}</td>
                <td>{{ $record->teeth_condition }}</td>
                <td>{{ $record->treatment }}</td>
                <td>{{ $record->notes }}</td>
                <td>{{ $record->doctor->name }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
