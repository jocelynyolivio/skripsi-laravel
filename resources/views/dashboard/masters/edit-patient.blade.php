@extends('dashboard.layouts.main')
@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['url' => route('dashboard.masters.patients'), 'text' => 'Master Patients'],
            ['text' => 'Edit Patient']
        ]
    ])
@endsection

@section('container')
<div class="container mt-5 col-md-8">
    <h3 class="text-center mb-4">Edit Patient</h3>

    <form action="{{ route('dashboard.masters.patients.update', $patient->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

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
                            <input type="text" name="fname" id="fname" class="form-control" value="{{ old('fname', $patient->fname) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="mname" class="form-label">Middle Name</label>
                            <input type="text" name="mname" id="mname" class="form-control" value="{{ old('mname', $patient->mname) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="lname" class="form-label">Last Name</label>
                            <input type="text" name="lname" id="lname" class="form-control" value="{{ old('lname', $patient->lname) }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="gender" class="form-label">Gender*</label>
                            <select name="gender" id="gender" class="form-select" required>
                                <option disabled {{ old('gender', $patient->gender) == '' ? 'selected' : '' }}>-- Select Gender --</option>
                                <option value="Male" {{ old('gender', $patient->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender', $patient->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ old('gender', $patient->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="nik" class="form-label">NIK*</label>
                            <input type="text" name="nik" id="nik" class="form-control" value="{{ old('nik', $patient->nik) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="blood_type" class="form-label">Blood Type*</label>
                            <input type="text" name="blood_type" id="blood_type" class="form-control" value="{{ old('blood_type', $patient->blood_type) }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="parent_name" class="form-label">Parent Name</label>
                            <input type="text" name="parent_name" id="parent_name" class="form-control" value="{{ old('parent_name', $patient->parent_name) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="occupation" class="form-label">Occupation</label>
                            <input type="text" name="occupation" id="occupation" class="form-control" value="{{ old('occupation', $patient->occupation) }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="place_of_birth" class="form-label">Place of Birth*</label>
                            <input type="text" name="place_of_birth" id="place_of_birth" class="form-control" value="{{ old('place_of_birth', $patient->place_of_birth) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="date_of_birth" class="form-label">Date of Birth*</label>
                            <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" value="{{ old('date_of_birth', $patient->date_of_birth) }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="religion" class="form-label">Religion</label>
                            <input type="text" name="religion" id="religion" class="form-control" value="{{ old('religion', $patient->religion) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="marital_status" class="form-label">Marital Status</label>
                            <select name="marital_status" id="marital_status" class="form-select">
                                <option disabled {{ old('marital_status', $patient->marital_status) == '' ? 'selected' : '' }}>-- Select Status --</option>
                                <option value="Single" {{ old('marital_status', $patient->marital_status) == 'Single' ? 'selected' : '' }}>Single</option>
                                <option value="Married" {{ old('marital_status', $patient->marital_status) == 'Married' ? 'selected' : '' }}>Married</option>
                                <option value="Divorced" {{ old('marital_status', $patient->marital_status) == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                <option value="Widowed" {{ old('marital_status', $patient->marital_status) == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="family_status" class="form-label">Family Status</label>
                            <input type="text" name="family_status" id="family_status" class="form-control" value="{{ old('family_status', $patient->family_status) }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="nationality" class="form-label">Nationality*</label>
                            <input type="text" name="nationality" id="nationality" class="form-control" value="{{ old('nationality', $patient->nationality) }}" required>
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
                    <textarea name="home_address" id="home_address" class="form-control" required>{{ old('home_address', $patient->home_address) }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="home_address_domisili" class="form-label">Domicile Address (if different)</label>
                    <textarea name="home_address_domisili" id="home_address_domisili" class="form-control">{{ old('home_address_domisili', $patient->home_address_domisili) }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="home_RT" class="form-label">RT</label>
                            <input type="text" name="home_RT" id="home_RT" class="form-control" value="{{ old('home_RT', $patient->home_RT) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="home_RW" class="form-label">RW</label>
                            <input type="text" name="home_RW" id="home_RW" class="form-control" value="{{ old('home_RW', $patient->home_RW) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="home_kelurahan" class="form-label">Kelurahan</label>
                            <input type="text" name="home_kelurahan" id="home_kelurahan" class="form-control" value="{{ old('home_kelurahan', $patient->home_kelurahan) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="home_kecamatan" class="form-label">Kecamatan</label>
                            <input type="text" name="home_kecamatan" id="home_kecamatan" class="form-control" value="{{ old('home_kecamatan', $patient->home_kecamatan) }}">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="home_city" class="form-label">City</label>
                            <input type="text" name="home_city" id="home_city" class="form-control" value="{{ old('home_city', $patient->home_city) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="home_zip_code" class="form-label">Zip Code</label>
                            <input type="text" name="home_zip_code" id="home_zip_code" class="form-control" value="{{ old('home_zip_code', $patient->home_zip_code) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="home_country" class="form-label">Country</label>
                            <input type="text" name="home_country" id="home_country" class="form-control" value="{{ old('home_country', $patient->home_country) }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="home_phone" class="form-label">Phone</label>
                            <input type="text" name="home_phone" id="home_phone" class="form-control" value="{{ old('home_phone', $patient->home_phone) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="home_mobile" class="form-label">Mobile*</label>
                            <input type="text" name="home_mobile" id="home_mobile" class="form-control" value="{{ old('home_mobile', $patient->home_mobile) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="home_email" class="form-label">Email</label>
                            <input type="email" name="home_email" id="home_email" class="form-control" value="{{ old('home_email', $patient->home_email) }}">
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
                    <textarea name="office_address" id="office_address" class="form-control">{{ old('office_address', $patient->office_address) }}</textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="office_city" class="form-label">City</label>
                            <input type="text" name="office_city" id="office_city" class="form-control" value="{{ old('office_city', $patient->office_city) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="office_zip_code" class="form-label">Zip Code</label>
                            <input type="text" name="office_zip_code" id="office_zip_code" class="form-control" value="{{ old('office_zip_code', $patient->office_zip_code) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="office_country" class="form-label">Country</label>
                            <input type="text" name="office_country" id="office_country" class="form-control" value="{{ old('office_country', $patient->office_country) }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="office_phone" class="form-label">Phone</label>
                            <input type="text" name="office_phone" id="office_phone" class="form-control" value="{{ old('office_phone', $patient->office_phone) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="office_mobile" class="form-label">Mobile</label>
                            <input type="text" name="office_mobile" id="office_mobile" class="form-control" value="{{ old('office_mobile', $patient->office_mobile) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="office_email" class="form-label">Email</label>
                            <input type="email" name="office_email" id="office_email" class="form-control" value="{{ old('office_email', $patient->office_email) }}">
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
                            <input type="text" name="emergency_contact_name" id="emergency_contact_name" class="form-control" value="{{ old('emergency_contact_name', $patient->emergency_contact_name) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="emergency_contact_relation" class="form-label">Relation*</label>
                            <input type="text" name="emergency_contact_relation" id="emergency_contact_relation" class="form-control" value="{{ old('emergency_contact_relation', $patient->emergency_contact_relation) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="emergency_contact_phone" class="form-label">Contact Phone*</label>
                            <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" class="form-control" value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone) }}" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Documents Section with existing files -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Documents</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="form_data_awal" class="form-label">
                        Initial Form Data
                        <small class="text-muted">(PDF, JPG, PNG - Max 2MB)</small>
                    </label>
                    @if($patient->form_data_awal)
                    <div class="mb-2">
                        <a href="{{ Storage::url($patient->form_data_awal) }}" target="_blank" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye"></i> View Current
                        </a>
                        <button type="button" class="btn btn-sm btn-danger" onclick="document.getElementById('delete_form_data_awal').value = '1'">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                        <input type="hidden" name="delete_form_data_awal" id="delete_form_data_awal" value="0">
                    </div>
                    @endif
                    <input type="file" name="form_data_awal" id="form_data_awal" class="form-control" accept=".pdf,.jpg,.png,.jpeg">
                </div>

                <div class="mb-3">
                    <label for="informed_consent" class="form-label">
                        Informed Consent
                        <small class="text-muted">(PDF, JPG, PNG - Max 2MB)</small>
                    </label>
                    @if($patient->informed_consent)
                    <div class="mb-2">
                        <a href="{{ Storage::url($patient->informed_consent) }}" target="_blank" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye"></i> View Current
                        </a>
                        <button type="button" class="btn btn-sm btn-danger" onclick="document.getElementById('delete_informed_consent').value = '1'">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                        <input type="hidden" name="delete_informed_consent" id="delete_informed_consent" value="0">
                    </div>
                    @endif
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
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $patient->email) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="password" class="form-label">Password (leave blank to keep current)</label>
                            <input type="password" name="password" id="password" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary btn-lg">Update Patient</button>
        </div>
    </form>
</div>
@endsection