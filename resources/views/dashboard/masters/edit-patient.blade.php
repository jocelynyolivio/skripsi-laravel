@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5 col-md-8">
    <h3 class="text-center mb-4">Edit Patient</h3>

    <form action="{{ route('dashboard.masters.patients.update', $patient->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Section: Informasi Pasien -->
        <h5 class="mb-3">Patient Information</h5>
        <div class="mb-3">
            <label for="patient_id" class="form-label">Patient ID</label>
            <input type="text" name="patient_id" id="patient_id" class="form-control" value="{{ $patient->patient_id }}" readonly>
        </div>
        <div class="mb-3">
            <label for="name" class="form-label">Name (*)</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $patient->name }}" required>
        </div>
        <div class="mb-3">
            <label for="gender" class="form-label">Gender (*)</label>
            <select name="gender" id="gender" class="form-select" required>
                <option value="Male" {{ $patient->gender == 'Male' ? 'selected' : '' }}>Male</option>
                <option value="Female" {{ $patient->gender == 'Female' ? 'selected' : '' }}>Female</option>
                <option value="Other" {{ $patient->gender == 'Other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="nik" class="form-label">NIK (*)</label>
            <input type="text" name="nik" id="nik" class="form-control" value="{{ $patient->nik }}" required>
        </div>
        <div class="mb-3">
            <label for="blood_type" class="form-label">Blood Type (*)</label>
            <input type="text" name="blood_type" id="blood_type" class="form-control" value="{{ $patient->blood_type }}" required>
        </div>

        <!-- Section: Alamat Rumah -->
        <h5 class="mt-4 mb-3">Home Address</h5>
        <div class="mb-3">
            <label for="home_address" class="form-label">Address (*)</label>
            <textarea name="home_address" id="home_address" class="form-control" required>{{ $patient->home_address }}</textarea>
        </div>
        <div class="mb-3">
            <label for="home_mobile" class="form-label">Mobile Number (*)</label>
            <input type="text" name="home_mobile" id="home_mobile" class="form-control" value="{{ $patient->home_mobile }}" required>
        </div>

        <!-- Section: Kontak Darurat -->
        <h5 class="mt-4 mb-3">Emergency Contact</h5>
        <div class="mb-3">
            <label for="emergency_contact_name" class="form-label">Contact Name (*)</label>
            <input type="text" name="emergency_contact_name" id="emergency_contact_name" class="form-control" value="{{ $patient->emergency_contact_name }}" required>
        </div>
        <div class="mb-3">
            <label for="emergency_contact_phone" class="form-label">Contact Phone (*)</label>
            <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" class="form-control" value="{{ $patient->emergency_contact_phone }}" required>
        </div>

        <!-- Section: Upload Files -->
        <h5 class="mt-4 mb-3">Upload Documents</h5>
        <div class="mb-3">
            <label for="form_data_awal" class="form-label">Upload Form Data Awal</label>
            <input type="file" name="form_data_awal" id="form_data_awal" class="form-control">
            @if($patient->form_data_awal)
                <p class="mt-2">Current File: <a href="{{ asset('storage/' . $patient->form_data_awal) }}" target="_blank">View File</a></p>
            @endif
        </div>
        <div class="mb-3">
            <label for="informed_consent" class="form-label">Upload Informed Consent</label>
            <input type="file" name="informed_consent" id="informed_consent" class="form-control">
            @if($patient->informed_consent)
                <p class="mt-2">Current File: <a href="{{ asset('storage/' . $patient->informed_consent) }}" target="_blank">View File</a></p>
            @endif
        </div>

        <button type="submit" class="btn btn-primary w-100 mt-4">Update Patient</button>
    </form>
</div>
@endsection
