@extends('dashboard.layouts.main')

@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['url' => route('dashboard.procedures.index'), 'text' => 'Procedures'],
            ['text' => 'Edit Procedure']
        ]
    ])
@endsection

@section('container')
<div class="container mt-5 col-md-6"> {{-- Kontainer utama, tanpa card di dalamnya --}}
    <h2>Edit Procedure</h2>

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

    <form id="editProcedureForm" action="{{ route('dashboard.procedures.update', $procedure->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Item Code (Readonly, spesifik untuk form edit jika diperlukan) --}}
        <div class="mb-3">
            <label for="item_code" class="form-label">Item Code</label>
            <input type="text" id="item_code" name="item_code" class="form-control" value="{{ $procedure->item_code }}" readonly>
            {{-- Jika item_code tidak boleh diubah oleh user, atribut readonly sudah tepat. --}}
            {{-- Jika item_code juga tidak relevan untuk ditampilkan, field ini bisa dihilangkan. --}}
        </div>

        <div class="mb-3">
            <label for="name" class="form-label">Procedure Name</label>
            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $procedure->name) }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="procedure_type_id" class="form-label">Procedure Type</label>
            <select id="procedure_type_id" name="procedure_type_id" class="form-select @error('procedure_type_id') is-invalid @enderror">
                <option value="">- Select Type -</option>
                @foreach($types as $type)
                    <option value="{{ $type->id }}" {{ old('procedure_type_id', $procedure->procedure_type_id) == $type->id ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
            @error('procedure_type_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $procedure->description) }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="requires_tooth" class="form-label">Requires Tooth?</label>
            <select id="requires_tooth" name="requires_tooth" class="form-select @error('requires_tooth') is-invalid @enderror">
                <option value="1" {{ old('requires_tooth', $procedure->requires_tooth ? '1' : '0') == '1' ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ old('requires_tooth', $procedure->requires_tooth ? '1' : '0') == '0' ? 'selected' : '' }}>No</option>
            </select>
            @error('requires_tooth')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <button type="submit" class="btn btn-primary">Update Procedure</button>
            <a href="{{ route('dashboard.procedures.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
<script>
    document.getElementById('editProcedureForm').addEventListener('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Update Confirmation',
            text: "Are you sure to update this procedure data?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, sure!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
</script>
@endsection