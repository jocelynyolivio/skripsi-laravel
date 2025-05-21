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
    <h3 class="text-center">Create Procedure Material</h3>

    <form action="{{ route('dashboard.procedure_materials.store') }}" method="POST">
        @csrf

        <!-- Procedure -->
        <div class="mb-3">
            <label for="procedure_id" class="form-label">Procedure</label>
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
            <label for="dental_material_id" class="form-label">Dental Material</label>
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
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" name="quantity" id="quantity" class="form-control @error('quantity') is-invalid @enderror" min="1" value="{{ old('quantity') }}" required>
            @error('quantity')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>
@endsection
