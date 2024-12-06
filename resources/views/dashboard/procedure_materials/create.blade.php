@extends('dashboard.layouts.main')

@section('container')
    <h1>Create Procedure Material</h1>

    <form action="{{ route('dashboard.procedure_materials.store') }}" method="POST">
        @csrf

        <div class="form-group mb-3">
            <label for="procedure_id" class="form-label">Procedure</label>
            <select name="procedure_id" id="procedure_id" class="form-control" required>
                <option value="" disabled selected>Select Procedure</option>
                @foreach ($procedures as $procedure)
                    <option value="{{ $procedure->id }}">{{ $procedure->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="dental_material_id" class="form-label">Dental Material</label>
            <select name="dental_material_id" id="dental_material_id" class="form-control" required>
                <option value="" disabled selected>Select Dental Material</option>
                @foreach ($dentalMaterials as $material)
                    <option value="{{ $material->id }}">{{ $material->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" name="quantity" id="quantity" class="form-control" required min="1">
        </div>

        <button type="submit" class="btn btn-primary">Save</button>
    </form>
@endsection

