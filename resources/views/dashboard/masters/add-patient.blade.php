@extends('dashboard.layouts.main')
@section('breadcrumbs')
@include('dashboard.layouts.breadcrumbs', [
'customBreadcrumbs' => [
['url' => route('dashboard.masters.patients'), 'text' => 'Master Patients'],
['text' => 'Create New Patient'],
],
])
@endsection
@section('container')
<div class="container mt-5 col-md-8">
    <h3 class="text-center mb-4">Create New Patient</h3>

    {{-- Menampilkan error validasi --}}
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form id="createPatientForm" action="{{ route('dashboard.masters.patients.store') }}" method="POST"
        enctype="multipart/form-data">
        @csrf

        <div class="card mb-4">
            <div class="card-header">
                <h5>Personal Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="fname" class="form-label">First Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="fname" id="fname" class="form-control" placeholder="John"
                                required value="{{ old('fname') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="mname" class="form-label">Middle Name</label>
                            <input type="text" name="mname" id="mname" class="form-control"
                                placeholder="William" value="{{ old('mname') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="lname" class="form-label">Last Name</label>
                            <input type="text" name="lname" id="lname" class="form-control" placeholder="Doe"
                                value="{{ old('lname') }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                            <select name="gender" id="gender" class="form-select" required>
                                <option value="" selected disabled>Select Gender</option>
                                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male
                                </option>
                                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female
                                </option>
                                <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="nik" class="form-label">NIK <span class="text-danger">*</span></label>
                            <input type="text" name="nik" id="nik" class="form-control"
                                placeholder="3171234567890123" required value="{{ old('nik') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="blood_type" class="form-label">Blood Type <span
                                    class="text-danger">*</span></label>
                            <select name="blood_type" id="blood_type" class="form-select" required>
                                <option value="" selected disabled>Select Blood Type</option>
                                <option value="A+" {{ old('blood_type') == 'A+' ? 'selected' : '' }}>A+</option>
                                <option value="A-" {{ old('blood_type') == 'A-' ? 'selected' : '' }}>A-</option>
                                <option value="B+" {{ old('blood_type') == 'B+' ? 'selected' : '' }}>B+</option>
                                <option value="B-" {{ old('blood_type') == 'B-' ? 'selected' : '' }}>B-</option>
                                <option value="AB+" {{ old('blood_type') == 'AB+' ? 'selected' : '' }}>AB+
                                </option>
                                <option value="AB-" {{ old('blood_type') == 'AB-' ? 'selected' : '' }}>AB-
                                </option>
                                <option value="O+" {{ old('blood_type') == 'O+' ? 'selected' : '' }}>O+</option>
                                <option value="O-" {{ old('blood_type') == 'O-' ? 'selected' : '' }}>O-</option>
                                <option value="Unknown" {{ old('blood_type') == 'Unknown' ? 'selected' : '' }}>
                                    Unknown</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="parent_name" class="form-label">Parent Name</label>
                            <input type="text" name="parent_name" id="parent_name" class="form-control"
                                placeholder="Robert Doe" value="{{ old('parent_name') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="occupation" class="form-label">Occupation</label>
                            <input type="text" name="occupation" id="occupation" class="form-control"
                                placeholder="Doctor" value="{{ old('occupation') }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="place_of_birth" class="form-label">Place of Birth <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="place_of_birth" id="place_of_birth" class="form-control"
                                placeholder="Jakarta" required value="{{ old('place_of_birth') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="date_of_birth" class="form-label">Date of Birth <span
                                    class="text-danger">*</span></label>
                            <input type="date" name="date_of_birth" id="date_of_birth" class="form-control"
                                required value="{{ old('date_of_birth') }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="religion" class="form-label">Religion</label>
                            <input type="text" name="religion" id="religion" class="form-control"
                                placeholder="Islam" value="{{ old('religion') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="marital_status" class="form-label">Marital Status</label>
                            <select name="marital_status" id="marital_status" class="form-select">
                                <option value="">Select Status</option>
                                <option value="Single" {{ old('marital_status') == 'Single' ? 'selected' : '' }}>
                                    Single</option>
                                <option value="Married" {{ old('marital_status') == 'Married' ? 'selected' : '' }}>
                                    Married</option>
                                <option value="Divorced"
                                    {{ old('marital_status') == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                <option value="Widowed" {{ old('marital_status') == 'Widowed' ? 'selected' : '' }}>
                                    Widowed</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="family_status" class="form-label">Family Status</label>
                            <input type="text" name="family_status" id="family_status" class="form-control"
                                placeholder="Head of Family" value="{{ old('family_status') }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="nationality" class="form-label">Nationality</label>
                            <input type="text" name="nationality" id="nationality" class="form-control"
                                placeholder="Indonesian" value="{{ old('nationality', 'Indonesian') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5>Home Address</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="home_address" class="form-label">Address <span
                            class="text-danger">*</span></label>
                    <textarea name="home_address" id="home_address" class="form-control" placeholder="Jl. Sudirman No. 123" required>{{ old('home_address') }}</textarea>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" name="same_as_home_address"
                        id="same_as_home_address" {{ old('same_as_home_address') ? 'checked' : '' }}>
                    <label class="form-check-label" for="same_as_home_address">Domicile address is same as home
                        address</label>
                </div>

                <div class="mb-3" id="domicile_address_container">
                    <label for="home_address_domisili" class="form-label">Domicile Address (if
                        different)</label>
                    <textarea name="home_address_domisili" id="home_address_domisili" class="form-control"
                        placeholder="Jl. Thamrin No. 456">{{ old('home_address_domisili') }}</textarea>
                </div>


                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="home_RT" class="form-label">RT</label>
                            <input type="text" name="home_RT" id="home_RT" class="form-control"
                                placeholder="001" value="{{ old('home_RT') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="home_RW" class="form-label">RW</label>
                            <input type="text" name="home_RW" id="home_RW" class="form-control"
                                placeholder="002" value="{{ old('home_RW') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="home_kelurahan" class="form-label">Kelurahan</label>
                            <input type="text" name="home_kelurahan" id="home_kelurahan" class="form-control"
                                placeholder="Menteng" value="{{ old('home_kelurahan') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="home_kecamatan" class="form-label">Kecamatan</label>
                            <input type="text" name="home_kecamatan" id="home_kecamatan" class="form-control"
                                placeholder="Jakarta Pusat" value="{{ old('home_kecamatan') }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="home_city" class="form-label">City</label>
                            <input type="text" name="home_city" id="home_city" class="form-control"
                                placeholder="Jakarta" value="{{ old('home_city') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="home_zip_code" class="form-label">Zip Code</label>
                            <input type="text" name="home_zip_code" id="home_zip_code" class="form-control"
                                placeholder="10310" value="{{ old('home_zip_code') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="home_country" class="form-label">Country</label>
                            <input type="text" name="home_country" id="home_country" class="form-control"
                                placeholder="Indonesia" value="{{ old('home_country', 'Indonesia') }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="home_phone" class="form-label">Phone</label>
                            <input type="text" name="home_phone" id="home_phone" class="form-control"
                                placeholder="0211234567" value="{{ old('home_phone') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="home_mobile" class="form-label">Mobile <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">+62</span>
                                <input type="text" class="form-control" name="home_mobile" id="home_mobile"
                                    placeholder="8123456789" required value="{{ old('home_mobile') }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="home_email" class="form-label">Email</label>
                            <input type="email" name="home_email" id="home_email" class="form-control"
                                placeholder="john.doe@example.com" value="{{ old('home_email') }}">
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
                    <textarea name="office_address" id="office_address" class="form-control" placeholder="Jl. Gatot Subroto No. 789"></textarea>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="office_city" class="form-label">City</label>
                            <input type="text" name="office_city" id="office_city" class="form-control" placeholder="Jakarta">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="office_zip_code" class="form-label">Zip Code</label>
                            <input type="text" name="office_zip_code" id="office_zip_code" class="form-control" placeholder="12950">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="office_country" class="form-label">Country</label>
                            <input type="text" name="office_country" id="office_country" class="form-control" placeholder="Indonesia">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="office_phone" class="form-label">Phone</label>
                            <input type="text" name="office_phone" id="office_phone" class="form-control" placeholder="0219876543">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="office_mobile" class="form-label">Mobile</label>
                            {{-- DIUBAH: Tambah format +62 --}}
                            <div class="input-group">
                                <span class="input-group-text">+62</span>
                                <input type="text" name="office_mobile" id="office_mobile" class="form-control"
                                    placeholder="8123456789" value="{{ old('office_mobile') }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="office_email" class="form-label">Email</label>
                            <input type="email" name="office_email" id="office_email" class="form-control" placeholder="office@example.com">
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
                            <label for="emergency_contact_name" class="form-label">Contact Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="emergency_contact_name" id="emergency_contact_name"
                                class="form-control" placeholder="Jane Doe" required
                                value="{{ old('emergency_contact_name') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="emergency_contact_relation" class="form-label">Relation <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="emergency_contact_relation"
                                id="emergency_contact_relation" class="form-control" placeholder="Spouse"
                                required value="{{ old('emergency_contact_relation') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="emergency_contact_phone" class="form-label">Contact Phone <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">+62</span>
                                <input type="text" name="emergency_contact_phone"
                                    id="emergency_contact_phone" class="form-control" placeholder="8123456789"
                                    required value="{{ old('emergency_contact_phone') }}">
                            </div>
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
                            <input type="email" name="email" id="email" class="form-control" placeholder="john.doe@example.com">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Minimum 8 characters">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <button type="submit" class="btn btn-primary px-4">Create Patient</button>
            <a href="{{ route('dashboard.masters.patients') }}" class="btn btn-secondary px-4">Cancel</a>
        </div>
    </form>
</div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- LOGIKA UNTUK CHECKBOX ALAMAT ---
            const sameAddressCheckbox = document.getElementById('same_as_home_address');
            const homeAddressField = document.getElementById('home_address');
            const domisiliAddressField = document.getElementById('home_address_domisili');
            const domisiliContainer = document.getElementById('domicile_address_container');

            function handleAddressCheckbox() {
                if (sameAddressCheckbox.checked) {
                    domisiliAddressField.value = homeAddressField.value;
                    domisiliAddressField.disabled = true;
                    domisiliContainer.style.opacity = '0.6';
                } else {
                    domisiliAddressField.disabled = false;
                    domisiliContainer.style.opacity = '1';
                }
            }

            sameAddressCheckbox.addEventListener('change', handleAddressCheckbox);
            homeAddressField.addEventListener('input', function() {
                if (sameAddressCheckbox.checked) {
                    domisiliAddressField.value = this.value;
                }
            });
            // Jalankan sekali saat load untuk handle old() value
            handleAddressCheckbox();


            // --- LOGIKA UNTUK SUBMIT FORM DENGAN SWEETALERT ---
            const createPatientForm = document.getElementById('createPatientForm');

            createPatientForm.addEventListener('submit', function(e) {
                // 1. Selalu cegah submit default di awal
                e.preventDefault();

                // 2. Validasi field yang required
                let isValid = true;
                const requiredFields = createPatientForm.querySelectorAll('[required]');

                requiredFields.forEach(field => {
                    // Hapus dulu class invalid sebelumnya
                    field.classList.remove('is-invalid');
                    
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('is-invalid'); // Tandai field yang error
                    }
                });

                if (!isValid) {
                    Swal.fire({
                        title: 'Gagal!',
                        text: 'Harap isi semua field yang wajib ditandai dengan bintang (*).',
                        icon: 'error',
                        confirmButtonText: 'Mengerti'
                    });
                    return; // Hentikan proses jika validasi gagal
                }


                // 3. Jika validasi lolos, tampilkan dialog konfirmasi
                Swal.fire({
                    title: 'Confirmation Patient Creation',
                    text: "Are you sure want to create this patient data?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, sure!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    // 4. Jika pengguna menekan "Ya"
                    if (result.isConfirmed) {
                        
                        // 5. Tampilkan alert "loading"
                        Swal.fire({
                            title: 'Memproses Data...',
                            text: 'Mohon tunggu sebentar.',
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // 6. Kirim form secara programmatic
                        createPatientForm.submit();
                    }
                });
            });
        });
    </script>
    <style>
        .is-invalid {
            border-color: #dc3545 !important;
        }

        #domicile_address_container {
            transition: opacity 0.3s ease;
        }

        textarea:disabled {
            background-color: #e9ecef;
            /* Warna standar bootstrap untuk disabled */
        }
    </style>
@endsection