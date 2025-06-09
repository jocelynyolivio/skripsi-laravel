@extends('dashboard.layouts.main')
@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['url' => route('dashboard.attendances.index'), 'text' => 'Attendances'],
            ['text' => 'Create Attendances']
        ]
    ])
@endsection
@section('container')
<div class="container mt-5 col-md-6">
    <h3 class="text-center">Create Attendances</h3>

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

    <form id="createAttendanceForm" action="{{ route('dashboard.attendances.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>User <span class="text-danger">*</span></label>
            <select name="no_id" class="form-control" required>
                <option value="">Pilih User</option>
                @foreach($users as $user)
                <option value="{{ $user->id }}">{{ $user->id }} - {{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Tanggal <span class="text-danger">*</span></label>
            <input type="date" name="tanggal" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Jam Masuk <span class="text-danger">*</span></label>
            <input type="time" name="jam_masuk" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Jam Pulang <span class="text-danger">*</span></label>
            <input type="time" name="jam_pulang" class="form-control" required>
        </div>
        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
        <button type="submit" class="btn btn-primary">Create</button>
        <a href="{{ route('dashboard.attendances.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
<script>
    document.getElementById('createAttendanceForm').addEventListener('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Confirm Attendance',
            text: "Are you sure you want to create this attendance?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, sure!'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
</script>
@endsection
