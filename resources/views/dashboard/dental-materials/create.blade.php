{{-- resources/views/dashboard/dental_materials/create.blade.php --}}
@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5 col-md-6">
    <h3 class="text-center">Add New Dental Material</h3>

    <form action="{{ route('dashboard.dental-materials.store') }}" method="POST">
        @csrf

        <!-- Material Name -->
        <div class="mb-3">
            <label for="name" class="form-label">Material Name</label>
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
            <label for="unit" class="form-label">Unit</label>
            <select id="unit" name="unit" class="form-select @error('unit') is-invalid @enderror">
                <option value="" disabled selected>Select Unit</option>

                <!-- Unit Berat -->
                <option value="mg" {{ old('unit') == 'mg' ? 'selected' : '' }}>mg (Miligram)</option>
                <option value="g" {{ old('unit') == 'g' ? 'selected' : '' }}>g (Gram)</option>
                <option value="kg" {{ old('unit') == 'kg' ? 'selected' : '' }}>kg (Kilogram)</option>

                <!-- Unit Volume -->
                <option value="ml" {{ old('unit') == 'ml' ? 'selected' : '' }}>mL (Mililiter)</option>
                <option value="l" {{ old('unit') == 'l' ? 'selected' : '' }}>L (Liter)</option>

                <!-- Unit Panjang -->
                <option value="mm" {{ old('unit') == 'mm' ? 'selected' : '' }}>mm (Milimeter)</option>
                <option value="cm" {{ old('unit') == 'cm' ? 'selected' : '' }}>cm (Centimeter)</option>
                <option value="m" {{ old('unit') == 'm' ? 'selected' : '' }}>m (Meter)</option>

                <!-- Unit Jumlah -->
                <option value="pcs" {{ old('unit') == 'pcs' ? 'selected' : '' }}>pcs (Pieces)</option>
                <option value="box" {{ old('unit') == 'box' ? 'selected' : '' }}>box (Box)</option>
                <option value="pack" {{ old('unit') == 'pack' ? 'selected' : '' }}>pack (Pack)</option>
                <option value="syringe" {{ old('unit') == 'syringe' ? 'selected' : '' }}>syringe (Syringe)</option>
                <option value="cartridge" {{ old('unit') == 'cartridge' ? 'selected' : '' }}>cartridge (Cartridge)</option>

                <!-- Unit Konsentrasi -->
                <option value="percent" {{ old('unit') == 'percent' ? 'selected' : '' }}>% (Persentase)</option>
                <option value="ppm" {{ old('unit') == 'ppm' ? 'selected' : '' }}>ppm (Parts per million)</option>
                <option value="molar" {{ old('unit') == 'molar' ? 'selected' : '' }}>M (Molar)</option>

                <!-- Unit Tekanan -->
                <option value="bar" {{ old('unit') == 'bar' ? 'selected' : '' }}>bar (Bar)</option>
                <option value="psi" {{ old('unit') == 'psi' ? 'selected' : '' }}>psi (Pounds per square inch)</option>
            </select>
            @error('unit')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>
@endsection
