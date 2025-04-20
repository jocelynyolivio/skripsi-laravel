@extends('dashboard.layouts.main')
@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['url' => route('dashboard.masters.patients'), 'text' => 'Master Patients'],
            ['text' => 'Create New Patient']
        ]
    ])
@endsection
@section('container')
<div class="container mt-5 col-md-8">
    <h3 class="text-center mb-4">Create New Patient</h3>

    <form id="addPatientForm" action="{{ route('dashboard.masters.patients.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Personal Information Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Personal Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="fname" class="form-label">First Name*</label>
                            <input type="text" name="fname" id="fname" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="mname" class="form-label">Middle Name</label>
                            <input type="text" name="mname" id="mname" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="lname" class="form-label">Last Name</label>
                            <input type="text" name="lname" id="lname" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="gender" class="form-label">Gender*</label>
                            <select name="gender" id="gender" class="form-select" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="nik" class="form-label">NIK*</label>
                            <input type="text" name="nik" id="nik" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="blood_type" class="form-label">Blood Type*</label>
                            <input type="text" name="blood_type" id="blood_type" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="parent_name" class="form-label">Parent Name</label>
                            <input type="text" name="parent_name" id="parent_name" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="occupation" class="form-label">Occupation</label>
                            <input type="text" name="occupation" id="occupation" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="place_of_birth" class="form-label">Place of Birth*</label>
                            <input type="text" name="place_of_birth" id="place_of_birth" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="date_of_birth" class="form-label">Date of Birth*</label>
                            <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="religion" class="form-label">Religion</label>
                            <input type="text" name="religion" id="religion" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="marital_status" class="form-label">Marital Status</label>
                            <select name="marital_status" id="marital_status" class="form-select">
                                <option value="">Select Status</option>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Divorced">Divorced</option>
                                <option value="Widowed">Widowed</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="family_status" class="form-label">Family Status</label>
                            <input type="text" name="family_status" id="family_status" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="nationality" class="form-label">Nationality*</label>
                            <input type="text" name="nationality" id="nationality" class="form-control" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Home Address Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Home Address</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="home_address" class="form-label">Address*</label>
                    <textarea name="home_address" id="home_address" class="form-control" required>{{ old('home_address') }}</textarea>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" name="same_as_home_address" id="same_as_home_address"
                        {{ old('same_as_home_address', false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="same_as_home_address">Domicile address is same as home address</label>
                </div>

                <div class="mb-3" id="domicile_address_container">
                    <label for="home_address_domisili" class="form-label">Domicile Address (if different)</label>
                    <textarea name="home_address_domisili" id="home_address_domisili" class="form-control"
                        {{ old('same_as_home_address', false) ? 'readonly' : '' }}>{{ old('home_address_domisili') }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="home_RT" class="form-label">RT</label>
                            <input type="text" name="home_RT" id="home_RT" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="home_RW" class="form-label">RW</label>
                            <input type="text" name="home_RW" id="home_RW" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="home_kelurahan" class="form-label">Kelurahan</label>
                            <input type="text" name="home_kelurahan" id="home_kelurahan" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="home_kecamatan" class="form-label">Kecamatan</label>
                            <input type="text" name="home_kecamatan" id="home_kecamatan" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="home_city" class="form-label">City</label>
                            <input type="text" name="home_city" id="home_city" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="home_zip_code" class="form-label">Zip Code</label>
                            <input type="text" name="home_zip_code" id="home_zip_code" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="home_country" class="form-label">Country</label>
                            <input type="text" name="home_country" id="home_country" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="home_phone" class="form-label">Phone</label>
                            <input type="text" name="home_phone" id="home_phone" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="home_mobile" class="form-label">Mobile*</label>
                            <div class="input-group">
                                <span class="input-group-text">+62</span>
                                <input type="text" class="form-control" name="home_mobile" id="home_mobile" placeholder="8123456789" value="{{ old('home_mobile') }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="home_email" class="form-label">Email</label>
                            <input type="email" name="home_email" id="home_email" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Office Address Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Office Address (Optional)</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="office_address" class="form-label">Address</label>
                    <textarea name="office_address" id="office_address" class="form-control"></textarea>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="office_city" class="form-label">City</label>
                            <input type="text" name="office_city" id="office_city" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="office_zip_code" class="form-label">Zip Code</label>
                            <input type="text" name="office_zip_code" id="office_zip_code" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="office_country" class="form-label">Country</label>
                            <input type="text" name="office_country" id="office_country" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="office_phone" class="form-label">Phone</label>
                            <input type="text" name="office_phone" id="office_phone" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="office_mobile" class="form-label">Mobile</label>
                            <input type="text" name="office_mobile" id="office_mobile" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="office_email" class="form-label">Email</label>
                            <input type="email" name="office_email" id="office_email" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Emergency Contact Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Emergency Contact</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="emergency_contact_name" class="form-label">Contact Name*</label>
                            <input type="text" name="emergency_contact_name" id="emergency_contact_name" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="emergency_contact_relation" class="form-label">Relation*</label>
                            <input type="text" name="emergency_contact_relation" id="emergency_contact_relation" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="emergency_contact_phone" class="form-label">Contact Phone*</label>
                            <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" class="form-control" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Documents Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Documents (Optional)</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="form_data_awal" class="form-label">
                        Initial Form Data
                        <small class="text-muted">(PDF, JPG, PNG - Max 2MB)</small>
                    </label>
                    <input type="file" name="form_data_awal" id="form_data_awal" class="form-control" accept=".pdf,.jpg,.png,.jpeg">
                </div>
                <div class="mb-3">
                    <label for="informed_consent" class="form-label">
                        Informed Consent
                        <small class="text-muted">(PDF, JPG, PNG - Max 2MB)</small>
                    </label>
                    <input type="file" name="informed_consent" id="informed_consent" class="form-control" accept=".pdf,.jpg,.png,.jpeg">
                </div>
            </div>
        </div>

        <!-- Account Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Account Information (Optional)</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary btn-lg">Create Patient</button>
        </div>
    </form>
</div>

<script>
    // Client-side validation
    document.getElementById('addPatientForm').addEventListener('submit', function(e) {
        let isValid = true;

        // Check required fields
        const requiredFields = this.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Please fill all required fields.');
        }
    });
</script>

<style>
    .is-invalid {
        border-color: #dc3545;
    }

    #domicile_address_container {
        transition: opacity 0.3s ease;
    }

    textarea:disabled {
        background-color: #f8f9fa;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sameAddressCheckbox = document.getElementById('same_as_home_address');
        const homeAddressField = document.getElementById('home_address');
        const domisiliAddressField = document.getElementById('home_address_domisili');
        const domisiliContainer = document.getElementById('domicile_address_container');

        sameAddressCheckbox.addEventListener('change', function() {
            if (this.checked) {
                // Copy value from home address to domisili address
                domisiliAddressField.value = homeAddressField.value;
                domisiliAddressField.disabled = true;
                domisiliContainer.style.opacity = '0.5';
            } else {
                // Clear and enable domisili address field
                domisiliAddressField.value = '';
                domisiliAddressField.disabled = false;
                domisiliContainer.style.opacity = '1';
            }
        });

        // Optional: Update domisili address if home address changes while checkbox is checked
        homeAddressField.addEventListener('input', function() {
            if (sameAddressCheckbox.checked) {
                domisiliAddressField.value = this.value;
            }
        });
    });
</script>
@endsection