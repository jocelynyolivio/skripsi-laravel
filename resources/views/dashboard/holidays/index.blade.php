@extends('dashboard.layouts.main')

@section('container')
<div class="container">
    <h2>Daftar Hari Libur</h2>
    <a href="{{ route('dashboard.holidays.create') }}" class="btn btn-primary">Tambah Libur</a>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($holidays as $holiday)
            <tr>
                <td>{{ $holiday->tanggal }}</td>
                <td>{{ $holiday->keterangan }}</td>
                <td>
                    <a href="{{ route('dashboard.holidays.edit', $holiday->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('dashboard.holidays.destroy', $holiday->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus libur ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
