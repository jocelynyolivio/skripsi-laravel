@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-4">
    <h2>Edit Absensi</h2>

    <form method="POST" action="{{ route('attendances.update', $attendance->id) }}">
        @csrf @method('PUT')
        <div class="mb-3">
            <label>No ID</label>
            <input type="number" name="no_id" class="form-control" value="{{ $attendance->no_id }}" required>
        </div>

        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="nama" class="form-control" value="{{ $attendance->nama }}" required>
        </div>

        <div class="mb-3">
            <label>Tanggal</label>
            <input type="date" name="tanggal" class="form-control" value="{{ $attendance->tanggal }}" required>
        </div>

        <div class="mb-3">
            <label>Jam Masuk</label>
            <input type="time" name="jam_masuk" class="form-control" value="{{ $attendance->jam_masuk }}">
        </div>

        <div class="mb-3">
            <label>Jam Pulang</label>
            <input type="time" name="jam_pulang" class="form-control" value="{{ $attendance->jam_pulang }}">
        </div>

        <button type="submit" class="btn btn-warning">Update</button>
    </form>
</div>
@endsection
