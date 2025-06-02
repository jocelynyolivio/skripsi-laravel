@extends('dashboard.layouts.main')

@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['url' => route('dashboard.holidays.index'), 'text' => 'Holidays'],
            ['text' => 'Edit Holiday']  {{-- Judul breadcrumb disesuaikan --}}
        ]
    ])
@endsection

@section('container')
<div class="container mt-5 col-md-6">
    <h2>Edit Holiday</h2> {{-- Judul halaman --}}

    {{-- Menampilkan pesan error jika ada dari session --}}
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <form action="{{ route('dashboard.holidays.update', $holiday->id) }}" method="POST">
        @csrf
        @method('PUT') {{-- Metode untuk update --}}

        <div class="form-group">
            <label for="tanggal">Date</label> {{-- Tambahkan atribut 'for' untuk aksesibilitas --}}
            <input type="date" id="tanggal" name="tanggal" class="form-control" value="{{ old('tanggal', $holiday->tanggal) }}" required>
            {{-- Menggunakan old() untuk mempertahankan input jika ada validation error --}}
        </div>

        <div class="form-group mt-3"> {{-- Menambahkan margin-top untuk spasi antar form-group --}}
            <label for="keterangan">Description</label> {{-- Tambahkan atribut 'for' --}}
            <input type="text" id="keterangan" name="keterangan" class="form-control" value="{{ old('keterangan', $holiday->keterangan) }}" required>
        </div>

        {{-- Menggunakan margin-top pada tombol untuk spasi, sebagai alternatif <br> --}}
        <button type="submit" class="btn btn-success mt-3">Update</button>
    </form>
</div>
@endsection