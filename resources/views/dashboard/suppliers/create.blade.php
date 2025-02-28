@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5 col-md-6">
    <h2>Tambah Supplier</h2>
    <form action="{{ route('dashboard.suppliers.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Nama</label>
            <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required>
            @error('name')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label>Alamat</label>
            <input type="text" name="alamat" class="form-control" value="{{ old('alamat') }}">
            @error('alamat')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label>Nomor Telepon</label>
            <input type="text" name="nomor_telepon" class="form-control" value="{{ old('nomor_telepon') }}">
            @error('nomor_telepon')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
            @error('email')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <button type="submit" class="btn btn-success">Simpan</button>
    </form>
</div>
@endsection
