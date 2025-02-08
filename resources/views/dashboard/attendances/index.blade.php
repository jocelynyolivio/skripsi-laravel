@extends('dashboard.layouts.main')

@section('container')
<div class="container">
    <h2>Data Presensi</h2>
    <a href="{{ route('dashboard.attendances.create') }}" class="btn btn-primary">Tambah Data</a>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($attendances->isEmpty())
        <p class="text-center mt-3">Belum ada data presensi.</p>
    @else
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>No ID</th>
                    <th>Nama</th>
                    <th>Tanggal</th>
                    <th>Jam Masuk</th>
                    <th>Jam Pulang</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendances as $attendance)
                <tr>
                    <td>{{ $attendance->id }}</td>
                    <td>{{ $attendance->no_id }}</td>
                    <td>{{ $attendance->nama }}</td>
                    <td>{{ $attendance->tanggal }}</td>
                    <td>{{ $attendance->jam_masuk }}</td>
                    <td>{{ $attendance->jam_pulang }}</td>
                    <td>
                        <a href="{{ route('dashboard.attendances.show', $attendance->id) }}" class="btn btn-info btn-sm">Lihat</a>
                        <a href="{{ route('dashboard.attendances.edit', $attendance->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('dashboard.attendances.destroy', $attendance->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
