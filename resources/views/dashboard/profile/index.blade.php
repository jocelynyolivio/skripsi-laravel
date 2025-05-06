@extends('dashboard.layouts.main')

@section('breadcrumbs')
@include('dashboard.layouts.breadcrumbs', [
'customBreadcrumbs' => [
['text' => 'Profile']
]
])
@endsection

@section('container')
<div class="container mt-5">
    <div class="row">
        
    <div class="d-flex justify-content-between mb-3">
        <h3 class="text-center">{{ $user->name }} - {{ $user->role->name ?? 'No Role' }}</h3>
        <a href="{{ route('dashboard.salaries.slips') }}" class="btn btn-success">Attendances & Slips</a>

    </div>

        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Profile Information</h5>
                </div>

                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    {{-- Profile fields --}}
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Full Name</strong></div>
                        <div class="col-sm-9 text-secondary">{{ $user->name }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Email</strong></div>
                        <div class="col-sm-9 text-secondary">{{ $user->email }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Phone</strong></div>
                        <div class="col-sm-9 text-secondary">{{ $user->nomor_telepon ?? '-' }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Address</strong></div>
                        <div class="col-sm-9 text-secondary">{{ $user->alamat ?? '-' }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Place, Date of Birth</strong></div>
                        <div class="col-sm-9 text-secondary">
                            {{ $user->tempat_lahir ?? '-' }},
                            {{ $user->tanggal_lahir ? \Carbon\Carbon::parse($user->tanggal_lahir)->format('d F Y') : '-' }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>NIK</strong></div>
                        <div class="col-sm-9 text-secondary">{{ $user->nik ?? '-' }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Bank Account</strong></div>
                        <div class="col-sm-9 text-secondary">{{ $user->nomor_rekening ?? '-' }}</div>
                    </div>

                    {{-- Edit button --}}
                    <div class="row">
                        <div class="col-sm-12">
                            <a href="{{ route('dashboard.profile.edit') }}" class="btn btn-warning">
                                <i class="bi bi-pencil-square"></i> Edit Profile
                            </a>
                        </div>
                    </div>
                </div> <!-- card-body -->
            </div> <!-- card -->
        </div> <!-- col -->
    </div> <!-- row -->
</div> <!-- container -->
@endsection