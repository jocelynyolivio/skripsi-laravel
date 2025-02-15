@extends('dashboard.layouts.main')

@section('container')
<div class="container mt-5 col-md-6">
    <h1>Edit Procedure Material</h1>

    <form action="{{ route('dashboard.procedure_materials.update', $procedureMaterial->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group mb-3">
            <label for="procedure_id">Procedure</label>
            <select name="procedure_id" id="procedure_id" class="form-control" required>
                @foreach ($procedures as $procedure)
                    <option value="{{ $procedure->id }}" {{ $procedureMaterial->procedure_id == $procedure->id ? 'selected' : '' }}>
                        {{ $procedure->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="dental_material_id">Dental Material</label>
            <select name="dental_material_id" id="dental_material_id" class="form-control" required>
                @foreach ($dentalMaterials as $material)
                    <option value="{{ $material->id }}" {{ $procedureMaterial->dental_material_id == $material->id ? 'selected' : '' }}>
                        {{ $material->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="quantity">Quantity</label>
            <input type="number" name="quantity" id="quantity" class="form-control" value="{{ $procedureMaterial->quantity }}" required min="1">
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
