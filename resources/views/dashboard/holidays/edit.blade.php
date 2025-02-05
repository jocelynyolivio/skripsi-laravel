@extends('dashboard.layouts.main')

@section('container')
<div class="container">
    <h2>Edit Hari Libur</h2>
    <form action="{{ route('dashboard.holidays.update', $holiday->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>Tanggal</label>
            <input type="date" name="tanggal" class="form-control" value="{{ $holiday->tanggal }}" required>
        </div>
        <div class="form-group">
            <label>Keterangan</label>
            <input type="text" name="keterangan" class="form-control" value="{{ $holiday->keterangan }}" required>
        </div>
        <button type="submit" class="btn btn-success">Update</button>
    </form>
</div>
@endsection
