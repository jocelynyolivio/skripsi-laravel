@extends('dashboard.layouts.main')
@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['text' => 'Dental Materials', 'url' => route('dashboard.dental-materials.index')],
            ['text' => 'Create Dental Materials']
        ]
    ])
@endsection
@section('container')
<div class="container mt-5 col-md-6">
    <h2>Create New Dental Material</h2>

    <form id="createDentalMaterialForm" action="{{ route('dashboard.dental-materials.store') }}" method="POST">
        @csrf

        <!-- Material Name -->
        <div class="mb-3">
            <label for="name" class="form-label">Material Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Description -->
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ old('description') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Unit Selection -->
        <div class="mb-3">
            <label for="unit_type" class="form-label">Unit <span class="text-danger">*</span></label>
            <select id="unit_type" name="unit_type" class="form-select @error('unit_type') is-invalid @enderror" required>
                <option value="" disabled selected>Select Unit</option>

                <!-- Unit Berat -->
                <option value="mg" {{ old('unit_type') == 'mg' ? 'selected' : '' }}>mg (Miligram)</option>
                <option value="g" {{ old('unit_type') == 'g' ? 'selected' : '' }}>g (Gram)</option>
                <option value="kg" {{ old('unit_type') == 'kg' ? 'selected' : '' }}>kg (Kilogram)</option>

                <!-- Unit Volume -->
                <option value="ml" {{ old('unit_type') == 'ml' ? 'selected' : '' }}>mL (Mililiter)</option>
                <option value="l" {{ old('unit_type') == 'l' ? 'selected' : '' }}>L (Liter)</option>

                <!-- Unit Panjang -->
                <option value="mm" {{ old('unit_type') == 'mm' ? 'selected' : '' }}>mm (Milimeter)</option>
                <option value="cm" {{ old('unit_type') == 'cm' ? 'selected' : '' }}>cm (Centimeter)</option>
                <option value="m" {{ old('unit_type') == 'm' ? 'selected' : '' }}>m (Meter)</option>

                <!-- Unit Jumlah -->
                <option value="pcs" {{ old('unit_type') == 'pcs' ? 'selected' : '' }}>pcs (Pieces)</option>
                <option value="box" {{ old('unit_type') == 'box' ? 'selected' : '' }}>box (Box)</option>
                <option value="pack" {{ old('unit_type') == 'pack' ? 'selected' : '' }}>pack (Pack)</option>
                <option value="syringe" {{ old('unit_type') == 'syringe' ? 'selected' : '' }}>syringe (Syringe)</option>
                <option value="cartridge" {{ old('unit_type') == 'cartridge' ? 'selected' : '' }}>cartridge (Cartridge)</option>

                <!-- Unit Konsentrasi -->
                <option value="percent" {{ old('unit_type') == 'percent' ? 'selected' : '' }}>% (Persentase)</option>
                <option value="ppm" {{ old('unit_type') == 'ppm' ? 'selected' : '' }}>ppm (Parts per million)</option>
                <option value="molar" {{ old('unit_type') == 'molar' ? 'selected' : '' }}>M (Molar)</option>

                <!-- Unit Tekanan -->
                <option value="bar" {{ old('unit_type') == 'bar' ? 'selected' : '' }}>bar (Bar)</option>
                <option value="psi" {{ old('unit_type') == 'psi' ? 'selected' : '' }}>psi (Pounds per square inch)</option>
            </select>
            @error('unit_type')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
        <button type="submit" class="btn btn-primary">Save</button>
            <a href="{{ route('dashboard.dental-materials.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
<script>
    document.getElementById('createDentalMaterialForm').addEventListener('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Confirm Account',
            text: "Are you sure you want to create this dental material?",
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
