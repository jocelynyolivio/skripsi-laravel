@extends('dashboard.layouts.main')

@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['url' => route('dashboard.procedures.index'), 'text' => 'Procedures'],
            ['text' => 'Create Procedure']
        ]
    ])
@endsection

@section('container')
<div class="container mt-5 col-md-6">
    <h2>Create Procedure</h2>

    {{-- Menampilkan pesan sukses atau error dari session --}}
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

    {{-- Menampilkan error validasi umum --}}
    @if ($errors->any())
        <div class="alert alert-danger pb-0">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('dashboard.procedures.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Procedure Name</label>
            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="procedure_type_id" class="form-label">Procedure Type</label>
            <select id="procedure_type_id" name="procedure_type_id" class="form-select @error('procedure_type_id') is-invalid @enderror">
                <option value="">- Select Type -</option>
                @foreach($types as $type)
                    <option value="{{ $type->id }}" {{ old('procedure_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                @endforeach
            </select>
            @error('procedure_type_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="requires_tooth" class="form-label">Requires Tooth?</label>
            <select id="requires_tooth" name="requires_tooth" class="form-select @error('requires_tooth') is-invalid @enderror">
                {{-- Default ke 'Yes' (1) jika tidak ada input lama (old), atau sesuai input lama --}}
                <option value="1" {{ old('requires_tooth', '1') == '1' ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ old('requires_tooth', '1') == '0' ? 'selected' : '' }}>No</option>
            </select>
            @error('requires_tooth')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mt-4"> {{-- Memberi jarak atas untuk tombol --}}
            <button type="submit" class="btn btn-success">Simpan</button>
            <a href="{{ route('dashboard.procedures.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection