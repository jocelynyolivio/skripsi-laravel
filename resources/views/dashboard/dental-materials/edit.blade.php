@extends('dashboard.layouts.main')
@section('breadcrumbs')
@include('dashboard.layouts.breadcrumbs', [
'customBreadcrumbs' => [
['text' => 'Dental Materials', 'url' => route('dashboard.dental-materials.index')],
['text' => 'Edit Dental Materials']
]
])
@endsection
@section('container')
<div class="container mt-5 col-md-6">
    <h2>Edit Dental Material</h2>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form id="editDentalMaterialForm" action="{{ route('dashboard.dental-materials.update', $dentalMaterial->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Material Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $dentalMaterial->name }}" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description">{{ $dentalMaterial->description }}</textarea>
        </div>

        <div class="mb-3">
            <label for="unit_type" class="form-label">Unit</label>
            <select id="unit_type" name="unit_type" class="form-select @error('unit_type') is-invalid @enderror">
                <option value="" disabled>Select Unit</option>

                {{-- Logika diubah menjadi: old('field', $model->field) --}}

                <option value="mg" {{ old('unit_type', $dentalMaterial->unit_type) == 'mg' ? 'selected' : '' }}>mg (Miligram)</option>
                <option value="g" {{ old('unit_type', $dentalMaterial->unit_type) == 'g' ? 'selected' : '' }}>g (Gram)</option>
                <option value="kg" {{ old('unit_type', $dentalMaterial->unit_type) == 'kg' ? 'selected' : '' }}>kg (Kilogram)</option>

                <option value="ml" {{ old('unit_type', $dentalMaterial->unit_type) == 'ml' ? 'selected' : '' }}>mL (Mililiter)</option>
                <option value="l" {{ old('unit_type', $dentalMaterial->unit_type) == 'l' ? 'selected' : '' }}>L (Liter)</option>

                <option value="mm" {{ old('unit_type', $dentalMaterial->unit_type) == 'mm' ? 'selected' : '' }}>mm (Milimeter)</option>
                <option value="cm" {{ old('unit_type', $dentalMaterial->unit_type) == 'cm' ? 'selected' : '' }}>cm (Centimeter)</option>
                <option value="m" {{ old('unit_type', $dentalMaterial->unit_type) == 'm' ? 'selected' : '' }}>m (Meter)</option>

                <option value="pcs" {{ old('unit_type', $dentalMaterial->unit_type) == 'pcs' ? 'selected' : '' }}>pcs (Pieces)</option>
                <option value="box" {{ old('unit_type', $dentalMaterial->unit_type) == 'box' ? 'selected' : '' }}>box (Box)</option>
                <option value="pack" {{ old('unit_type', $dentalMaterial->unit_type) == 'pack' ? 'selected' : '' }}>pack (Pack)</option>
                <option value="syringe" {{ old('unit_type', $dentalMaterial->unit_type) == 'syringe' ? 'selected' : '' }}>syringe (Syringe)</option>
                <option value="cartridge" {{ old('unit_type', $dentalMaterial->unit_type) == 'cartridge' ? 'selected' : '' }}>cartridge (Cartridge)</option>

                <option value="percent" {{ old('unit_type', $dentalMaterial->unit_type) == 'percent' ? 'selected' : '' }}>% (Persentase)</option>
                <option value="ppm" {{ old('unit_type', $dentalMaterial->unit_type) == 'ppm' ? 'selected' : '' }}>ppm (Parts per million)</option>
                <option value="molar" {{ old('unit_type', $dentalMaterial->unit_type) == 'molar' ? 'selected' : '' }}>M (Molar)</option>

                <option value="bar" {{ old('unit_type', $dentalMaterial->unit_type) == 'bar' ? 'selected' : '' }}>bar (Bar)</option>
                <option value="psi" {{ old('unit_type', $dentalMaterial->unit_type) == 'psi' ? 'selected' : '' }}>psi (Pounds per square inch)</option>
            </select>
            @error('unit_type')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('dashboard.dental-materials.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
<script>
    document.getElementById('editDentalMaterialForm').addEventListener('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Update Confirmation',
            text: "Are you sure to update this Dental Material data?",
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