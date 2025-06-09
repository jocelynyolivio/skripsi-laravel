@extends('dashboard.layouts.main')
@section('breadcrumbs')
    @include('dashboard.layouts.breadcrumbs', [
        'customBreadcrumbs' => [
            ['text' => 'Procedure Materials', 'url' => route('dashboard.procedure_materials.index')],
            ['text' => 'Edit Procedure Materials']
        ]
    ])
@endsection
@section('container')
<div class="container mt-5 col-md-6">
    <h1>Edit Procedure Material</h1>

    <form id="editProcedureMaterialForm" action="{{ route('dashboard.procedure_materials.update', $procedureMaterial->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="procedure_id">Procedure</label>
            <select name="procedure_id" id="procedure_id" class="form-control" required>
                @foreach ($procedures as $procedure)
                    <option value="{{ $procedure->id }}" {{ $procedureMaterial->procedure_id == $procedure->id ? 'selected' : '' }}>
                        {{ $procedure->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="dental_material_id">Dental Material</label>
            <select name="dental_material_id" id="dental_material_id" class="form-control" required>
                @foreach ($dentalMaterials as $material)
                    <option value="{{ $material->id }}" {{ $procedureMaterial->dental_material_id == $material->id ? 'selected' : '' }}>
                        {{ $material->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="quantity">Quantity</label>
            <input type="number" name="quantity" id="quantity" class="form-control" value="{{ $procedureMaterial->quantity }}" required min="1">
        </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('dashboard.procedure_materials.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
<script>
    document.getElementById('editProcedureMaterialForm').addEventListener('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Update Confirmation',
            text: "Are you sure to update this Procedure Material data?",
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
