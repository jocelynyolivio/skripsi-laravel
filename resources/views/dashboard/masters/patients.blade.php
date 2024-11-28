@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
        <div class="d-flex justify-content-between mb-3">
        <h1 class="text-center">Master Patients</h1>
        <a href="{{ route('dashboard.masters.patients.create') }}" class="btn btn-primary mb-3">Add New Patient</a>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($patients as $patient)
            <tr>
                <td>{{ $patient->id }}</td>
                <td>{{ $patient->name }}</td>
                <td>{{ $patient->email }}</td>
                <td>{{ $patient->nomor_telepon }}</td>
                <td>
    <a href="{{ route('dashboard.masters.patients.edit', $patient->id) }}" class="btn btn-sm btn-warning">Edit</a>
    <form action="{{ route('dashboard.masters.patients.destroy', $patient->id) }}" method="POST" style="display:inline;">
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
