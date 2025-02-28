@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5">
    <h3 class="text-center">Daftar Supplier</h3>

    <a href="{{ route('dashboard.suppliers.create') }}" class="btn btn-success mb-3">Tambah Supplier</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>Nomor Telepon</th>
                <th>Email</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($suppliers as $index => $supplier)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $supplier->nama }}</td>
                    <td>{{ $supplier->alamat }}</td>
                    <td>{{ $supplier->nomor_telepon }}</td>
                    <td>{{ $supplier->email }}</td>
                    <td>
                        <a href="{{ route('dashboard.suppliers.edit', $supplier->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('dashboard.suppliers.destroy', $supplier->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
