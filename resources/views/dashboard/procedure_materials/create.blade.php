@extends('dashboard.layouts.main')

@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['text' => 'Procedure Materials', 'url' => route('dashboard.procedure_materials.index')],
            ['text' => 'Create Procedure Material']
        ]
    ])
@endsection

@section('container')
<div class="container mt-5 col-md-6">
    <h2>Create Procedure Material</h2>
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

    <form id="createProcedureMaterialForm" action="{{ route('dashboard.procedure_materials.store') }}" method="POST">
        @csrf

        <!-- Procedure -->
        <div class="mb-3">
            <label for="procedure_id" class="form-label">Procedure <span class="text-danger">*</span></label>
            <select name="procedure_id" id="procedure_id" class="form-select @error('procedure_id') is-invalid @enderror" required>
                <option value="" disabled {{ old('procedure_id') ? '' : 'selected' }}>Select Procedure</option>
                @foreach ($procedures as $procedure)
                    <option value="{{ $procedure->id }}" {{ old('procedure_id') == $procedure->id ? 'selected' : '' }}>
                        {{ $procedure->name }}
                    </option>
                @endforeach
            </select>
            @error('procedure_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Dental Material -->
        <div class="mb-3">
            <label for="dental_material_id" class="form-label">Dental Material <span class="text-danger">*</span></label>
            <select name="dental_material_id" id="dental_material_id" class="form-select @error('dental_material_id') is-invalid @enderror" required>
                <option value="" disabled {{ old('dental_material_id') ? '' : 'selected' }}>Select Dental Material</option>
                @foreach ($dentalMaterials as $material)
                    <option value="{{ $material->id }}" {{ old('dental_material_id') == $material->id ? 'selected' : '' }}>
                        {{ $material->name }}
                    </option>
                @endforeach
            </select>
            @error('dental_material_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Quantity -->
        <div class="mb-3">
            <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
            <input type="number" name="quantity" id="quantity" class="form-control @error('quantity') is-invalid @enderror" min="1" value="{{ old('quantity') }}" required>
            @error('quantity')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="{{ route('dashboard.procedure_materials.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
<script>
    document.getElementById('createProcedureMaterialForm').addEventListener('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Confirm Procedure Material',
            text: "Are you sure you want to create this procedure material?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, sure!'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
</script>
@endsection
