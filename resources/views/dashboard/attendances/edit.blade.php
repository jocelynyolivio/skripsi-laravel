@extends('dashboard.layouts.main')

@section('container')
<div class="container">
    <h2>Edit Data Presensi</h2>

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
                @foreach($users as $user)
                <option value="{{ $user->id }}" {{ $attendance->no_id == $user->id ? 'selected' : '' }}>
                    {{ $user->id }} - {{ $user->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="nama" class="form-control" value="{{ $attendance->nama }}" readonly>
        </div>

        <div class="mb-3">
            <label>Tanggal</label>
            <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', $attendance->tanggal ? \Carbon\Carbon::parse($attendance->tanggal)->format('Y-m-d') : '') }}" required>
        </div>

        <div class="mb-3">
            <label>Jam Masuk</label>
            <input type="time" name="jam_masuk" class="form-control" value="{{ old('jam_masuk', $attendance->jam_masuk ? \Carbon\Carbon::parse($attendance->jam_masuk)->format('H:i') : '') }}" required>
        </div>

        <div class="mb-3">
            <label>Jam Pulang</label>
            <input type="time" name="jam_pulang" class="form-control" value="{{ old('jam_pulang', $attendance->jam_pulang ? \Carbon\Carbon::parse($attendance->jam_pulang)->format('H:i') : '') }}" required>
        </div>


        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>

<script>
    document.querySelector("select[name='no_id']").addEventListener("change", function() {
        let selectedUser = this.options[this.selectedIndex].text.split(" - ")[1];
        document.querySelector("input[name='nama']").value = selectedUser;
    });
</script>
@endsection