@extends('dashboard.layouts.main')
@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['url' => route('dashboard.attendances.index'), 'text' => 'Attendances'],
            ['text' => 'Edit Attendance']
        ]
    ])
@endsection
@section('container')
<div class="container mt-5 col-md-6">
    <h3 class="text-center">Edit Attendance</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('dashboard.attendances.update', $attendance->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>No ID (User)</label>
            <select name="no_id" class="form-control" required>
                <option value="">Pilih User</option>
                @foreach($users as $user)
                <option value="{{ $user->id }}" {{ $user->id == $attendance->no_id ? 'selected' : '' }}>{{ $user->id }} - {{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
    <label>Tanggal</label>
    <input type="date" name="tanggal" class="form-control" value="{{ $attendance->tanggal->format('Y-m-d') }}" required>
</div>

<div class="mb-3">
    <label>Jam Masuk</label>
    <input type="time" name="jam_masuk" class="form-control" value="{{ \Carbon\Carbon::parse($attendance->jam_masuk)->format('H:i') }}" required>
</div>

<div class="mb-3">
    <label>Jam Pulang</label>
    <input type="time" name="jam_pulang" class="form-control" value="{{ \Carbon\Carbon::parse($attendance->jam_pulang)->format('H:i') }}" required>
</div>


        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="{{ route('dashboard.attendances.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
