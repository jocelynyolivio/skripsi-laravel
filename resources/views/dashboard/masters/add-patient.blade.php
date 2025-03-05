@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5 col-md-8">
    <h3 class="text-center mb-4">Add New Patient</h3>

    <form action="{{ route('dashboard.masters.patients.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Section: Informasi Pasien -->
        <h5 class="mb-3">Patient Information</h5>
        <div class="mb-3">
            <label for="name" class="form-label">Name (*)</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="gender" class="form-label">Gender (*)</label>
            <select name="gender" id="gender" class="form-select" required>
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="nik" class="form-label">NIK (National ID) (*)</label>
            <input type="text" name="nik" id="nik" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="blood_type" class="form-label">Blood Type (*)</label>
            <input type="text" name="blood_type" id="blood_type" class="form-control" required>
        </div>

        <!-- Section: Status & Pekerjaan -->
        <h5 class="mt-4 mb-3">Status & Occupation</h5>
        <div class="mb-3">
            <label for="place_of_birth" class="form-label">Place of Birth (*)</label>
            <input type="text" name="place_of_birth" id="place_of_birth" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="date_of_birth" class="form-label">Date of Birth (*)</label>
            <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="religion" class="form-label">Religion</label>
            <input type="text" name="religion" id="religion" class="form-control">
        </div>
        <div class="mb-3">
            <label for="marital_status" class="form-label">Marital Status</label>
            <select name="marital_status" id="marital_status" class="form-select">
                <option value="">Select Marital Status</option>
                <option value="Single">Single</option>
                <option value="Married">Married</option>
                <option value="Divorced">Divorced</option>
                <option value="Widowed">Widowed</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="occupation" class="form-label">Occupation</label>
            <input type="text" name="occupation" id="occupation" class="form-control">
        </div>
        <div class="mb-3">
            <label for="nationality" class="form-label">Nationality (*)</label>
            <input type="text" name="nationality" id="nationality" class="form-control" required>
        </div>

        <!-- Section: Alamat Rumah -->
        <h5 class="mt-4 mb-3">Home Address</h5>
        <div class="mb-3">
            <label for="home_address" class="form-label">Address (*)</label>
            <textarea name="home_address" id="home_address" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label for="home_mobile" class="form-label">Mobile Number (*)</label>
            <input type="text" name="home_mobile" id="home_mobile" class="form-control" required>
        </div>

        <!-- Section: Kontak Darurat -->
        <h5 class="mt-4 mb-3">Emergency Contact</h5>
        <div class="mb-3">
            <label for="emergency_contact_name" class="form-label">Contact Name (*)</label>
            <input type="text" name="emergency_contact_name" id="emergency_contact_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="emergency_contact_phone" class="form-label">Contact Phone (*)</label>
            <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" class="form-control" required>
        </div>

        <!-- Section: Upload Files -->
        <h5 class="mt-4 mb-3">Upload Documents</h5>
        <div class="mb-3">
            <label for="form_data_awal" class="form-label">Upload Form Data Awal</label>
            <input type="file" name="form_data_awal" id="form_data_awal" class="form-control">
        </div>
        <div class="mb-3">
            <label for="informed_consent" class="form-label">Upload Informed Consent</label>
            <input type="file" name="informed_consent" id="informed_consent" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary w-100 mt-4">Add Patient</button>
    </form>
</div>
@endsection
