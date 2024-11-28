@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h3 class="text-center">Edit Patient</h3>

    <form action="{{ route('dashboard.masters.patients.update', $patient->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $patient->name }}" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ $patient->email }}" required>
        </div>

        <div class="mb-3">
            <label for="nomor_telepon" class="form-label">Phone</label>
            <input type="text" name="nomor_telepon" id="nomor_telepon" class="form-control" value="{{ $patient->nomor_telepon }}" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Update Patient</button>
    </form>
</div>
@endsection
